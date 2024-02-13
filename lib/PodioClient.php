<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\TransferStats;

class PodioClient
{
    public $oauth;
    /** @var bool|string */
    protected $debug = false;
    /**
     * Only created/used if debug is enabled and set to 'file'.
     *
     * @var ?PodioLogger
     */
    public $logger;
    public $session_manager;
    /** @var ?PodioResponse */
    public $last_response;
    public $auth_type;
    /** @var \GuzzleHttp\Client */
    public $http_client;
    protected $url;
    protected $client_id;
    protected $client_secret;
    /** @var \Psr\Http\Message\ResponseInterface */
    private $last_http_response;

    public const VERSION = '7.0.0';

    public const GET = 'GET';
    public const POST = 'POST';
    public const PUT = 'PUT';
    public const DELETE = 'DELETE';

    public function __construct($client_id, $client_secret, $options = array('session_manager' => null, 'curl_options' => []))
    {
        // Setup client info
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;

        $this->url = empty($options['api_url']) ? 'https://api.podio.com:443' : $options['api_url'];
        $client_config = [
            'base_uri' => $this->url,

            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'User-Agent' => 'Podio PHP Client/' . self::VERSION . '-guzzle'
            ]
        ];
        if ($options && !empty($options['curl_options'])) {
            $client_config['curl'] = $options['curl_options'];
        }
        if (class_exists('\\Composer\\CaBundle\\CaBundle')) {
            /** @noinspection PhpFullyQualifiedNameUsageInspection */
            $client_config[RequestOptions::VERIFY] = \Composer\CaBundle\CaBundle::getSystemCaRootBundlePath();
        }
        $this->http_client = new Client($client_config);

