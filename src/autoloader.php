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
namespace ExtraMocks;

spl_autoload_register(
    function($class) {
        if (0 !== strpos($class, __NAMESPACE__ . '\\')) {
            return;
        }
        $classPath = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
        if (is_file($classPath)) {
            include $classPath;
        }
    },
    false,
    true
);
