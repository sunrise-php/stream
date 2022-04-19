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
        $stream = (new StreamFactory)->createStream('065036e5-ea69-460a-9491-01f85289ab92');
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertTrue($stream->isReadable());
        $this->assertTrue($stream->isWritable());
        $this->assertEquals('php://temp', $stream->getMetadata('uri'));
        $this->assertEquals(0, $stream->tell());
        $this->assertEquals('065036e5-ea69-460a-9491-01f85289ab92', (string) $stream);
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
        $stream = (new StreamFactory)->createStreamFromResource(\fopen('php://memory', 'r+b'));
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $stream->close();
    }

    public function testCreateStreamFromUnopenableFile()
    {
        $this->expectException(UnopenableStreamException::class);
        $this->expectExceptionMessage('Unable to open file "/a1fd94f5-9390-41b8-a3e3-8039b6015db6" in mode "r"');

        (new StreamFactory)->createStreamFromFile('/a1fd94f5-9390-41b8-a3e3-8039b6015db6', 'r');
    }

    public function testCreateStreamWithTemporaryFile()
    {
        $stream = (new StreamFactory)->createStreamFromTemporaryFile('c4ab0f0b-3ca6-43df-a58b-51e7eec44090');
        $this->assertStringEqualsFile($stream->getMetadata('uri'), 'c4ab0f0b-3ca6-43df-a58b-51e7eec44090');
    }
}
