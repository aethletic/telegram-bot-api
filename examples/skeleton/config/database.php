<?php 

/**
 * Модуль Database.
 * 
 * Позволяет взаимодействовать с базой данных.
 * Используется библиотека от Laravel.
 * 
 * @see https://laravel.com/docs/8.x/database
 */
return [
    'database' => [
        /**
         * Включение/выключение модуля.
         */
        'enable' => false,

        /**
         * Драйвер базы данных.
         * 
         * Поддерживает:
         * string "sqlite"
         * string "mysql"
         */
        'driver' => 'mysql',
        'sqlite' => [
            'database' => '/path/to/database.sqlite',
        ],
        'mysql' => [
            'host'      => 'localhost',
            'database'  => 'telegram',
            'username'  => 'mysql',
            'password'  => 'mysql',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ],
    ],
];