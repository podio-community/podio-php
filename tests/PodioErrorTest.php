<?php

namespace Podio\Tests;

use PodioError;
use PHPUnit\Framework\TestCase;

class PodioErrorTest extends TestCase
{
    public function test__constructShouldOutputNoWarningForEmptyBody()
    {
        $this->expectOutputString('');
        new PodioError(null, null, null);
    }
}
