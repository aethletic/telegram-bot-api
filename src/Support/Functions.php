<?php

use Telegram\Bot;
use Telegram\Update;
use Telegram\Keyboard;
use Telegram\Support\Helpers;

if (!function_exists('keyboard')) {
    function keyboard($keyboard = false, $oneTime = false, $resize = true, $selective = false)
    {
        if (!func_num_args()) {
            return new Keyboard;
        }

        return Keyboard::show($keyboard, $oneTime, $resize, $selective);
    }
}

if (!function_exists('keyboard_hide')) {
    function keyboard_hide($selective = false)
    {
        return Keyboard::hide($selective);
    }
}

if (!function_exists('keyboard_add')) {
    function keyboard_add($keyboards = [])
    {
        return Keyboard::add($keyboards);
    }
}

if (!function_exists('keyboard_set')) {
    function keyboard_set($keyboards = [])
    {
        return Keyboard::set($keyboards);
    }
}

if (!function_exists('bot')) {
    function bot($token = null, $config = null, $migration = null)
    {
        return !$token && !$config && !$migration ? Bot::getInstance() : Bot::getInstance()->auth($token, $config, $migration);
    }
}

if (!function_exists('update')) {
    function update($key = null, $default = null)
    {
        return Update::get($key, $default);
    }
}

if (!function_exists('config')) {
    function config($key = null, $default = null)
    {
        return Bot::getInstance()->config($key, $default);
    }
}

if (!function_exists('say')) {
    function say($text, $keyboard = null, $extra = [])
    {
        return Bot::getInstance()->say($text, $keyboard, $extra);
    }
}

if (!function_exists('reply')) {
    function reply($text, $keyboard = null, $extra = [])
    {
        return Bot::getInstance()->reply($text, $keyboard, $extra);
    }
}

if (!function_exists('notify')) {
    function notify($text, $showAlert = false, $extra = [])
    {
        return Bot::getInstance()->notify($text, $showAlert, $extra);
    }
}

if (!function_exists('action')) {
    function action($action = 'typing', $extra = [])
    {
        return Bot::getInstance()->action($action, $extra);
    }
}

if (!function_exists('dice')) {
    function dice($emoji = '🎲', $keyboard = null, $extra = [])
    {
        return Bot::getInstance()->dice($emoji, $keyboard, $extra);
    }
}

if (!function_exists('plural')) {
    function plural($n, $forms)
    {
        return Helpers::plural($n, $forms);
    }
}

if (!function_exists('lang')) {
    function lang($key, $replace = null, $language = null)
    {
        return Bot::getInstance()->lang()->get($key, $replace, $language);
    }
}

if (!function_exists('helper')) {
    function helper()
    {
        return Bot::getInstance()->helper();
    }
}

if (!function_exists('store')) {
    function store()
    {
        return Bot::getInstance()->store();
    }
}

if (!function_exists('cache')) {
    function cache()
    {
        return Bot::getInstance()->cache();
    }
}

if (!function_exists('state')) {
    function state()
    {
        return Bot::getInstance()->state();
    }
}

if (!function_exists('user')) {
    function user()
    {
        return Bot::getInstance()->user();
    }
}

if (!function_exists('db')) {
    function db($table = null)
    {
        return Bot::getInstance()->db($table);
    }
}

if (!function_exists('log')) {
    function log($data = false, $type = 'auto', $postfix = 'bot')
    {
        return $data ? Bot::getInstance()->log()->write($data, $type, $postfix) : Bot::getInstance()->log();
    }
}

if (!function_exists('upload_file')) {
    function upload_file($path = null)
    {
        return $path ? Bot::getInstance()->helper()->upload($path) : false;
    }
}
