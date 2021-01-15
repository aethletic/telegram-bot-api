<?php 

/**
 * Параметры бота.
 */
return [
    'bot' => [
        /**
         * Токен бота, который был получен у @BotFather.
         */
        'token' => '1234567890:BOT_TOKEN',

        /**
         * Имя бота, например, такое же как указано у @BotFather.
         */
        'name' => 'MyBot',

        /**
         * Юзернейм бота, без собаки (@).
         */
        'username' => 'MyTelegram_bot',

        /**
         * Обработчик Webhook.
         */
        'handler' => 'https://example.com/bot.php',

        /**
         * Версия бота.
         */
        'version' => '1.0.0',
    ],
];