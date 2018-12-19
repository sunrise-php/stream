<?php

namespace Sunrise\Stream\Tests;

use Http\Psr7Test\StreamIntegrationTest as BaseStreamIntegrationTest;
use Sunrise\Stream\Stream;

class StreamIntegrationTest extends BaseStreamIntegrationTest
{
	public function createStream($resource)
	{
		return new Stream($resource);
	}
}
