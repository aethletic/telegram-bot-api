<?php

require "../vendor/autoload.php";

/**
 * Авторизация бота + получаем обновление от Telegram
 * 
 * @var \Telegram\Bot $bot
 */
$bot = bot('1234567890:BOT_TOKEN', require 'config.php')->webhook();

/**
 * Чтобы обработать любое сообщение первый параметр должен быть равен:
 * NULL, пустой строке, или "//iu"
 * Так же можно передать {название}, текст сообщения будет автоматически передан в параметры функции
 */
$bot->hear('{message}', fn ($message) => say($message));

$bot->run();
