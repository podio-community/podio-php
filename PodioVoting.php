<?php
class PodioVoting extends PodioObject
{
  public function __construct($attributes = array())
  {
    $this->property('count', 'integer');
    $this->init($attributes);
  }
  
  public static function get_result_for_item($item_id, $voting_id)
  {
    return self::member(Podio::get("/voting/item/{$item_id}/voting/{$voting_id}/result"));    
  }
  
  public static function get_voting_id($app_id)
  {
    return Podio::get("/voting/app/{$app_id}/voting")->json_body();    
  }
  
  public static function get_list_of_users_with_votes($item_id, $voting_id)
  {
    return Podio::get("/voting/item/{$item_id}/voting/{$voting_id}/user")->json_body();    
  }
  
}
