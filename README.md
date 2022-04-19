## Stream wrapper for PHP 7.1+ based on PSR-7 and PSR-17

[![Gitter](https://badges.gitter.im/sunrise-php/support.png)](https://gitter.im/sunrise-php/support)
[![Build Status](https://circleci.com/gh/sunrise-php/stream.svg?style=shield)](https://circleci.com/gh/sunrise-php/stream)
[![Code Coverage](https://scrutinizer-ci.com/g/sunrise-php/stream/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/sunrise-php/stream/?branch=main)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/sunrise-php/stream/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/sunrise-php/stream/?branch=main)
[![Total Downloads](https://poser.pugx.org/sunrise/stream/downloads?format=flat)](https://packagist.org/packages/sunrise/stream)
[![Latest Stable Version](https://poser.pugx.org/sunrise/stream/v/stable?format=flat)](https://packagist.org/packages/sunrise/stream)
[![License](https://poser.pugx.org/sunrise/stream/license?format=flat)](https://packagist.org/packages/sunrise/stream)

## Installation

```bash
composer require sunrise/stream
```

## How to use?

```php
use Sunrise\Stream\Stream;
use Sunrise\Stream\StreamFactory;

// creates a new stream without a factory
$stream = new Stream(fopen(...));

// creates a new stream from the given string
$stream = (new StreamFactory)->createStream('Hello, world!');

// creates a new stream from the given filename or URI
$stream = (new StreamFactory)->createStreamFromFile('http://php.net/', 'rb');

// creates a new stream from the given resource
$stream = (new StreamFactory)->createStreamFromResource(fopen(...));

// creates a new stream from temporary file (available from version 1.3)
$stream = (new StreamFactory)->createStreamFromTemporaryFile(?string);

// converts the stream to string
(string) $stream;

// closes the stream
$stream->close();
```

---

## Test run

```bash
php vendor/bin/phpunit
```

## Useful links

* https://www.php-fig.org/psr/psr-7/
* https://www.php-fig.org/psr/psr-17/
