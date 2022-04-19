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
 * Import classes
 */
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Sunrise\Stream\Exception\UnreadableStreamException;
use Sunrise\Stream\Exception\UnseekableStreamException;
use Sunrise\Stream\Exception\UntellableStreamException;
use Sunrise\Stream\Exception\UnwritableStreamException;
use Throwable;

/**
 * Import functions
 */
use function fclose;
use function feof;
use function fread;
use function fseek;
use function fstat;
use function ftell;
use function fwrite;
use function is_resource;
use function stream_get_contents;
use function stream_get_meta_data;
use function strpbrk;

/**
 * Import constants
 */
use const SEEK_SET;

/**
 * Stream
 *
 * @link https://www.php-fig.org/psr/psr-7/
 */
class Stream implements StreamInterface
{

    /**
     * The stream resource
     *
     * @var resource|null
     */
    protected $resource;

    /**
     * Constructor of the class
     *
     * @param resource $resource
     *
     * @throws InvalidArgumentException
     */
    public function __construct($resource)
    {
        if (!is_resource($resource)) {
            throw new InvalidArgumentException('Invalid stream resource');
        }

        $this->resource = $resource;
    }

    /**
     * Detaches a resource from the stream
     *
     * Returns NULL if the stream already without a resource.
     *
     * @return resource|null
     */
    public function detach()
    {
        $resource = $this->resource;
        $this->resource = null;

        return $resource;
    }

    /**
     * Closes the stream
     *
     * @return void
     *
     * @link http://php.net/manual/en/function.fclose.php
     */
    public function close() : void
    {
        $resource = $this->detach();
        if (!is_resource($resource)) {
            return;
        }

        fclose($resource);
    }

    /**
     * Checks if the end of the stream is reached
     *
     * @return bool
     *
     * @link http://php.net/manual/en/function.feof.php
     */
    public function eof() : bool
    {
        if (!is_resource($this->resource)) {
            return true;
        }

        return feof($this->resource);
    }

    /**
     * Gets the stream pointer position
     *
     * @return int
     *
     * @throws UntellableStreamException
     *
     * @link http://php.net/manual/en/function.ftell.php
     */
    public function tell() : int
    {
        if (!is_resource($this->resource)) {
            throw new UntellableStreamException('Stream is not resourceable');
        }

        $result = ftell($this->resource);
        if ($result === false) {
            throw new UntellableStreamException('Unable to get the stream pointer position');
        }

        return $result;
    }

    /**
     * Checks if the stream is seekable
     *
     * @return bool
     */
    public function isSeekable() : bool
    {
        if (!is_resource($this->resource)) {
            return false;
        }

        /** @var array{seekable: bool} */
        $metadata = stream_get_meta_data($this->resource);

        return $metadata['seekable'];
    }

    /**
     * Moves the stream pointer to the beginning
     *
     * @return void
     *
     * @throws UnseekableStreamException
     *
     * @link http://php.net/manual/en/function.rewind.php
     */
    public function rewind() : void
    {
        if (!is_resource($this->resource)) {
            throw new UnseekableStreamException('Stream is not resourceable');
        }

        if (!$this->isSeekable()) {
            throw new UnseekableStreamException('Stream is not seekable');
        }

        $result = fseek($this->resource, 0, SEEK_SET);
        if ($result !== 0) {
            throw new UnseekableStreamException('Unable to move the stream pointer to beginning');
        }
    }

    /**
     * Moves the stream pointer to the given position
     *
     * @param int $offset
     * @param int $whence
     *
     * @return void
     *
     * @throws UnseekableStreamException
     *
     * @link http://php.net/manual/en/function.fseek.php
     */
    public function seek($offset, $whence = SEEK_SET) : void
    {
        if (!is_resource($this->resource)) {
            throw new UnseekableStreamException('Stream is not resourceable');
        }

        if (!$this->isSeekable()) {
            throw new UnseekableStreamException('Stream is not seekable');
        }

        $result = fseek($this->resource, $offset, $whence);
        if ($result !== 0) {
            throw new UnseekableStreamException('Unable to move the stream pointer to the given position');
        }
    }

