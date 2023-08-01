<?php

class PodioError extends Exception
{
    /** @var array|null */
    public $body;
    public $status;
    public $url;
    public $request;

    /**
     * @param string|null $body
     * @param int|null $status
     * @param string|null $url
     */
    public function __construct($body, $status, $url)
    {
        $this->body = $body !== null ? json_decode($body, true) : null;
        $this->status = $status;
        $this->url = $url;
        $this->request = $this->body['request'] ?? null;
        parent::__construct(get_class($this), 1, null);
    }

    public function __toString()
    {
        $str = get_class($this);
        if (!empty($this->body['error_description'])) {
            $str .= ': "'.$this->body['error_description'].'"';
        }
        $str .= "\nRequest URL: ".$this->request['url'];
        if (!empty($this->request['query_string'])) {
            $str .= '?'.$this->request['query_string'];
        }
        if (!empty($this->request['body'])) {
            $str .= "\nRequest Body: ".json_encode($this->request['body']);
        }

        $str .= "\n\nStack Trace: \n".$this->getTraceAsString();
        return $str;
    }
}
