<?php
/**
 * @see https://developers.podio.com/doc/conversations
 */
class PodioConversationParticipant extends PodioObject
{
    public function __construct(PodioClient $podio_client, $attributes = array())
    {
        parent::__construct($podio_client);
        $this->property('created_on', 'datetime');

        $this->has_one('user', 'User');
        $this->has_one('created_via', 'Via');
        $this->has_one('created_by', 'ByLine');

        $this->init($attributes);
    }
}
