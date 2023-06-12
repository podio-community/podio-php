<?php

class PodioVia extends PodioObject
{
    public function __construct(PodioClient $podio_client, $attributes = array())
    {
        parent::__construct($podio_client);
        $this->property('id', 'integer');
        $this->property('auth_client_id', 'integer');
        $this->property('name', 'string');
        $this->property('url', 'string');
        $this->property('display', 'boolean');

        $this->init($attributes);
    }
}
