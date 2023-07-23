<?php

namespace Podio\Tests;

require __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use PodioDataIntegrityError;
use PodioItem;
use PodioItemFieldCollection;
use PodioMissingRelationshipError;

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
        $this->expectException(PodioMissingRelationshipError::class);
        $itemField = new \PodioItemField();
        \PodioItemField::save($this->mockClient, $itemField);
    }

    public function test_save_should_throw_error_if_external_id_missing(): void
    {
        $this->expectException(PodioDataIntegrityError::class);
        $itemField = new \PodioItemField();
        // assure relationship to item is present:
        new PodioItem(['fields' => new PodioItemFieldCollection([$itemField])]);

        \PodioItemField::save($this->mockClient, $itemField);
    }
}
