## Simple Stream wrapper for PHP 7.1+ based on PSR-7 & PSR-17

[![Gitter](https://badges.gitter.im/sunrise-php/support.png)](https://gitter.im/sunrise-php/support)
[![Build Status](https://api.travis-ci.com/sunrise-php/stream.svg?branch=master)](https://travis-ci.com/sunrise-php/stream)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/sunrise-php/stream/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/sunrise-php/stream/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/sunrise-php/stream/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/sunrise-php/stream/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/sunrise/stream/v/stable)](https://packagist.org/packages/sunrise/stream)
[![Total Downloads](https://poser.pugx.org/sunrise/stream/downloads)](https://packagist.org/packages/sunrise/stream)
[![License](https://poser.pugx.org/sunrise/stream/license)](https://packagist.org/packages/sunrise/stream)

## Awards

[![SymfonyInsight](https://insight.symfony.com/projects/a6301a76-9b35-49a3-adb1-ebbf59f810f2/big.svg)](https://insight.symfony.com/projects/a6301a76-9b35-49a3-adb1-ebbf59f810f2)

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

// converts the stream to string
(string) $stream;

// closes the stream
$stream->close();
```

## Test run

```bash
php vendor/bin/phpunit
```

## Api documentation

https://phpdoc.fenric.ru/

## Useful links

* https://www.php-fig.org/psr/psr-7/
* https://www.php-fig.org/psr/psr-17/
