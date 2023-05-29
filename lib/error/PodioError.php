<?php

class PodioError extends Exception
{
    public $body;
    public $status;
    public $url;
    public function __construct($body, $status, $url)
    {
        $this->body = json_decode($body, true);
        $this->status = $status;
        $this->url = $url;
        $this->request = $this->body['request'] ?? null;
        parent::__construct(get_class($this), 1, null);
    }

    public function __toString()
    {
        $str = $str = get_class($this);
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
