<?php

namespace Podio\Tests;

use PodioAssetItemField;
use PodioCollection;
use PodioFile;

class PodioAssetItemFieldTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        $this->object = new PodioAssetItemField(array(
      '__api_values' => true,
      'values' => array(
        array('value' => array('file_id' => 1, 'name' => 'doge.jpg')),
        array('value' => array('file_id' => 2, 'name' => 'trollface.jpg')),
        array('value' => array('file_id' => 3, 'name' => 'YUNO.jpg')),
      )
    ));
    }

    public function test_can_construct_from_simple_value()
    {
        $object = new PodioAssetItemField(array(
      'field_id' => 123,
      'values' => array('file_id' => 4, 'name' => 'philosoraptor.jpg')
    ));
        $this->assertEquals(array(
      array('value' => array('file_id' => 4, 'name' => 'philosoraptor.jpg')),
    ), $object->__attribute('values'));
    }

    public function test_can_provide_value()
    {
        // Empty values
        $empty_values = new PodioAssetItemField(array('field_id' => 1));
        $this->assertNull($empty_values->values);

        // Populated values
        $this->assertInstanceOf('PodioCollection', $this->object->values);
        $this->assertEquals(3, count($this->object->values));
        foreach ($this->object->values as $value) {
            $this->assertInstanceOf('PodioFile', $value);
        }
    }

    public function test_can_set_value_from_object()
    {
        $this->object->values = new PodioFile(array('file_id' => 4, 'name' => 'philosoraptor.jpg'));
        $this->assertEquals(array(
      array('value' => array('file_id' => 4, 'name' => 'philosoraptor.jpg'))
    ), $this->object->__attribute('values'));
    }

    public function test_can_set_value_from_collection()
    {
        $this->object->values = new PodioCollection(array(new PodioFile(array('file_id' => 4, 'name' => 'philosoraptor.jpg'))));

        $this->assertEquals(array(
      array('value' => array('file_id' => 4, 'name' => 'philosoraptor.jpg'))
    ), $this->object->__attribute('values'));
    }

    public function test_can_set_value_from_hash()
    {
        $this->object->values = array('file_id' => 4, 'name' => 'philosoraptor.jpg');
        $this->assertEquals(array(
      array('value' => array('file_id' => 4, 'name' => 'philosoraptor.jpg')),
    ), $this->object->__attribute('values'));
    }

    public function test_can_set_value_from_array_of_objects()
    {
        $this->object->values = array(
      new PodioFile(array('file_id' => 4, 'name' => 'philosoraptor.jpg')),
      new PodioFile(array('file_id' => 5, 'name' => 'nyancat.jgp'))
    );
        $this->assertEquals(array(
      array('value' => array('file_id' => 4, 'name' => 'philosoraptor.jpg')),
      array('value' => array('file_id' => 5, 'name' => 'nyancat.jgp')),
    ), $this->object->__attribute('values'));
    }

    public function test_can_set_value_from_array_of_hashes()
    {
        $this->object->values = array(
      array('file_id' => 4, 'name' => 'philosoraptor.jpg'),
      array('file_id' => 5, 'name' => 'nyancat.jgp')
    );
        $this->assertEquals(array(
      array('value' => array('file_id' => 4, 'name' => 'philosoraptor.jpg')),
      array('value' => array('file_id' => 5, 'name' => 'nyancat.jgp')),
    ), $this->object->__attribute('values'));
    }

    public function test_can_humanize_value()
    {
        // Empty values
        $empty_values = new PodioAssetItemField(array('field_id' => 1));
        $this->assertEquals('', $empty_values->humanized_value());

        // Populated values
        $this->assertEquals('doge.jpg;trollface.jpg;YUNO.jpg', $this->object->humanized_value());
    }

    public function test_can_convert_to_api_friendly_json()
    {
        // Empty values
        $empty_values = new PodioAssetItemField(array('field_id' => 1));
        $this->assertEquals('[]', $empty_values->as_json());

        // Populated values
        $this->assertEquals('[1,2,3]', $this->object->as_json());
    }
}
