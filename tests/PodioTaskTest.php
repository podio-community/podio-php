<?php

namespace Podio\Tests;

use PodioTask;
use PHPUnit\Framework\TestCase;

class PodioTaskTest extends TestCase
{
    public function test_save_performs_update_when_id_present()
    {
        $mockClient = $this->createMock(\PodioClient::class);
        $mockClient->method('url_with_options')->willReturnArgument(0);
        $mockClient->expects($this->once())
            ->method('put')
            ->with('/task/1', $this->anything())
            ->willReturn(['task_id' => 1]);
        $task = new PodioTask(['task_id' => 1]);
        PodioTask::save($mockClient, $task);
    }

    public function test_save_performs_create_when_no_id_present()
    {
        $mockClient = $this->createMock(\PodioClient::class);
        $mockClient->method('url_with_options')->willReturnArgument(0);
        $mockClient->expects($this->once())
            ->method('post')
            ->with('/task/', $this->anything())
            ->willReturn(['task_id' => 2]);
        $task = new PodioTask();
        $created = PodioTask::save($mockClient, $task);
        $this->assertEquals(2, $created->task_id);
    }
}
