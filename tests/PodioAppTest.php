<?php

class PodioAppTest extends \PHPUnit\Framework\TestCase
{
    public function test_performance_large_app()
    {
        $start = time();
        $appString = file_get_contents(__DIR__ . '/large-app.json');
        $appJson = json_decode($appString, true);
        new PodioApp(array_merge($appJson, array('__api_values' => true)));
        $duration = time() - $start;
        $this->assertLessThan(5, $duration, "creating large app should be fast!");
    }
}
