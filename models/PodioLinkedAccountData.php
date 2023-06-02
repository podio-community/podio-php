<?php

class PodioLinkedAccountData extends PodioObject
{
    public function __construct(PodioClient $podio_client, $attributes = array())
    {
        parent::__construct($podio_client);
        $this->property('id', 'integer');
        $this->property('type', 'string');
        $this->property('info', 'string');
        $this->property('url', 'string');

        $this->init($attributes);
    }
}
