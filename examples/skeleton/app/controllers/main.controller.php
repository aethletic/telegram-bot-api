<?php 

use Telegram\Bot;
use Chipslays\Collection\Collection;

class MainController
{
    public static function start(Bot $bot, Collection $update, $source)
    {
        say(lang('START'));
    }

    public static function echo(Bot $bot, Collection $update, $message)
    {
        say($message);
    }
}