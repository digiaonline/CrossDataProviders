<p align="center"><a href="https://t-regx.com/"><img src="t.regx.png"></a></p>

# Cross @dataProviders

Handy `require-dev` testing tool for [PhpUnit](https://github.com/sebastianbergmann/phpunit).

It allows you to create square matrices of your data providers!

[![Build Status](https://travis-ci.org/T-Regx/CrossDataProviders.svg?branch=master)](https://travis-ci.org/T-Regx/CrossDataProviders)
[![Coverage Status](https://coveralls.io/repos/github/T-Regx/CrossDataProviders/badge.svg?branch=master)](https://coveralls.io/github/T-Regx/CrossDataProviders?branch=master)
[![Dependencies](https://img.shields.io/badge/dependencies-0-brightgreen.svg)](https://github.com/T-Regx/CrossDataProviders)
[![Repository Size](https://github-size-badge.herokuapp.com/T-Regx/CrossDataProviders.svg)](https://github.com/T-Regx/CrossDataProviders)
[![License](https://img.shields.io/github/license/T-Regx/CrossDataProviders.svg)](https://github.com/T-Regx/CrossDataProviders)
[![Composer lock](https://img.shields.io/badge/.lock-uncommited-green.svg)](https://github.com/T-Regx/CrossDataProviders)

[![PHP Version](https://img.shields.io/badge/PHP-5.6-blue.svg)](https://travis-ci.org/T-Regx/CrossDataProviders)
[![PHP Version](https://img.shields.io/badge/PHP-7.0-blue.svg)](https://travis-ci.org/T-Regx/CrossDataProviders)
[![PHP Version](https://img.shields.io/badge/PHP-7.1-blue.svg)](https://travis-ci.org/T-Regx/CrossDataProviders)
[![PHP Version](https://img.shields.io/badge/PHP-7.2-blue.svg)](https://travis-ci.org/T-Regx/CrossDataProviders)
[![PHP Version](https://img.shields.io/badge/PHP-7.3-blue.svg)](https://travis-ci.org/T-Regx/CrossDataProviders)
[![PHP Version](https://img.shields.io/badge/PHP-7.4-blue.svg)](https://travis-ci.org/T-Regx/CrossDataProviders)

[![PRs Welcome](https://img.shields.io/badge/PR-welcome-brightgreen.svg?style=popout)](http://makeapullrequest.com)

1. [Installation](#installation)
    * [Composer](#installation)
2. [Examples](#examples)
4. [Supported PHP versions](#supported-php-versions)

# Installation

Installation for PHP 7.0 and later:

Add git@github.com:digiaonline/CrossDataProviders.git repository to composer.json

```bash
$ composer require --dev rawr/cross-data-providers
```

# Examples

Ever wanted to use multiple PhpUnit @dataProvider's with each other? Well, look no more :) 

Imagine you have a service that allows you to log in to GitHub, BitBucket, SourceForge and GitLab with either SSH, HTTP or HTTPS and you want to test each **possible configuration of those**.

```php
/**
 * @test
 * @dataProvider services
 */
public function shouldLogin(string $service, string $method) {
    // given
    $login = new Login($method);
    
    // when
    $result = $login->log($service);
    
    // then
    $this->assertTrue($result);
}

function services() {
    return DataProviders::crossAll(
      [
        ['github.com'],
        ['bitbucket.com'],
        ['gitlab.com'],
        ['sourceforge.net']
      ],
      [
        ['http'],
        ['https'],
        ['ssh']
      ]
    );
}
```

This is equivalent of having a regular dataProvider that looks like this
```php
function services() {
    return [
        ['github.com', 'http'],
        ['github.com', 'https'],
        ['github.com', 'ssh'],
        ['bitbucket.com', 'http'],
        ['bitbucket.com', 'https'],
        ['bitbucket.com', 'ssh'],
        ['gitlab.com', 'http'],
        ['gitlab.com', 'https'],
        ['gitlab.com', 'ssh'],
        ['sourceforge.net', 'http'],
        ['sourceforge.net', 'https'],
        ['sourceforge.net', 'ssh'],
    ];
}
```

## More advanced example

Let's say that apart from the domain and the protocol, you'd also like to add the protocol port, and the service title. Further more, you'd like to have three strategies of connection: lazy, eager and a test dry run.

```php
/**
 * @test
 * @dataProvider services
 */
public function shouldLogin(string $service, string $title, string $method, int $port, $strategy) {
    // given
    $login = new Login($method, $port);
    $login->useStrategy($strategy);
    
    // when
    $result = $login->log($service);
    
    // then
    $this->assertTrue($result, "Failed to login to $title");
}

function services() {
    return DataProviders::crossAll(
      [
        // First two paramters: $service and $title
        ['github.com',      'GitHub'],
        ['bitbucket.com',   'BitBucket'],
        ['gitlab.com',      'GitLab'],
        ['sourceforge.net', 'SourceForge'],
        ['www.gitkraken.com', 'Git Kraken']
      ],
      [
        // Second pair of parameters: $method and $port
        ['http',  80],
        ['https', 443],
        ['ssh',   22]
      ],
      [
        // Last parameter: $strategy
        new EagerStrategy(),
        new LazyStrategy(),
        new DryRunStrategy(),
      ]
    );
}
```

This is equal to a @dataProvider with 45 entries. The test will be run 45 times, each time with a unique combination of your parameter sets :)
