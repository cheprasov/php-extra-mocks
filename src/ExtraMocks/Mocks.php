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
namespace ExtraMocks;

use ExtraMocks\Exception\FunctionNotFoundException;

class Mocks
{
    const VERSION = '1.0.0';

    /**
     * @var array
     */
    protected static $mockedGlobalFunctions = [];

    /**
     * @var array
     */
    protected static $mockedGlobalFunctionsCalls = [];

    /**
     * @param string $fullName
     * @param callable|mixed $result
     * @param null|int $count
     */
    public static function mockGlobalFunction($fullName, $result, $count = null)
    {
        if (false !== ($pos = strrpos($fullName, '\\'))) {
            $namespace = trim(substr($fullName, 0, $pos), '\\');
            $name = substr($fullName, $pos + 1);
        } else {
            $namespace = '';
            $name = $fullName;
        }

        if (!function_exists($fullName)) {
            $eval = [];
            if ($namespace) {
                $eval[] = "namespace {$namespace};";
            }
            $eval[] = "function {$name}() {
                return \\ExtraMocks\\Mocks::_invokeMockedGlobalFunction('{$fullName}', '{$name}', func_get_args());
            };";
            eval(implode(PHP_EOL, $eval));
        }
        static::$mockedGlobalFunctionsCalls[$fullName] = 0;
        static::$mockedGlobalFunctions[$fullName] = [
            'full_name' => $fullName,
            'name' => $name,
            'namespace' => $namespace,
            'result' => $result,
            'count' => $count,
        ];
    }

    /**
     * Get counts of calls of mocked function
     * @param string $fullName
     * @return int
     */
    public static function getCountCalls($fullName)
    {
        if (empty(static::$mockedGlobalFunctionsCalls[$fullName])) {
            return 0;
        }
        return static::$mockedGlobalFunctionsCalls[$fullName];
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([static::class, ltrim($name, '_')], $arguments);
    }

    /**
     * @param $fullName
     * @param $name
     * @param $args
     * @return mixed
     * @throws \Exception
     */
    protected static function invokeMockedGlobalFunction($fullName, $name, $args)
    {
        if (!isset(static::$mockedGlobalFunctions[$fullName])
            || isset(static::$mockedGlobalFunctions[$fullName]['count'])
            && static::$mockedGlobalFunctions[$fullName]['count'] == 0
        ) {
            if (is_callable($name)) {
                $e = call_user_func_array('\\' . $name, $args);
                return $e;
            }
            throw new FunctionNotFoundException("Can not to call function '{$name}'");
        }
        $result = static::$mockedGlobalFunctions[$fullName]['result'];
        if (is_callable($result)) {
            $result = call_user_func_array($result, $args);
        }
        if (isset(static::$mockedGlobalFunctions[$fullName]['count'])) {
            static::$mockedGlobalFunctions[$fullName]['count'] -= 1;
        }
        static::$mockedGlobalFunctionsCalls[$fullName] += 1;
        return $result;
    }
}
