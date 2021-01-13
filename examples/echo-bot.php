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
 * NULL, пустой строке, "{any}" или "//iu"
 */
$bot->hear('', fn () => say(update('message.text')));

$bot->run();
