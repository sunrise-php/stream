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
use Sunrise\Stream\Exception\InvalidArgumentException;
use Sunrise\Stream\Exception\UnreadableStreamException;
use Sunrise\Stream\Exception\UnseekableStreamException;
use Sunrise\Stream\Exception\UntellableStreamException;
use Sunrise\Stream\Exception\UnwritableStreamException;

/**
 * Stream
 */
class Stream implements StreamInterface
{

	/**
	 * Resource of the stream
	 *
	 * @var resource
	 */
	protected $resource;

	/**
	 * {@inheritDoc}
	 */
	public function __construct($resource)
	{
		if (! \is_resource($resource))
		{
			throw new InvalidArgumentException('Invalid stream resource');
		}

		$this->resource = $resource;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isResourceable() : bool
	{
		return \is_resource($this->resource);
	}

	/**
	 * {@inheritDoc}
	 */
	public function detach()
	{
		$resource = $this->resource;

		$this->resource = null;

		return $resource;
	}

	/**
	 * {@inheritDoc}
	 */
	public function close() : void
	{
		if (! $this->isResourceable())
		{
			return;
		}

		$resource = $this->detach();

		\fclose($resource);
	}

	/**
	 * {@inheritDoc}
	 */
	public function eof() : bool
	{
		if (! $this->isResourceable())
		{
			return true;
		}

		return \feof($this->resource);
	}

	/**
	 * {@inheritDoc}
	 */
	public function tell() : int
	{
		if (! $this->isResourceable())
		{
			throw new UntellableStreamException('Stream is not resourceable');
		}

		$result = \ftell($this->resource);

		if (false === $result)
		{
			throw new UntellableStreamException('Unable to get the stream pointer position');
		}

		return $result;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSeekable() : bool
	{
		if (! $this->isResourceable())
		{
			return false;
		}

		$metadata = \stream_get_meta_data($this->resource);

		return $metadata['seekable'];
	}

	/**
	 * {@inheritDoc}
	 */
	public function rewind() : void
	{
		if (! $this->isResourceable())
		{
			throw new UnseekableStreamException('Stream is not resourceable');
		}

		if (! $this->isSeekable())
		{
			throw new UnseekableStreamException('Stream is not seekable');
		}

		$result = \fseek($this->resource, 0, \SEEK_SET);

		if (! (0 === $result))
		{
			throw new UnseekableStreamException('Unable to move the stream pointer to beginning');
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function seek(int $offset, int $whence = \SEEK_SET) : void
	{
		if (! $this->isResourceable())
		{
			throw new UnseekableStreamException('Stream is not resourceable');
		}

		if (! $this->isSeekable())
		{
			throw new UnseekableStreamException('Stream is not seekable');
		}

		$result = \fseek($this->resource, $offset, $whence);

		if (! (0 === $result))
		{
			throw new UnseekableStreamException('Unable to move the stream pointer to the given position');
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function isWritable() : bool
	{
		if (! $this->isResourceable())
		{
			return false;
		}

		$metadata = \stream_get_meta_data($this->resource);

		return ! (false === \strpbrk($metadata['mode'], '+acwx'));
	}

	/**
	 * {@inheritDoc}
	 */
	public function write(string $string) : int
	{
		if (! $this->isResourceable())
		{
			throw new UnwritableStreamException('Stream is not resourceable');
		}

		if (! $this->isWritable())
		{
			throw new UnwritableStreamException('Stream is not writable');
		}

		$result = \fwrite($this->resource, $string);

		if (false === $result)
		{
			throw new UnwritableStreamException('Unable to write to the stream');
		}

		return $result;
	}

	/**
	 * {@inheritDoc}
	 */
	public function truncate(int $length = 0) : void
	{
		if (! $this->isResourceable())
		{
			throw new UnwritableStreamException('Stream is not resourceable');
		}

		if (! $this->isWritable())
		{
			throw new UnwritableStreamException('Stream is not writable');
		}

		$result = \ftruncate($this->resource, $length);

		if (false === $result)
		{
			throw new UnwritableStreamException('Unable to truncate the stream');
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function isReadable() : bool
	{
		if (! $this->isResourceable())
		{
			return false;
		}

		$metadata = \stream_get_meta_data($this->resource);

		return ! (false === \strpbrk($metadata['mode'], '+r'));
	}

	/**
	 * {@inheritDoc}
	 */
	public function read(int $length) : string
	{
		if (! $this->isResourceable())
		{
			throw new UnreadableStreamException('Stream is not resourceable');
		}

		if (! $this->isReadable())
		{
			throw new UnreadableStreamException('Stream is not readable');
		}

		$result = \fread($this->resource, $length);

		if (false === $result)
		{
			throw new UnreadableStreamException('Unable to read from the stream');
		}

		return $result;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getContents() : string
	{
		if (! $this->isResourceable())
		{
			throw new UnreadableStreamException('Stream is not resourceable');
		}

		if (! $this->isReadable())
		{
			throw new UnreadableStreamException('Stream is not readable');
		}

		$result = \stream_get_contents($this->resource);

		if (false === $result)
		{
			throw new UnreadableStreamException('Unable to read remainder of the stream');
		}

		return $result;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMetadata() : ?array
	{
		if (! $this->isResourceable())
		{
			return null;
		}

		return \stream_get_meta_data($this->resource);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSize() : ?int
	{
		if (! $this->isResourceable())
		{
			return null;
		}

		$stats = \fstat($this->resource);

		if (false === $stats)
		{
			return null;
		}

		return $stats['size'];
	}

	/**
	 * {@inheritDoc}
	 */
	public function toString() : string
	{
		try
		{
			if ($this->isReadable())
			{
				if ($this->isSeekable())
				{
					$this->rewind();
				}

				return $this->getContents();
			}
		}
		catch (\Throwable $e)
		{
			// ignore...
		}

		return '';
	}
}
