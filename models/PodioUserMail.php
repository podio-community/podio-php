<?php
/**
 * @see https://developers.podio.com/doc/users
 */
class PodioUserMail extends PodioObject
{
    public function __construct(PodioClient $podio_client, $attributes = array())
    {
        parent::__construct($podio_client);
        $this->property('mail', 'string');
        $this->property('verified', 'boolean');
        $this->property('primary', 'boolean');
        $this->property('disabled', 'boolean');

        $this->init($attributes);
    }
}
