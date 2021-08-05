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
     * {@inheritdoc}
     */
    public function createStream(string $content = '') : StreamInterface
    {
        $resource = \fopen('php://temp', 'r+b');

        \fwrite($resource, $content);
        \rewind($resource);

        return new Stream($resource);
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception\UnopenableStreamException If the given file does not open
     */
    public function createStreamFromFile(string $filename, string $mode = 'r') : StreamInterface
    {
        // See http://php.net/manual/en/function.fopen.php
        $resource = @ \fopen($filename, $mode);

        if (false === $resource) {
            throw new Exception\UnopenableStreamException(
                \sprintf('Unable to open file "%s" in mode "%s"', $filename, $mode)
            );
        }

        return new Stream($resource);
    }

    /**
     * {@inheritdoc}
     */
    public function createStreamFromResource($resource) : StreamInterface
    {
        return new Stream($resource);
    }

    /**
     * Creates temporary file
     *
     * The temporary file is automatically removed when the stream is closed or the script ends.
     *
     * It isn't the PSR-7 method.
     *
     * @link https://www.php.net/manual/en/function.tmpfile.php
     *
     * @param null|string $content
     *
     * @return StreamInterface
     *
     * @throws Exception\UnopenableStreamException
     */
    public function createStreamFromTemporaryFile(?string $content = null) : StreamInterface
    {
        $resource = \tmpfile();
        if (false === $resource) {
            throw new Exception\UnopenableStreamException('Unable to create temporary file');
        }

        if (null !== $content) {
            \fwrite($resource, $content);
            \rewind($resource);
        }

        return new Stream($resource);
    }
}
