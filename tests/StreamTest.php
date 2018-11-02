<?php

namespace Sunrise\Stream\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Sunrise\Stream\Exception\Exception;
use Sunrise\Stream\Exception\InvalidArgumentException;
use Sunrise\Stream\Exception\UnopenableStreamException;
use Sunrise\Stream\Exception\UnreadableStreamException;
use Sunrise\Stream\Exception\UnseekableStreamException;
use Sunrise\Stream\Exception\UntellableStreamException;
use Sunrise\Stream\Exception\UnwritableStreamException;
use Sunrise\Stream\Stream;

class StreamTest extends TestCase
{
	public function testConstructor()
	{
		$handle = \fopen('php://memory', 'r+b');
		$stream = new Stream($handle);

		$this->assertInstanceOf(StreamInterface::class, $stream);

		\fclose($handle);
	}

	public function testIsResourceable()
	{
		$handle = \fopen('php://memory', 'r+b');
		$stream = new Stream($handle);

		$this->assertTrue($stream->isResourceable());

		$stream->detach();
		$this->assertFalse($stream->isResourceable());

		\fclose($handle);
	}

	public function testDetach()
	{
		$handle = \fopen('php://memory', 'r+b');
		$stream = new Stream($handle);

		$this->assertEquals($handle, $stream->detach());

		$this->assertFalse($stream->isResourceable());
		$this->assertEquals(null, $stream->detach());

		\fclose($handle);
	}

	public function testClose()
	{
		$handle = \fopen('php://memory', 'r+b');
		$stream = new Stream($handle);

		$stream->close();
		$this->assertFalse(\is_resource($handle));

		if (\is_resource($handle)) {
			\fclose($handle);
		}
	}

	public function testEof()
	{
		$handle = \fopen('php://memory', 'r+b');
		$stream = new Stream($handle);

		while (! \feof($handle)) {
			\fread($handle, 1024);
		}

		$this->assertTrue($stream->eof());

		\rewind($handle);
		$this->assertFalse($stream->eof());

		\fclose($handle);
	}

	public function testTell()
	{
		$string = 'Hello, world!';
		$length = \strlen($string);

		$handle = \fopen('php://memory', 'r+b');
		$stream = new Stream($handle);

		\rewind($handle);
		$this->assertEquals(0, $stream->tell());

		\fwrite($handle, $string, $length);
		$this->assertEquals($length, $stream->tell());

		\rewind($handle);
		$this->assertEquals(0, $stream->tell());

		\fclose($handle);
	}

	public function testIsSeekable()
	{
		$handle = \fopen('php://memory', 'r+b');
		$stream = new Stream($handle);

		$this->assertTrue($stream->isSeekable());

		$stream->detach();
		$this->assertFalse($stream->isSeekable());

		\fclose($handle);
	}

	public function testRewind()
	{
		$string = 'Hello, world!';
		$length = \strlen($string);

		$handle = \fopen('php://memory', 'r+b');
		$stream = new Stream($handle);

		\fwrite($handle, $string, $length);
		$stream->rewind();
		$this->assertEquals(0, \ftell($handle));

		\fclose($handle);
	}

	public function testSeek()
	{
		$string = 'Hello, world!';
		$length = \strlen($string);

		$handle = \fopen('php://memory', 'r+b');
		$stream = new Stream($handle);

		\fwrite($handle, $string, $length);
		\rewind($handle);
		$stream->seek($length, \SEEK_SET);
		$this->assertEquals($length, \ftell($handle));

		\fclose($handle);
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

		$handle = \fopen('php://memory', 'r+b');
		$stream = new Stream($handle);

		$this->assertEquals($length, $stream->write($string));

		\rewind($handle);
		$this->assertEquals($string, \fread($handle, $length));

		\fclose($handle);
	}

	public function testTruncate()
	{
		$string = 'Hello, world!';
		$length = \strlen($string);

		$handle = \fopen('php://memory', 'r+b');
		$stream = new Stream($handle);

		\fwrite($handle, $string);
		$stream->truncate(4);
		\rewind($handle);
		$this->assertEquals(\substr($string, 0, 4), \fread($handle, $length));

		$stream->truncate(0);
		\rewind($handle);
		$this->assertEquals('', \fread($handle, $length));

		\fclose($handle);
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

		$handle = \fopen('php://memory', 'r+b');
		$stream = new Stream($handle);

		\fwrite($handle, $string);
		\rewind($handle);
		$this->assertEquals($string, $stream->read($length));

		\fclose($handle);
	}

	public function testGetContents()
	{
		$string = 'Hello, world!';
		$length = \strlen($string);

		$handle = \fopen('php://memory', 'r+b');
		$stream = new Stream($handle);

		\fwrite($handle, $string);
		\rewind($handle);
		$this->assertEquals($string, $stream->getContents());

		\fclose($handle);
	}

	public function testGetMetadata()
	{
		$handle = \fopen('php://memory', 'r+b');
		$stream = new Stream($handle);

		$this->assertEquals(
			\stream_get_meta_data($handle),
			$stream->getMetadata()
		);

		\fclose($handle);
	}

