<?php

namespace Sunrise\Stream\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Sunrise\Stream\Exception\UnopenableStreamException;
use Sunrise\Stream\Exception\UnreadableStreamException;
use Sunrise\Stream\Exception\UnseekableStreamException;
use Sunrise\Stream\Exception\UntellableStreamException;
use Sunrise\Stream\Exception\UnwritableStreamException;
use Sunrise\Stream\Stream;

class StreamTest extends TestCase
{
	private $handle;

	protected function setUp()
	{
		$this->handle = \fopen('php://memory', 'r+b');
	}

	protected function tearDown()
	{
		if (\is_resource($this->handle))
		{
			\fclose($this->handle);
		}
	}

	public function testConstructor()
	{
		$stream = new Stream($this->handle);

		$this->assertInstanceOf(StreamInterface::class, $stream);
		$this->assertStreamResourceEquals($stream, $this->handle);
	}

	public function testConstructorWithInvalidResource()
	{
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Invalid stream resource');

		new Stream('');
	}

	public function testDetach()
	{
		$stream = new Stream($this->handle);

		$this->assertEquals($this->handle, $stream->detach());
		$this->assertStreamResourceEquals($stream, null);
		$this->assertNull($stream->detach());
	}

	public function testClose()
	{
		$stream = new Stream($this->handle);

		$stream->close();
		$this->assertStreamResourceEquals($stream, null);
		$this->assertFalse(\is_resource($this->handle));
	}

	public function testEof()
	{
		$stream = new Stream($this->handle);

		while (! \feof($this->handle)) {
			\fread($this->handle, 1024);
		}

		$this->assertTrue($stream->eof());
		\rewind($this->handle);
		$this->assertFalse($stream->eof());
	}

	public function testTell()
	{
		$string = 'Hello, world!';
		$length = \strlen($string);

		$stream = new Stream($this->handle);

		\rewind($this->handle);
		$this->assertEquals(0, $stream->tell());

		\fwrite($this->handle, $string, $length);
		$this->assertEquals($length, $stream->tell());

		\rewind($this->handle);
		$this->assertEquals(0, $stream->tell());
	}

	public function testTellUnresourceable()
	{
		$this->expectException(UntellableStreamException::class);
		$this->expectExceptionMessage('Stream is not resourceable');

		$stream = new Stream($this->handle);

		$stream->close();
		$stream->tell();
	}

	public function testIsSeekable()
	{
		$stream = new Stream($this->handle);

		$this->assertTrue($stream->isSeekable());

		$stream->detach();
		$this->assertFalse($stream->isSeekable());
	}

	public function testRewind()
	{
		$string = 'Hello, world!';
		$length = \strlen($string);

		$stream = new Stream($this->handle);

		\fwrite($this->handle, $string, $length);
		$stream->rewind();
		$this->assertEquals(0, \ftell($this->handle));
	}

	public function testRewindUnresourceable()
	{
		$this->expectException(UnseekableStreamException::class);
		$this->expectExceptionMessage('Stream is not resourceable');

		$stream = new Stream($this->handle);

		$stream->close();
		$stream->rewind();
	}

	public function testSeek()
	{
		$string = 'Hello, world!';
		$length = \strlen($string);

		$stream = new Stream($this->handle);

		\fwrite($this->handle, $string, $length);
		\rewind($this->handle);
		$stream->seek($length, \SEEK_SET);
		$this->assertEquals($length, \ftell($this->handle));
	}

	public function testSeekUnresourceable()
	{
		$this->expectException(UnseekableStreamException::class);
		$this->expectExceptionMessage('Stream is not resourceable');

		$stream = new Stream($this->handle);

		$stream->close();
		$stream->seek(0, \SEEK_SET);
	}

	public function testIsWritable()
	{
		$stream = new Stream(\STDOUT);
		$this->assertTrue($stream->isWritable());

		$stream = new Stream(\STDIN);
		$this->assertFalse($stream->isWritable());
	}

	public function testWrite()
	{
		$string = 'Hello, world!';
		$length = \strlen($string);

		$stream = new Stream($this->handle);

		$this->assertEquals($length, $stream->write($string));

		\rewind($this->handle);
		$this->assertEquals($string, \fread($this->handle, $length));
	}

	public function testWriteUnresourceable()
	{
		$this->expectException(UnwritableStreamException::class);
		$this->expectExceptionMessage('Stream is not resourceable');

		$stream = new Stream($this->handle);

		$stream->close();
		$stream->write('0', 1);
	}

