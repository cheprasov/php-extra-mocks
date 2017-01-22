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

class Mocks {

    protected static $mockedFunctions = [];

    protected static $mockedFunctionsCalls = [];

    /**
     * @param string $fullName
     * @param callable|mixed $result
     * @param null|int $count
     */
    public static function mockFunction($fullName, $result, $count = null) {
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
                return \\ExtraMocks\\Mocks::invokeMockedFunction(
                    '{$fullName}',
                    '{$name}',
                    func_get_args()
                );
            };";
            eval(implode(PHP_EOL, $eval));
        }
        static::$mockedFunctionsCalls[$fullName] = 0;
        static::$mockedFunctions[$fullName] = [
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
    public static function getCountCalls($fullName) {
        if (empty(static::$mockedFunctionsCalls[$fullName])) {
            return 0;
        }
        return static::$mockedFunctionsCalls[$fullName];
    }

    /**
     * @param $fullName
     * @param $name
     * @param $args
     * @return mixed
     * @throws \Exception
     */
    public static function invokeMockedFunction($fullName, $name, $args) {
        if (!isset(static::$mockedFunctions[$fullName])
            || isset(static::$mockedFunctions[$fullName]['count'])
            && static::$mockedFunctions[$fullName]['count'] == 0
        ) {
            if (is_callable($name)) {
                $e = call_user_func_array('\\' . $name, $args);
                return $e;
            }
            throw new \Exception("Can not to call function '{$name}'");
        }
        $result = static::$mockedFunctions[$fullName]['result'];
        if (is_callable($result)) {
            $result = call_user_func_array($result, $args);
        }
        if (isset(static::$mockedFunctions[$fullName]['count'])) {
            static::$mockedFunctions[$fullName]['count'] -= 1;
        }
        static::$mockedFunctionsCalls[$fullName] += 1;
        return $result;
    }

}
