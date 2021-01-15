<?php 

/**
 * Модуль Cache.
 * 
 * Повзоляет взаимодействовать с Memcached и Redis.
 * Внимение, у вас должно быть установленно Memcached и/или Redis.
 */
return [
    'cache' => [
        /**
         * Включение/выключение модуля.
         */
        'enable' => false,

         /**
         * Драйвер кеша.
         *
         * Поддерживает:
         * string "memcached"
         * string "redis"
         * 
         * @see https://www.php.net/manual/ru/book.memcached.php
         * @see https://github.com/phpredis/phpredis
         */
        'driver' => 'memcached',

        'memcached' => [
            'host'  => 'localhost',
            'port' => '11211',
        ],

        'redis' => [
            'host'  => '127.0.0.1',
            'port' => '6379',
        ],
    ],
];