<?php

namespace Sunrise\Stream\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Sunrise\Stream\Exception\UnopenableStreamException;
use Sunrise\Stream\StreamFactory;

class StreamFactoryTest extends TestCase
{
	public function testConstructor()
	{
		$factory = new StreamFactory();

		$this->assertInstanceOf(StreamFactoryInterface::class, $factory);
	}

	public function testCreateStream()
	{
		$content = 'Hello, world!';

		$stream = (new StreamFactory)->createStream($content);

		$this->assertInstanceOf(StreamInterface::class, $stream);

		$this->assertTrue($stream->isReadable());

		$this->assertTrue($stream->isWritable());

		$this->assertEquals('php://temp', $stream->getMetadata('uri'));

		$this->assertEquals(0, $stream->tell());

		$this->assertEquals($content, (string) $stream);

		$stream->close();
	}

	public function testCreateStreamFromFile()
	{
		$stream = (new StreamFactory)->createStreamFromFile('php://memory', 'r+b');

		$this->assertInstanceOf(StreamInterface::class, $stream);

		$stream->close();
	}

	public function testCreateStreamFromResource()
	{
		$resource = \fopen('php://memory', 'r+b');

		$stream = (new StreamFactory)->createStreamFromResource($resource);

		$this->assertInstanceOf(StreamInterface::class, $stream);

		\fclose($resource);
	}

	public function testCreateStreamFromUnopenableFile()
	{
		$this->expectException(UnopenableStreamException::class);
		$this->expectExceptionMessage(\sprintf('Unable to open file "%s/nonexistent.file" in mode "r"', __DIR__));

		(new StreamFactory)->createStreamFromFile(__DIR__ . '/nonexistent.file', 'r');
	}
}
