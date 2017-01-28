<?php
/**
 * This file is part of ExtraMocks.
 * git: https://github.com/cheprasov/php-extra-mocks
 *
 * (C) Alexander Cheprasov <cheprasov.84@ya.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A;

class A
{
    public static function string_length($str)
    {
        return strlen($str);
    }
}

namespace B;

class B {

    public static function string_length($str)
    {
        return strlen($str);
    }
}

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
