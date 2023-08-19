<?php

namespace Podio\Tests;

use PodioConversationMessage;
use PHPUnit\Framework\TestCase;

class PodioConversationMessageTest extends TestCase
{
    public function test__construct()
    {
        $message = new PodioConversationMessage(['message_id' => 42]);
        $this->assertEquals(42, $message->message_id);
        $this->assertEquals(42, $message->id);
    }
}
