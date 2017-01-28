[![MIT license](http://img.shields.io/badge/license-MIT-brightgreen.svg)](http://opensource.org/licenses/MIT)
# ExtraMocks v1.0.0 for PHP >= 5.5

## About

ExtraMocks are a tools that give extra functionality for Mocks.

## Main features
- Allow to redefine global function in namespaces.

## Usage

1. Mock global function

\ExtraMocks\Mocks::mockGlobalFunction($fullName, $result, $count = null)

#### \ExtraMocks\Mocks :: mockGlobalFunction ( `string` **$fullName** , `mixed|callable` **$result** [, `int|null` **$count** = null ] )
---
Redefine global function.

##### Method Pameters

1. string **$fullName** - name with namespace of function for redefine
2. mixed|callable **$result** - new function or result
3. int|null **$count**, default = null. Count of mocked calls

#### \ExtraMocks\Mocks :: getCountCalls ( `string` **$fullName** )
---
Get count of mocked calls

##### Method Pameters

1. string **$fullName** - fullname of redefined function


## Examples

```php
namespace A;

class A
{
    public static function string_length($str)
    {
        return strlen($str);
    }
}
```

```php
namespace B;

class B {

    public static function string_length($str)
    {
        return strlen($str);
    }
}
```

```php
namespace Example;

require (__DIR__ . '/../src/autoloader.php');

use ExtraMocks\Mocks;

// 1. Redefine Global Function by Function

\ExtraMocks\Mocks::mockGlobalFunction(
    '\A\strlen',
    function($s) {
        return strlen($s) * 5;
    }
);

echo \A\A::string_length('foo') . PHP_EOL; // 15
echo \B\B::string_length('foo') . PHP_EOL; // 3;

// 2. Redefine Global Function by Result

\ExtraMocks\Mocks::mockGlobalFunction('\A\strlen', 42);

echo \A\A::string_length('foo') . PHP_EOL; // 42;
echo \B\B::string_length('foo') . PHP_EOL; // 3;

// 3. Redefine Global Function by Result once

\ExtraMocks\Mocks::mockGlobalFunction('\A\strlen', 42, 1);

echo \A\A::string_length('foo') . PHP_EOL; // 42;
echo \A\A::string_length('foo') . PHP_EOL; // 3;
echo \B\B::string_length('foo') . PHP_EOL; // 3;

// 3. Get count of calls mocked function

\ExtraMocks\Mocks::mockGlobalFunction('\A\strlen', 42);

echo Mocks::getCountCalls('\A\strlen') . PHP_EOL; // 0
echo \A\A::string_length('foo') . PHP_EOL;        // 42;
echo Mocks::getCountCalls('\A\strlen') . PHP_EOL; // 1
echo \A\A::string_length('foo') . PHP_EOL;        // 42;
echo Mocks::getCountCalls('\A\strlen') . PHP_EOL; // 2
```

## Installation

### Composer

Download composer:

    wget -nc http://getcomposer.org/composer.phar

and add dependency to your project:

    php composer.phar require cheprasov/php-extra-mocks

## Running tests

3. To run tests type in console:

    ./vendor/bin/phpunit

## Something doesn't work

Feel free to fork project, fix bugs and finally request for pull
