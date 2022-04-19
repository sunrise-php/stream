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
use Sunrise\Stream\Exception\UnopenableStreamException;

/**
 * Import functions
 */
use function fopen;
use function sprintf;
use function tmpfile;

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
    public function createStreamFromResource($resource) : StreamInterface
    {
        return new Stream($resource);
    }

    /**
     * {@inheritdoc}
     *
     * @throws UnopenableStreamException
     *         If the file cannot be open.
     */
    public function createStreamFromFile(string $filename, string $mode = 'r') : StreamInterface
    {
        $resource = @fopen($filename, $mode);
        if ($resource === false) {
            throw new UnopenableStreamException(sprintf(
                'Unable to open file "%s" in mode "%s"',
                $filename,
                $mode
            ));
        }

        return $this->createStreamFromResource($resource);
    }

    /**
     * Creates a temporary file
     *
     * The temporary file is automatically removed when the stream is closed or the script ends.
     *
     * @param string|null $content
     *
     * @return StreamInterface
     *
     * @throws UnopenableStreamException
     *         If a temporary file cannot be created.
     *
     * @link https://www.php.net/manual/en/function.tmpfile.php
     */
    public function createStreamFromTemporaryFile(?string $content = null) : StreamInterface
    {
        $resource = tmpfile();
        if ($resource === false) {
            throw new UnopenableStreamException('Unable to create temporary file');
        }

        $stream = $this->createStreamFromResource($resource);
        if ($content === null) {
            return $stream;
        }

        $stream->write($content);
        $stream->rewind();

        return $stream;
    }

    /**
     * {@inheritdoc}
     */
    public function createStream(string $content = '') : StreamInterface
    {
        $stream = $this->createStreamFromFile('php://temp', 'r+b');
        if ($content === '') {
            return $stream;
        }

        $stream->write($content);
        $stream->rewind();

        return $stream;
    }
}
