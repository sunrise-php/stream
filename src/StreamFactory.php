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
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

/**
 * StreamFactory
 *
 * @link https://www.php-fig.org/psr/psr-17/
 */
class StreamFactory implements StreamFactoryInterface
{

	/**
	 * {@inheritDoc}
	 */
	public function createStream(string $content = '') : StreamInterface
	{
		$resource = \fopen('php://temp', 'r+b');

		\fwrite($resource, $content);
		\rewind($resource);

		return new Stream($resource);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws Exception\UnopenableStreamException If the given file does not open
	 */
	public function createStreamFromFile(string $filename, string $mode = 'r') : StreamInterface
	{
		// See http://php.net/manual/en/function.fopen.php
		$resource = @ \fopen($filename, $mode);

		if (false === $resource)
		{
			throw new Exception\UnopenableStreamException(
				\sprintf('Unable to open file "%s" in mode "%s"', $filename, $mode)
			);
		}

		return new Stream($resource);
	}

	/**
	 * {@inheritDoc}
	 */
	public function createStreamFromResource($resource) : StreamInterface
	{
		return new Stream($resource);
	}
}
