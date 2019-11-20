<?php
class PodioCalculationItemFieldTest extends PHPUnit_Framework_TestCase {

  public function setup() {
    $this->object = new PodioCalculationItemField(array(
      '__api_values' => true,
      'field_id' => 123,
      'values' => array(
        array('value' => '1234.5600')
      )
    ));
    $this->empty_values = new PodioCalculationItemField(array('field_id' => 1));
    $this->zero_value = new PodioCalculationItemField(array('__api_values' => true, 'field_id' => 2, 'values' => array(array('value' => '0'))));
    $this->date_value = new PodioCalculationItemField(array(
      '__api_values' => true, 
      'field_id' => 3, 
      'values' => array(array(
        'start' => '2016-11-11 00:00:00',
        'start_date_utc' => '2016-11-11',
        'start_time_utc' => '00:00:00',
        'start_time' => '00:00:00',
        'start_utc' => '2016-11-11 00:00:00',
        'start_date' => '2016-11-11'
      ))));
  }

  public function test_can_provide_value() {
    $this->assertNull($this->empty_values->values);
    $this->assertEquals('1234.5600', $this->object->values);
    $this->assertEquals('0', $this->zero_value->values);
    $this->assertEquals(array(
      'start' => '2016-11-11 00:00:00',
      'start_date_utc' => '2016-11-11',
      'start_time_utc' => '00:00:00',
      'start_time' => '00:00:00',
      'start_utc' => '2016-11-11 00:00:00',
      'start_date' => '2016-11-11'
    ), $this->date_value->values);
  }

  public function test_cannot_modify_value() {
    $this->object->values = '12.34';
    $this->assertEquals(array(array('value' => '1234.5600')), $this->object->__attribute('values'));
  }

  public function test_can_humanize_value() {
    $this->assertEquals('', $this->empty_values->humanized_value());
    $this->assertEquals('1234.56', $this->object->humanized_value());
    $this->assertEquals('0', $this->zero_value->humanized_value());
    // cannot humanize value for date ($this->date_value)
  }

  public function test_can_convert_to_api_friendly_json() {
    $this->assertEquals('null', $this->empty_values->as_json());
    $this->assertEquals('"1234.5600"', $this->object->as_json());
    $this->assertEquals('"0"', $this->zero_value->as_json());
    $date_value_json = '{"start":"2016-11-11 00:00:00","start_date_utc":"2016-11-11","start_time_utc":"00:00:00",' .
      '"start_time":"00:00:00","start_utc":"2016-11-11 00:00:00","start_date":"2016-11-11"}';
    $this->assertEquals('' . $date_value_json . '', $this->date_value->as_json());
  }

}
