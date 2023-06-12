<?php

namespace Podio\Tests;

require __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use PodioItem;
use PodioItemFieldCollection;

class PodioItemFieldTest extends TestCase
{
    private $mockClient;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockClient = $this->createMock(\PodioClient::class);
    }
    public function test_save_should_throw_error_if_relationship_to_item_missing(): void
    {
        $this->expectException('PodioMissingRelationshipError');
        $itemField = new \PodioItemField($this->mockClient);
        $itemField->save();
    }

    public function test_save_should_throw_error_if_external_id_missing(): void
    {
        $this->expectException('PodioDataIntegrityError');
        $itemField = new \PodioItemField($this->mockClient);
        // assure relationship to item is present:
        new PodioItem($this->mockClient, ['fields' => new PodioItemFieldCollection($this->mockClient, [$itemField])]);

        $itemField->save();
    }
}