        $this->session_manager = null;
        if ($options && !empty($options['session_manager'])) {
            if (is_string($options['session_manager']) && class_exists($options['session_manager'])) {
                $this->session_manager = new $options['session_manager']();
            } elseif (is_object($options['session_manager']) && method_exists($options['session_manager'], 'get') && method_exists($options['session_manager'], 'set')) {
                $this->session_manager = $options['session_manager'];
            }
            if ($this->session_manager) {
                $this->oauth = $this->session_manager->get();
            }
        }
    }

    public function __destruct()
    {
        $this->shutdown();
    }

    public function authenticate_with_app($app_id, $app_token): bool
    {
        return $this->authenticate('app', ['app_id' => $app_id, 'app_token' => $app_token]);
    }

    public function authenticate_with_password($username, $password): bool
    {
        return $this->authenticate('password', ['username' => $username, 'password' => $password]);
    }

    public function authenticate_with_authorization_code($authorization_code, $redirect_uri): bool
    {
        return $this->authenticate('authorization_code', ['code' => $authorization_code, 'redirect_uri' => $redirect_uri]);
    }

    public function refresh_access_token(): bool
    {
        return $this->authenticate('refresh_token', ['refresh_token' => $this->oauth->refresh_token]);
    }

    public function authenticate($grant_type, $attributes): bool
    {
        $data = [];
        $auth_type = ['type' => $grant_type];

        switch ($grant_type) {
            case 'password':
                $data['grant_type'] = 'password';
                $data['username'] = $attributes['username'];
                $data['password'] = $attributes['password'];

                $auth_type['identifier'] = $attributes['username'];
                break;
            case 'refresh_token':
                $data['grant_type'] = 'refresh_token';
                $data['refresh_token'] = $attributes['refresh_token'];
                break;
            case 'authorization_code':
                $data['grant_type'] = 'authorization_code';
                $data['code'] = $attributes['code'];
                $data['redirect_uri'] = $attributes['redirect_uri'];
                break;
            case 'app':
                $data['grant_type'] = 'app';
                $data['app_id'] = $attributes['app_id'];
                $data['app_token'] = $attributes['app_token'];

                $auth_type['identifier'] = $attributes['app_id'];
                break;
            default:
                break;
        }

        $request_data = array_merge($data, ['client_id' => $this->client_id, 'client_secret' => $this->client_secret]);
        if ($response = $this->request(self::POST, '/oauth/token', $request_data, ['oauth_request' => true])) {
            $body = $response->json_body();
            $this->oauth = new PodioOAuth($body['access_token'], $body['refresh_token'], $body['expires_in'], $body['ref'], $body['scope']);

            // Don't touch auth_type if we are refreshing automatically as it'll be reset to null
            if ($grant_type !== 'refresh_token') {
                $this->auth_type = $auth_type;
            }

            if ($this->session_manager) {
                $this->session_manager->set($this->oauth, $this->auth_type);
            }

            return true;
        }
        return false;
    }

    public function clear_authentication()
    {
        $this->oauth = new PodioOAuth();

        if ($this->session_manager) {
            $this->session_manager->set($this->oauth, $this->auth_type);
        }
    }

    public function authorize_url($redirect_uri, $scope): string
    {
        $parsed_url = parse_url($this->url);
        $host = str_replace('api.', '', $parsed_url['host']);
        return 'https://' . $host . '/oauth/authorize?response_type=code&client_id=' . $this->client_id . '&redirect_uri=' . rawurlencode($redirect_uri) . '&scope=' . rawurlencode($scope);
    }

    public function is_authenticated(): bool
    {
        return $this->oauth && $this->oauth->access_token;
    }

    /**
     * @throws PodioBadRequestError
     * @throws PodioConflictError
     * @throws PodioRateLimitError
     * @throws PodioUnavailableError
     * @throws PodioGoneError
     * @throws PodioDataIntegrityError
     * @throws PodioForbiddenError
     * @throws PodioNotFoundError
     * @throws PodioError
     * @throws PodioInvalidGrantError
     * @throws PodioAuthorizationError
     * @throws PodioConnectionError
     * @throws PodioServerError
     * @throws Exception when client is not setup
     */
    public function request($method, $url, $attributes = [], $options = [])
    {
        $original_url = $url;
        $encoded_attributes = null;

        if (is_object($attributes) && substr(get_class($attributes), 0, 5) == 'Podio') {
            $attributes = $attributes->as_json(false);
        }

        if (!is_array($attributes) && !is_object($attributes)) {
            throw new PodioDataIntegrityError('Attributes must be an array');
        }

        $request = new Request($method, $url);
        switch ($method) {
            case self::DELETE:
            case self::GET:
                $request = $request->withHeader('Content-type', 'application/x-www-form-urlencoded');
                $request = $request->withHeader('Content-length', '0');

                $separator = strpos($url, '?') ? '&' : '?';
                if ($attributes) {
                    $query = $this->encode_attributes($attributes);
                    $request = $request->withUri(new Uri($url . $separator . $query));
                }
                break;
            case self::POST:
                if (!empty($options['upload'])) {
                    $request = $request->withBody(new MultipartStream([
                        [
                            'name' => 'source',
                            'contents' => fopen($attributes['filepath'], 'r'),
                            'filename' => $attributes['filename']
                        ], [
                            'name' => 'filename',
                            'contents' => $attributes['filename']
                        ]
                    ]));
                } elseif (empty($options['oauth_request'])) {
                    // application/json
                    $encoded_attributes = json_encode($attributes);
                    $request = $request->withBody(GuzzleHttp\Psr7\Utils::streamFor($encoded_attributes));
                    $request = $request->withHeader('Content-type', 'application/json');
                } else {
                    // x-www-form-urlencoded
                    $encoded_attributes = $this->encode_attributes($attributes);
                    $request = $request->withBody(GuzzleHttp\Psr7\Utils::streamFor($encoded_attributes));
                    $request = $request->withHeader('Content-type', 'application/x-www-form-urlencoded');
                }
                break;
            case self::PUT:
                $encoded_attributes = json_encode($attributes);
                $request = $request->withBody(GuzzleHttp\Psr7\Utils::streamFor($encoded_attributes));
                $request = $request->withHeader('Content-type', 'application/json');
                break;
        }

        // Add access token to request
        if (isset($this->oauth) && !empty($this->oauth->access_token) && !(isset($options['oauth_request']) && $options['oauth_request'] == true)) {
            $token = $this->oauth->access_token;
            $request = $request->withHeader('Authorization', "OAuth2 {$token}");
        }

        // File downloads can be of any type
        if (!empty($options['file_download'])) {
            $request = $request->withHeader('Accept', '*/*');
        }

        $response = new PodioResponse();

        try {
            $transferTime = 0;
            /** \Psr\Http\Message\ResponseInterface */
            $http_response = $this->http_client->send($request, [
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::ON_STATS => function (TransferStats $stats) use (&$transferTime) {
                    $transferTime = $stats->getTransferTime();
                }
            ]);
            $response->status = $http_response->getStatusCode();
            $response->headers = array_map(function ($values) {
                return implode(', ', $values);
            }, $http_response->getHeaders());
            $this->last_http_response = $http_response;
            if (!isset($options['return_raw_as_resource_only']) || $options['return_raw_as_resource_only'] != true) {
                $response->body = $http_response->getBody()->getContents();
            }
            $this->last_response = $response;
        } catch (RequestException $requestException) {
            throw new PodioConnectionError('Connection to Podio API failed: [' . get_class($requestException) . '] ' . $requestException->getMessage(), $requestException->getCode());
        } catch (GuzzleException $e) { // this generally should not happen as RequestOptions::HTTP_ERRORS is set to `false`
            throw new PodioConnectionError('Connection to Podio API failed: [' . get_class($e) . '] ' . $e->getMessage(), $e->getCode());
        }

        if (!isset($options['oauth_request'])) {
            $this->log_request($method, $url, $encoded_attributes, $response, $transferTime);
        }

        switch ($response->status) {
            case 200:
            case 201:
            case 204:
                if (isset($options['return_raw_as_resource_only']) && $options['return_raw_as_resource_only'] === true) {
                    return $http_response->getBody();
                }
                return $response;
            case 400:
                // invalid_grant_error or bad_request_error
                $body_str = $response->body ?? $http_response->getBody()->getContents();
                $body = json_decode($body_str, true);
                if (strstr($body['error'], 'invalid_grant')) {
                    // Reset access token & refresh_token
                    $this->clear_authentication();
                    throw new PodioInvalidGrantError($body_str, $response->status, $url);
                } else {
                    throw new PodioBadRequestError($body_str, $response->status, $url);
                }
            // no break
            case 401:
                $body_str = $response->body ?? $http_response->getBody()->getContents();
                $body = json_decode($body_str, true);
                if (strstr($body['error_description'], 'expired_token') || strstr($body['error'], 'invalid_token')) {
                    if ($this->oauth->refresh_token) {
                        // Access token is expired. Try to refresh it.
                        if ($this->refresh_access_token()) {
                            // Try the original request again.
                            return $this->request($method, $original_url, $attributes);
                        } else {
                            $this->clear_authentication();
                            throw new PodioAuthorizationError($body_str, $response->status, $url);
                        }
                    } else {
                        // We have tried in vain to get a new access token. Log the user out.
                        $this->clear_authentication();
                        throw new PodioAuthorizationError($body_str, $response->status, $url);
                    }
                } elseif (strstr($body['error'], 'invalid_request') || strstr($body['error'], 'unauthorized')) {
                    // Access token is invalid.
                    $this->clear_authentication();
                    throw new PodioAuthorizationError($body_str, $response->status, $url);
                }
                break;
            case 403:
                throw new PodioForbiddenError($response->body ?? $http_response->getBody()->getContents(), $response->status, $url);
            case 404:
                throw new PodioNotFoundError($response->body ?? $http_response->getBody()->getContents(), $response->status, $url);
            case 409:
                throw new PodioConflictError($response->body ?? $http_response->getBody()->getContents(), $response->status, $url);
            case 410:
                throw new PodioGoneError($response->body ?? $http_response->getBody()->getContents(), $response->status, $url);
            case 420:
                throw new PodioRateLimitError($response->body ?? $http_response->getBody()->getContents(), $response->status, $url);
            case 500:
                throw new PodioServerError($response->body ?? $http_response->getBody()->getContents(), $response->status, $url);
            case 502:
            case 503:
            case 504:
                throw new PodioUnavailableError($response->body ?? $http_response->getBody()->getContents(), $response->status, $url);
            default:
                throw new PodioError($response->body ?? $http_response->getBody()->getContents(), $response->status, $url);
        }
        return false;
    }

    public function get($url, $attributes = [], $options = [])
    {
        return $this->request(PodioClient::GET, $url, $attributes, $options);
    }
    public function post($url, $attributes = [], $options = [])
    {
        return $this->request(PodioClient::POST, $url, $attributes, $options);
    }
    public function put($url, $attributes = [])
    {
        return $this->request(PodioClient::PUT, $url, $attributes);
    }
    public function delete($url, $attributes = [])
    {
        return $this->request(PodioClient::DELETE, $url, $attributes);
    }

    public function encode_attributes($attributes): string
    {
        $return = [];
        foreach ($attributes as $key => $value) {
            $return[] = urlencode($key) . '=' . urlencode($value);
        }
        return join('&', $return);
    }
    public function url_with_options($url, $options): string
    {
        $parameters = [];

        if (isset($options['silent']) && $options['silent']) {
            $parameters[] = 'silent=1';
        }

        if (isset($options['hook']) && !$options['hook']) {
            $parameters[] = 'hook=false';
        }

        if (!empty($options['fields'])) {
            $parameters[] = 'fields=' . $options['fields'];
        }

        return $parameters ? $url . '?' . join('&', $parameters) : $url;
    }

    public function rate_limit_remaining(): string
    {
        if (isset($this->last_http_response)) {
            return implode($this->last_http_response->getHeader('x-rate-limit-remaining'));
        }
        return '-1';
    }

    public function rate_limit(): string
    {
        if (isset($this->last_http_response)) {
            return implode($this->last_http_response->getHeader('x-rate-limit-limit'));
        }
        return '-1';
    }

    /**
     * Set debug config
     *
     * @param $toggle boolean True to enable debugging. False to disable
     * @param $output string Output mode. Can be "stdout" or "file". Default is "stdout"
     */
    public function set_debug(bool $toggle, string $output = "stdout")
    {
        if ($toggle) {
            $this->debug = $output;
        } else {
            $this->debug = false;
        }
    }

    protected function log_request($method, $url, $encoded_attributes, $response, $transferTime): void
    {
        if ($this->debug) {
            if (!$this->logger) {
                $this->logger = new PodioLogger();
            }
            $timestamp = gmdate('Y-m-d H:i:s');
            $text = "{$timestamp} {$response->status} {$method} {$url}\n";
            if (!empty($encoded_attributes)) {
                $text .= "{$timestamp} Request body: " . $encoded_attributes . "\n";
            }
            $text .= "{$timestamp} Response: {$response->body}\n\n";

            if ($this->debug === 'file') {
                $this->logger->log($text);
            } elseif ($this->debug === 'stdout' && php_sapi_name() !== 'cli' && class_exists('\\Kint\\Kint')) {
                /** @noinspection PhpFullyQualifiedNameUsageInspection */
                \Kint\Kint::dump("{$method} {$url}", $encoded_attributes, $response);
            } else {
                print $text;
            }

            $this->logger->call_log[] = $transferTime;
        }
    }

    public function shutdown()
    {
        // Write any new access and refresh tokens to session.
        if ($this->session_manager) {
            $this->session_manager->set($this->oauth, $this->auth_type);
        }

        // Log api call times if debugging
        if ($this->debug && $this->logger) {
            $timestamp = gmdate('Y-m-d H:i:s');
            $count = sizeof($this->logger->call_log);
            $duration = 0;
            if ($this->logger->call_log) {
                foreach ($this->logger->call_log as $val) {
                    $duration += $val;
                }
            }

            $text = "\n{$timestamp} Performed {$count} request(s) in {$duration} seconds\n";
            if ($this->debug === 'file') {
                $this->logger->log($text);
            } elseif ($this->debug === 'stdout' && php_sapi_name() === 'cli') {
                print $text;
            }
        }
    }
}
