<?php

namespace Podio\Tests;

use PHPUnit\Framework\TestCase;
use PodioApp;

class PodioAppTest extends TestCase
{
    public function test_performance_large_app(): void
    {
        $start = time();
        $appString = file_get_contents(__DIR__ . '/fixtures/large-app.json');
        $appJson = json_decode($appString, true);
        new PodioApp(array_merge($appJson, ['__api_values' => true]));
        $duration = time() - $start;
        $this->assertLessThan(5, $duration, "creating large app should be fast!");
    }

    public function test_app_with_id(): void
    {
        $app = new PodioApp(12345);
        $this->assertEquals(12345, $app->app_id);
        $this->assertEquals(12345, $app->id);
    }
}
