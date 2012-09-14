<?php
/**
 * @see https://developers.podio.com/doc/contacts
 */
class PodioProfile extends PodioObject {
  public function __construct($attributes = array()) {
    $this->property('profile_id', 'integer', array('id' => true));
    $this->property('name', 'string');
    $this->property('avatar', 'integer');
    $this->property('birthdate', 'date');
    $this->property('department', 'string');
    $this->property('vatin', 'string');
    $this->property('skype', 'string');
    $this->property('about', 'string');
    $this->property('address', 'array');
    $this->property('zip', 'string');
    $this->property('city', 'string');
    $this->property('country', 'string');
    $this->property('state', 'string');
    $this->property('im', 'array');
    $this->property('location', 'array');
    $this->property('mail', 'array');
    $this->property('phone', 'array');
    $this->property('title', 'array');
    $this->property('url', 'array');
    $this->property('skill', 'array');
    $this->property('linkedin', 'string');
    $this->property('twitter', 'string');

    $this->property('app_store_about', 'string');
    $this->property('app_store_organization', 'string');
    $this->property('app_store_location', 'string');
    $this->property('app_store_title', 'string');
    $this->property('app_store_url', 'string');

    $this->property('last_seen_on', 'datetime');
    $this->property('is_employee', 'boolean');

    $this->has_many('image', 'File');

    $this->init($attributes);
  }

}