    /**
     * Checks if the stream is writable
     *
     * @return bool
     */
    public function isWritable() : bool
    {
        if (!is_resource($this->resource)) {
            return false;
        }

        /** @var array{mode: string} */
        $metadata = stream_get_meta_data($this->resource);

        return strpbrk($metadata['mode'], '+acwx') !== false;
    }

    /**
     * Writes the given string to the stream
     *
     * Returns the number of bytes written to the stream.
     *
     * @param string $string
     *
     * @return int
     *
     * @throws UnwritableStreamException
     *
     * @link http://php.net/manual/en/function.fwrite.php
     */
    public function write($string) : int
    {
        if (!is_resource($this->resource)) {
            throw new UnwritableStreamException('Stream is not resourceable');
        }

        if (!$this->isWritable()) {
            throw new UnwritableStreamException('Stream is not writable');
        }

        $result = fwrite($this->resource, $string);
        if ($result === false) {
            throw new UnwritableStreamException('Unable to write to the stream');
        }

        return $result;
    }

    /**
     * Checks if the stream is readable
     *
     * @return bool
     */
    public function isReadable() : bool
    {
        if (!is_resource($this->resource)) {
            return false;
        }

        /** @var array{mode: string} */
        $metadata = stream_get_meta_data($this->resource);

        return strpbrk($metadata['mode'], '+r') !== false;
    }

    /**
     * Reads the given number of bytes from the stream
     *
     * @param int $length
     *
     * @return string
     *
     * @throws UnreadableStreamException
     *
     * @link http://php.net/manual/en/function.fread.php
     */
    public function read($length) : string
    {
        if (!is_resource($this->resource)) {
            throw new UnreadableStreamException('Stream is not resourceable');
        }

        if (!$this->isReadable()) {
            throw new UnreadableStreamException('Stream is not readable');
        }

        $result = fread($this->resource, $length);
        if ($result === false) {
            throw new UnreadableStreamException('Unable to read from the stream');
        }

        return $result;
    }

    /**
     * Reads the remainder of the stream
     *
     * @return string
     *
     * @throws UnreadableStreamException
     *
     * @link http://php.net/manual/en/function.stream-get-contents.php
     */
    public function getContents() : string
    {
        if (!is_resource($this->resource)) {
            throw new UnreadableStreamException('Stream is not resourceable');
        }

        if (!$this->isReadable()) {
            throw new UnreadableStreamException('Stream is not readable');
        }

        $result = stream_get_contents($this->resource);
        if ($result === false) {
            throw new UnreadableStreamException('Unable to read remainder of the stream');
        }

        return $result;
    }

    /**
     * Gets the stream metadata
     *
     * @param string $key
     *
     * @return mixed
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     */
    public function getMetadata($key = null)
    {
        if (!is_resource($this->resource)) {
            return null;
        }

        $metadata = stream_get_meta_data($this->resource);
        if ($key === null) {
            return $metadata;
        }

        return $metadata[$key] ?? null;
    }

    /**
     * Gets the stream size
     *
     * Returns NULL if the stream without a resource,
     * or if the stream size cannot be determined.
     *
     * @return int|null
     *
     * @link http://php.net/manual/en/function.fstat.php
     */
    public function getSize() : ?int
    {
        if (!is_resource($this->resource)) {
            return null;
        }

        /** @var array{size: int}|false */
        $stats = fstat($this->resource);
        if ($stats === false) {
            return null;
        }

        return $stats['size'];
    }

    /**
     * Converts the stream to a string
     *
     * @return string
     *
     * @link http://php.net/manual/en/language.oop5.magic.php#object.tostring
     */
    public function __toString()
    {
        try {
            if ($this->isReadable()) {
                if ($this->isSeekable()) {
                    $this->rewind();
                }

                return $this->getContents();
            }
        } catch (Throwable $e) {
        }

        return '';
    }
}
