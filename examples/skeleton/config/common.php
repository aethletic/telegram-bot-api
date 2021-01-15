<?php 

/**
 * Общие настройки.
 */
return [
    'common' => [
        /**
         * Временная зона.
         * 
         * bool false - Чтобы не использовать этот параметр
         * 
         * @see https://www.php.net/manual/en/timezones.php
         */
        'timezone' => 'UTC',

        /**
         * Максимальная нагрузка на сервер.
         */
        'max_system_load' => 1,
    ],
];