	public function testWriteUnwritable()
	{
		$this->expectException(UnwritableStreamException::class);
		$this->expectExceptionMessage('Stream is not writable');

		$stream = new Stream(\STDIN);
		$stream->write('0', 1);
	}

	public function testIsReadable()
	{
		$stream = new Stream(\STDIN);
		$this->assertTrue($stream->isReadable());

		$stream = new Stream(\STDOUT);
		$this->assertFalse($stream->isReadable());
	}

	public function testRead()
	{
		$string = 'Hello, world!';
		$length = \strlen($string);

		$stream = new Stream($this->handle);

		\fwrite($this->handle, $string);
		\rewind($this->handle);
		$this->assertEquals($string, $stream->read($length));
	}

	public function testReadUnresourceable()
	{
		$this->expectException(UnreadableStreamException::class);
		$this->expectExceptionMessage('Stream is not resourceable');

		$stream = new Stream($this->handle);

		$stream->close();
		$stream->read(1);
	}

	public function testReadUnreadable()
	{
		$this->expectException(UnreadableStreamException::class);
		$this->expectExceptionMessage('Stream is not readable');

		$stream = new Stream(\STDOUT);
		$stream->read(1);
	}

	public function testGetContents()
	{
		$string = 'Hello, world!';
		$length = \strlen($string);

		$stream = new Stream($this->handle);

		\fwrite($this->handle, $string);
		\rewind($this->handle);
		$this->assertEquals($string, $stream->getContents());
	}

	public function testGetContentsUnresourceable()
	{
		$this->expectException(UnreadableStreamException::class);
		$this->expectExceptionMessage('Stream is not resourceable');

		$stream = new Stream($this->handle);

		$stream->close();
		$stream->getContents();
	}

	public function testGetContentsUnreadable()
	{
		$this->expectException(UnreadableStreamException::class);
		$this->expectExceptionMessage('Stream is not readable');

		$stream = new Stream(\STDOUT);
		$stream->getContents();
	}

	public function testGetMetadata()
	{
		$stream = new Stream($this->handle);

		$this->assertEquals(
			\stream_get_meta_data($this->handle),
			$stream->getMetadata()
		);
	}

	public function testGetMetadataWithKey()
	{
		$stream = new Stream($this->handle);

		$this->assertEquals(
			'php://memory',
			$stream->getMetadata('uri')
		);

		$this->assertEquals(
			null,
			$stream->getMetadata('undefined')
		);
	}

	public function testGetMetadataUnresourceable()
	{
		$stream = new Stream($this->handle);

		$stream->close();
		$this->assertNull($stream->getMetadata());
	}

	public function testGetSize()
	{
		$string = 'Hello, world!';
		$length = \strlen($string);

		$stream = new Stream($this->handle);

		\fwrite($this->handle, $string);
		$this->assertEquals($length, $stream->getSize());

		\ftruncate($this->handle, 0);
		$this->assertEquals(0, $stream->getSize());
	}

	public function testGetSizeUnresourceable()
	{
		$stream = new Stream($this->handle);

		$stream->close();
		$this->assertNull($stream->getSize());
	}

	public function testToString()
	{
		$string = 'Hello, world!';
		$length = \strlen($string);

		$stream = new Stream($this->handle);

		\fwrite($this->handle, $string);
		$this->assertEquals($string, (string) $stream);
	}

	public function testToStringUnresourceable()
	{
		$stream = new Stream($this->handle);

		$stream->close();
		$this->assertEquals('', (string) $stream);
	}

	public function testToStringUnreadable()
	{
		$stream = new Stream(\STDOUT);

		$this->assertEquals('', (string) $stream);
	}

	public function testExceptions()
	{
		$this->assertInstanceOf(\RuntimeException::class, new UnopenableStreamException(''));
		$this->assertInstanceOf(\RuntimeException::class, new UnreadableStreamException(''));
		$this->assertInstanceOf(\RuntimeException::class, new UnseekableStreamException(''));
		$this->assertInstanceOf(\RuntimeException::class, new UntellableStreamException(''));
		$this->assertInstanceOf(\RuntimeException::class, new UnwritableStreamException(''));
	}

	private function assertStreamResourceEquals(StreamInterface $stream, $expected)
	{
		$property = new \ReflectionProperty($stream, 'resource');

		$property->setAccessible(true);

		return $this->assertEquals($property->getValue($stream), $expected);
	}
}
