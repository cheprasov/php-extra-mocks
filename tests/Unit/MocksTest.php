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

class A {
    public static function strlen($str) {
        return strlen($str);
    }
}

namespace B;

class B {
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
        $this->assertSame($expect, \B\B::strlen($str));
    }

    public function provider_mockGlobalFunction()
    {
        return [
            ['foo', 6, 3],
            ['bar', 6, 3],
            ['hello', 10, 5],
            ['', null, 0],
        ];
    }

    /**
     * @dataProvider provider_mockGlobalFunction
     */
    public function test_mockGlobalFunction($str, $expect_a, $expect_b) {
        Mocks::mockGlobalFunction(
            '\A\strlen',
            function($str) {
                return ($l = strlen($str)) ? $l * 2 : null;
            }
        );
        $this->assertSame($expect_a, \A\A::strlen($str));
        $this->assertSame($expect_b, \B\B::strlen($str));
        $this->assertSame(1, Mocks::getCountCalls('\A\strlen'));
    }

    public function provider_mockGlobalResult()
    {
        return [
            ['foo', ],
            ['bar'],
            ['hello'],
            [''],
        ];
    }

    /**
     * @dataProvider provider_mockGlobalResult
     */
    public function test_mockGlobalResult($str) {
        Mocks::mockGlobalFunction('\A\strlen', 42);
        $this->assertSame(42, \A\A::strlen($str));
        $this->assertSame(1, Mocks::getCountCalls('\A\strlen'));
    }

    public function test_mockGlobalCount() {
        Mocks::mockGlobalFunction(
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
