<?php

namespace Podio\Tests;

use PodioHook;
use PHPUnit\Framework\TestCase;

class PodioHookTest extends TestCase
{
    public function test__construct()
    {
        $hook = new PodioHook(['hook_id' => 42]);
        $this->assertEquals(42, $hook->hook_id);
        $this->assertEquals(42, $hook->id);
    }
}
