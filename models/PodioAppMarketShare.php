<?php
/**
 * @see https://developers.podio.com/doc/app-store
 */
class PodioAppMarketShare extends PodioObject
{
    public function __construct(PodioClient $podio_client, $attributes = array())
    {
        parent::__construct($podio_client);
        $this->property('share_id', 'integer', array('id' => true));
        $this->property('type', 'string');
        $this->property('status', 'string');
        $this->property('name', 'string');
        $this->property('description', 'string');
        $this->property('abstract', 'string');
        $this->property('language', 'string');
        $this->property('features', 'array');
        $this->property('filters', 'array');
        $this->property('integration', 'string');
        $this->property('categories', 'hash');
        $this->property('org', 'hash');
        $this->property('author_apps', 'integer');
        $this->property('author_packs', 'integer');
        $this->property('icon', 'string');
        $this->property('icon_id', 'integer');
        $this->property('ratings', 'hash');
        $this->property('user_rating', 'array');
        $this->property('video', 'string');
        $this->property('rating', 'integer');

        $this->has_one('author', 'ByLine');
        $this->has_one('app', 'App');
        $this->has_one('space', 'Space');

        $this->has_many('children', 'AppMarketShare');
        $this->has_many('parents', 'AppMarketShare');
        $this->has_many('screenshots', 'File');
        $this->has_many('comments', 'Comment');

        $this->init($attributes);
    }

    /**
     * @see https://developers.podio.com/doc/app-market/install-share-22499
     */
    public static function install(PodioClient $podio_client, $share_id, $attributes = array())
    {
        return $podio_client->post("/app_store/{$share_id}/install", $attributes)->json_body();
    }
}
