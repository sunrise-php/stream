<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Fenric <anatoly@fenric.ru>
 * @copyright Copyright (c) 2018, Anatoly Fenric
 * @license https://github.com/sunrise-php/stream/blob/master/LICENSE
 * @link https://github.com/sunrise-php/stream
 */

namespace Sunrise\Stream;

/**
 * StreamInterface
 */
interface StreamInterface
{

	/**
	 * Constructor of the class
	 *
	 * @param resource $resource
	 *
	 * @throws Exception\InvalidArgumentException
	 */
	public function __construct($resource);

	/**
	 * Checks if the stream is resourceable
	 *
	 * @return bool
	 */
	public function isResourceable() : bool;

	/**
	 * Detaches a resource from the stream
	 *
	 * Returns NULL if the stream already without a resource.
	 *
	 * @return null|resource
	 */
	public function detach();

	/**
	 * Closes the stream
	 *
	 * @return void
	 *
	 * @link http://php.net/manual/en/function.fclose.php
	 */
	public function close() : void;

	/**
	 * Checks if the end of the stream is reached
	 *
	 * @return bool
	 *
	 * @link http://php.net/manual/en/function.feof.php
	 */
	public function eof() : bool;

	/**
	 * Gets the stream pointer position
	 *
	 * @return int
	 *
	 * @throws Exception\UntellableStreamException
	 *
	 * @link http://php.net/manual/en/function.ftell.php
	 */
	public function tell() : int;

	/**
	 * Checks if the stream is seekable
	 *
	 * @return bool
	 */
	public function isSeekable() : bool;

	/**
	 * Moves the stream pointer to begining
	 *
	 * @return void
	 *
	 * @throws Exception\UnseekableStreamException
	 *
	 * @link http://php.net/manual/en/function.rewind.php
	 */
	public function rewind() : void;

	/**
	 * Moves the stream pointer to the given position
	 *
	 * @param int $offset
	 * @param int $whence
	 *
	 * @return void
	 *
	 * @throws Exception\UnseekableStreamException
	 *
	 * @link http://php.net/manual/en/function.fseek.php
	 */
	public function seek(int $offset, int $whence = \SEEK_SET) : void;

	/**
	 * Checks if the stream is writable
	 *
	 * @return bool
	 */
	public function isWritable() : bool;

	/**
	 * Writes the given string to the stream
	 *
	 * Returns the number of bytes written to the stream.
	 *
	 * @param string $string
	 *
	 * @return int
	 *
	 * @throws Exception\UnwritableStreamException
	 *
	 * @link http://php.net/manual/en/function.fwrite.php
	 */
	public function write(string $string) : int;

	/**
	 * Truncates the stream to the given length
	 *
	 * @param int $length
	 *
	 * @return void
	 *
	 * @throws Exception\UnwritableStreamException
	 *
	 * @link http://php.net/manual/en/function.ftruncate.php
	 */
	public function truncate(int $length = 0) : void;

	/**
	 * Checks if the stream is readable
	 *
	 * @return bool
	 */
	public function isReadable() : bool;

	/**
	 * Reads the given number of bytes from the stream
	 *
	 * @param int $length
	 *
	 * @return string
	 *
	 * @throws Exception\UnreadableStreamException
	 *
	 * @link http://php.net/manual/en/function.fread.php
	 */
	public function read(int $length) : string;

	/**
	 * Reads remainder of the stream
	 *
	 * @return string
	 *
	 * @throws Exception\UnreadableStreamException
	 *
	 * @link http://php.net/manual/en/function.stream-get-contents.php
	 */
	public function getContents() : string;

	/**
	 * Gets the stream metadata
	 *
	 * Returns NULL if the stream without a resource.
	 *
	 * @return null|array
	 *
	 * @link http://php.net/manual/en/function.stream-get-meta-data.php
	 */
	public function getMetadata() : ?array;

	/**
	 * Gets the stream size
	 *
	 * Returns NULL if the stream without a resource, or if the stream size cannot be determined.
	 *
	 * @return null|int
	 *
	 * @link http://php.net/manual/en/function.fstat.php
	 */
	public function getSize() : ?int;

	/**
	 * Converts the stream to string
	 *
	 * This method SHOULD NOT throw an exception.
	 *
	 * @return string
	 */
	public function toString() : string;
}
