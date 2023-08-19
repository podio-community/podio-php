<?php

namespace Podio\Tests;

use PodioComment;
use PHPUnit\Framework\TestCase;

class PodioCommentTest extends TestCase
{
    public function test__construct()
    {
        $comment = new PodioComment(['comment_id' => 42]);
        $this->assertEquals(42, $comment->comment_id);
        $this->assertEquals(42, $comment->id);
    }
}
