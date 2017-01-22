<?php
/**
 * This file is part of RedisClient.
 * git: https://github.com/cheprasov/php-extra-mocks
 *
 * (C) Alexander Cheprasov <cheprasov.84@ya.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace A;

class A {
    public static function strlen($str) {
        return strlen($str);
    }
}

namespace Tests\Unit;
use ExtraMocks\Mocks;

/**
 * @see \ExtraMocks\Mocks
 * @runInSeparateProcess
 * @runTestsInSeparateProcesses
 */
class MocksTest extends \PHPUnit_Framework_TestCase {

    public function provider_noMock()
    {
        return [
            ['foo', 3],
            ['bar', 3],
            ['hello', 5],
            ['', 0],
        ];
    }

    /**
     * @dataProvider provider_noMock
     */
    public function test_noMock($str, $expect) {
        $this->assertSame($expect, \A\A::strlen($str));
    }

    public function provider_mockFunction()
    {
        return [
            ['foo', 6],
            ['bar', 6],
            ['hello', 10],
            ['', null],
        ];
    }

    /**
     * @dataProvider provider_mockFunction
     */
    public function test_mockFunction($str, $expect) {
        Mocks::mockFunction(
            '\A\strlen',
            function($str) {
                return ($l = strlen($str)) ? $l * 2 : null;
            }
        );
        $this->assertSame($expect, \A\A::strlen($str));
        $this->assertSame(1, Mocks::getCountCalls('\A\strlen'));
    }

    public function provider_mockResult()
    {
        return [
            ['foo'],
            ['bar'],
            ['hello'],
            [''],
        ];
    }

    /**
     * @dataProvider provider_mockResult
     */
    public function test_mockResult($str) {
        Mocks::mockFunction('\A\strlen', 42);
        $this->assertSame(42, \A\A::strlen($str));
        $this->assertSame(1, Mocks::getCountCalls('\A\strlen'));
    }

    public function test_mockCount() {
        Mocks::mockFunction(
            '\A\strlen',
            function($str) {
                return ($l = strlen($str)) ? $l * 2 : null;
            },
            3
        );
        $data = [
            ['foo', 6],
            ['bar', 6],
            ['hello', 10],
            ['foo', 3],
            ['bar', 3],
            ['hello', 5],
        ];
        foreach ($data as $d) {
            $this->assertSame($d[1], \A\A::strlen($d[0]));
        }
        $this->assertSame(3, Mocks::getCountCalls('\A\strlen'));
    }
}
