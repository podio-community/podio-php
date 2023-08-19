<?php

namespace Podio\Tests;

use PodioOrganization;
use PHPUnit\Framework\TestCase;

class PodioOrganizationTest extends TestCase
{
    public function test__construct()
    {
        $org = new PodioOrganization(['org_id' => 42]);
        $this->assertEquals(42, $org->org_id);
        $this->assertEquals(42, $org->id);
    }
}
