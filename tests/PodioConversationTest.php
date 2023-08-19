<?php

namespace Podio\Tests;

use PodioConversation;
use PHPUnit\Framework\TestCase;

class PodioConversationTest extends TestCase
{
    public function test__construct()
    {
        $conversation = new PodioConversation(['conversation_id' => 42]);
        $this->assertEquals(42, $conversation->conversation_id);
        $this->assertEquals(42, $conversation->id);
    }
}
