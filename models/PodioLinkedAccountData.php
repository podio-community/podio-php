<?php

class PodioLinkedAccountData extends PodioObject
{
    public function __construct($attributes = array())
    {
        parent::__construct();
        $this->property('id', 'integer');
        $this->property('type', 'string');
        $this->property('info', 'string');
        $this->property('url', 'string');

        $this->init($attributes);
    }
}
