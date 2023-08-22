<?php

namespace Podio\Tests;

use PodioSpace;
use PHPUnit\Framework\TestCase;

class PodioSpaceTest extends TestCase
{
    public function test__construct()
    {
        $space = new PodioSpace(['space_id' => 42]);
        $this->assertEquals(42, $space->space_id);
        $this->assertEquals(42, $space->id);
    }
}
