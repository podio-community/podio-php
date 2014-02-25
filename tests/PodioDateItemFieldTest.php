<?php
class PodioDateItemFieldTest extends PHPUnit_Framework_TestCase {

  public function setup() {
    $this->empty_values = new PodioDateItemField(array('field_id' => 1));
    $this->start_date = new PodioDateItemField(array('field_id' => 2, 'values' => array(array(
      "start_date" => "2011-05-31",
      "end_date" => "2011-05-31",
      "start_time" => null,
      "end_time" => null,
    ))));

    $this->start_datetime = new PodioDateItemField(array('field_id' => 2, 'values' => array(array(
      "start_date" => "2011-05-31",
      "end_date" => "2011-05-31",
      "start_time" => "14:00:00",
      "end_time" => null,
    ))));

    $this->start_datetime_with_endtime_same_day = new PodioDateItemField(array('field_id' => 2, 'values' => array(array(
      "start_date" => "2011-05-31",
      "end_date" => "2011-05-31",
      "start_time" => "14:00:00",
      "end_time" => "15:00:00",
    ))));

    $this->start_date_end_date = new PodioDateItemField(array('field_id' => 2, 'values' => array(array(
      "start_date" => "2011-05-31",
      "end_date" => "2011-06-08",
      "start_time" => null,
      "end_time" => null,
    ))));

    $this->start_datetime_end_date = new PodioDateItemField(array('field_id' => 2, 'values' => array(array(
      "start_date" => "2011-05-31",
      "end_date" => "2011-06-08",
      "start_time" => "14:00:00",
      "end_time" => null,
    ))));

    $this->start_datetime_end_datetime = new PodioDateItemField(array('field_id' => 2, 'values' => array(array(
      "start_date" => "2011-05-31",
      "end_date" => "2011-06-08",
      "start_time" => "14:00:00",
      "end_time" => "14:00:00",
    ))));

  }

  public function test_can_provide_values() {
    $this->assertNull($this->empty_values->values);

    $this->assertTrue(is_array($this->start_date->values));
    $this->assertEquals('2011-05-31 00:00:00', $this->start_date->values['start']->format('Y-m-d H:i:s'));
    $this->assertNull($this->start_date->values['end']);

    $this->assertTrue(is_array($this->start_datetime->values));
    $this->assertEquals('2011-05-31 14:00:00', $this->start_datetime->values['start']->format('Y-m-d H:i:s'));
    $this->assertNull($this->start_datetime->values['end']);

    $this->assertTrue(is_array($this->start_datetime_with_endtime_same_day->values));
    $this->assertEquals('2011-05-31 14:00:00', $this->start_datetime_with_endtime_same_day->values['start']->format('Y-m-d H:i:s'));
    $this->assertEquals('2011-05-31 15:00:00', $this->start_datetime_with_endtime_same_day->values['end']->format('Y-m-d H:i:s'));

    $this->assertTrue(is_array($this->start_date_end_date->values));
    $this->assertEquals('2011-05-31 00:00:00', $this->start_date_end_date->values['start']->format('Y-m-d H:i:s'));
    $this->assertEquals('2011-06-08 00:00:00', $this->start_date_end_date->values['end']->format('Y-m-d H:i:s'));

    $this->assertTrue(is_array($this->start_datetime_end_date->values));
    $this->assertEquals('2011-05-31 14:00:00', $this->start_datetime_end_date->values['start']->format('Y-m-d H:i:s'));
    $this->assertEquals('2011-06-08 00:00:00', $this->start_datetime_end_date->values['end']->format('Y-m-d H:i:s'));

    $this->assertTrue(is_array($this->start_datetime_end_datetime->values));
    $this->assertEquals('2011-05-31 14:00:00', $this->start_datetime_end_datetime->values['start']->format('Y-m-d H:i:s'));
    $this->assertEquals('2011-06-08 14:00:00', $this->start_datetime_end_datetime->values['end']->format('Y-m-d H:i:s'));

  }

  public function test_can_provide_start_datetime() {
    $this->assertNull($this->empty_values->start);
    $this->assertInstanceOf('DateTime', $this->start_date->start);
    $this->assertInstanceOf('DateTime', $this->start_datetime->start);
    $this->assertInstanceOf('DateTime', $this->start_datetime_with_endtime_same_day->start);
    $this->assertInstanceOf('DateTime', $this->start_date_end_date->start);
    $this->assertInstanceOf('DateTime', $this->start_datetime_end_date->start);
    $this->assertInstanceOf('DateTime', $this->start_datetime_end_datetime->start);
  }

  public function test_can_provide_end_datetime() {
    $this->assertNull($this->empty_values->end);
    $this->assertNull($this->start_date->end);
    $this->assertNull($this->start_datetime->end);
    $this->assertInstanceOf('DateTime', $this->start_datetime_with_endtime_same_day->end);
    $this->assertInstanceOf('DateTime', $this->start_date_end_date->end);
    $this->assertInstanceOf('DateTime', $this->start_datetime_end_date->end);
    $this->assertInstanceOf('DateTime', $this->start_datetime_end_datetime->end);
  }

  public function test_can_provide_sameday() {
    $this->assertTrue($this->empty_values->same_day());
    $this->assertTrue($this->start_date->same_day());
    $this->assertTrue($this->start_datetime->same_day());
    $this->assertTrue($this->start_datetime_with_endtime_same_day->same_day());
    $this->assertFalse($this->start_date_end_date->same_day());
    $this->assertFalse($this->start_datetime_end_date->same_day());
    $this->assertFalse($this->start_datetime_end_datetime->same_day());
  }

  public function test_can_provide_allday() {
    $this->assertFalse($this->empty_values->all_day());
    $this->assertTrue($this->start_date->all_day());
    $this->assertFalse($this->start_datetime->all_day());
    $this->assertFalse($this->start_datetime_with_endtime_same_day->all_day());
    $this->assertTrue($this->start_date_end_date->all_day());
    $this->assertFalse($this->start_datetime_end_date->all_day());
    $this->assertFalse($this->start_datetime_end_datetime->all_day());
  }

  // public function test_can_set_start() {
  // }

  // public function test_can_set_end() {
  // }

  // public function test_can_humanize_value() {
  //   $this->assertEquals('', $this->empty_values->humanized_value());
  // }

  // public function test_can_convert_to_api_friendly_json() {
  //   $this->assertEquals('null', $this->empty_values->as_json());
  // }

}
