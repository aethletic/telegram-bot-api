<?php

use Telegram\Support\Debug;  // TODO remove

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/../../vendor/autoload.php'; // TODO remove

$config = [];
foreach (glob(__DIR__ . '/config/*.php') as $file) {
    $config = array_merge($config, require $file);
}

$bot = bot($config['bot']['token'], $config);

$bot->webhook(Debug::MESSAGE);  // TODO remove

foreach (glob(__DIR__ . '/app/keyboards/*.keyboard.php') as $keyboard) {
    keyboard_add(require $keyboard);
}

foreach (glob(__DIR__ . '/app/middlewares/*.middleware.php') as $middleware) {
    require $middleware;
}

require __DIR__ . '/app/brain.php';

$bot->run();