	public function testGetMetadataWithKey()
	{
		$handle = \fopen('php://memory', 'r+b');
		$stream = new Stream($handle);

		$this->assertEquals(
			'php://memory',
			$stream->getMetadata('uri')
		);

		$this->assertEquals(
			null,
			$stream->getMetadata('undefined')
		);

		\fclose($handle);
	}

	public function testGetSize()
	{
		$string = 'Hello, world!';
		$length = \strlen($string);

		$handle = \fopen('php://memory', 'r+b');
		$stream = new Stream($handle);

		\fwrite($handle, $string);
		$this->assertEquals($length, $stream->getSize());

		\ftruncate($handle, 0);
		$this->assertEquals(0, $stream->getSize());

		\fclose($handle);
	}

	public function testToString()
	{
		$string = 'Hello, world!';
		$length = \strlen($string);

		$handle = \fopen('php://memory', 'r+b');
		$stream = new Stream($handle);

		\fwrite($handle, $string);
		$this->assertEquals($string, (string) $stream);

		\fclose($handle);
	}

	public function testConstructorWithInvalidResource()
	{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('Invalid stream resource');

		new Stream('');
	}

	public function testTellUnresourceable()
	{
		$this->expectException(UntellableStreamException::class);
		$this->expectExceptionMessage('Stream is not resourceable');

		$handle = \fopen('php://memory', 'r+b');
		$stream = new Stream($handle);

		$stream->close();
		$stream->tell();
	}

	public function testRewindUnresourceable()
	{
		$this->expectException(UnseekableStreamException::class);
		$this->expectExceptionMessage('Stream is not resourceable');

		$handle = \fopen('php://memory', 'r+b');
		$stream = new Stream($handle);

		$stream->close();
		$stream->rewind();
	}

	public function testSeekUnresourceable()
	{
		$this->expectException(UnseekableStreamException::class);
		$this->expectExceptionMessage('Stream is not resourceable');

		$handle = \fopen('php://memory', 'r+b');
		$stream = new Stream($handle);

		$stream->close();
		$stream->seek(0, \SEEK_SET);
	}

	public function testWriteUnresourceable()
	{
		$this->expectException(UnwritableStreamException::class);
		$this->expectExceptionMessage('Stream is not resourceable');

		$handle = \fopen('php://memory', 'r+b');
		$stream = new Stream($handle);

		$stream->close();
		$stream->write('0', 1);
	}

	public function testTruncateUnresourceable()
	{
		$this->expectException(UnwritableStreamException::class);
		$this->expectExceptionMessage('Stream is not resourceable');

		$handle = \fopen('php://memory', 'r+b');
		$stream = new Stream($handle);

		$stream->close();
		$stream->truncate(0);
	}

	public function testReadUnresourceable()
	{
		$this->expectException(UnreadableStreamException::class);
		$this->expectExceptionMessage('Stream is not resourceable');

		$handle = \fopen('php://memory', 'r+b');
		$stream = new Stream($handle);

		$stream->close();
		$stream->read(1);
	}

	public function testGetContentsUnresourceable()
	{
		$this->expectException(UnreadableStreamException::class);
		$this->expectExceptionMessage('Stream is not resourceable');

		$handle = \fopen('php://memory', 'r+b');
		$stream = new Stream($handle);

		$stream->close();
		$stream->getContents();
	}

	public function testWriteUnwritable()
	{
		$this->expectException(UnwritableStreamException::class);
		$this->expectExceptionMessage('Stream is not writable');

		$stream = new Stream(\STDIN);
		$stream->write('0', 1);
	}

	public function testTruncateUnwritable()
	{
		$this->expectException(UnwritableStreamException::class);
		$this->expectExceptionMessage('Stream is not writable');

		$stream = new Stream(\STDIN);
		$stream->truncate(0);
	}

	public function testReadUnreadable()
	{
		$this->expectException(UnreadableStreamException::class);
		$this->expectExceptionMessage('Stream is not readable');

		$stream = new Stream(\STDOUT);
		$stream->read(1);
	}

	public function testGetContentsUnreadable()
	{
		$this->expectException(UnreadableStreamException::class);
		$this->expectExceptionMessage('Stream is not readable');

		$stream = new Stream(\STDOUT);
		$stream->getContents();
	}

	public function testGetMetadataUnresourceable()
	{
		$handle = \fopen('php://memory', 'r+b');
		$stream = new Stream($handle);

		$stream->close();
		$this->assertEquals(null, $stream->getMetadata());
	}

	public function testGetSizeUnresourceable()
	{
		$handle = \fopen('php://memory', 'r+b');
		$stream = new Stream($handle);

		$stream->close();
		$this->assertEquals(null, $stream->getSize());
	}

	public function testToStringUnresourceable()
	{
		$handle = \fopen('php://memory', 'r+b');
		$stream = new Stream($handle);

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
		$this->assertInstanceOf(\RuntimeException::class, new Exception(''));
		$this->assertInstanceOf(Exception::class, new InvalidArgumentException(''));
		$this->assertInstanceOf(Exception::class, new UnopenableStreamException(''));
		$this->assertInstanceOf(Exception::class, new UnreadableStreamException(''));
		$this->assertInstanceOf(Exception::class, new UnseekableStreamException(''));
		$this->assertInstanceOf(Exception::class, new UntellableStreamException(''));
		$this->assertInstanceOf(Exception::class, new UnwritableStreamException(''));
	}
}
