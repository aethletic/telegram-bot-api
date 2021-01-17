<?php

use Telegram\Bot;
use Telegram\Update;
use Telegram\Keyboard;
use Telegram\Support\Helpers;
use Chipslays\Collection\Collection;

if (!function_exists('keyboard')) {
    /**
     * Если передать параметры будет вызван метод Keyboard::show().
     * Пустые параметры возвращают объект Keyboard.
     *
     * @param boolean $keyboard
     * @param boolean $oneTime
     * @param boolean $resize
     * @param boolean $selective
     * @return Keyboard|string
     */
    function keyboard($keyboard = false, $oneTime = false, $resize = true, $selective = false)
    {
        if (!func_num_args()) {
            return new Keyboard;
        }

        return Keyboard::show($keyboard, $oneTime, $resize, $selective);
    }
}

if (!function_exists('keyboard_hide')) {
    /**
     * @param boolean $selective
     * @return string
     */
    function keyboard_hide($selective = false)
    {
        return Keyboard::hide($selective);
    }
}

if (!function_exists('keyboard_add')) {
    /**
     * @param array $keyboards
     * @return void
     */
    function keyboard_add($keyboards = [])
    {
        Keyboard::add($keyboards);
    }
}

if (!function_exists('keyboard_set')) {
    /**
     * @param array $keyboards
     * @return void
     */
    function keyboard_set($keyboards = [])
    {
        Keyboard::set($keyboards);
    }
}

if (!function_exists('bot')) {
    /**
     * Если передать параметры будет вызван метод Bot::auth().
     * Пустые параметры возвращают объект Bot.
     *
     * @param string|null $token
     * @param array|null $config
     * @param boolean $migration Осторожно! True - накатить миграцию, False - откатить миграцию
     * @return Bot
     */
    function bot(string $token = null, array $config = null, bool $migration = null)
    {
        return $token === null && $config === null && $migration === null ? Bot::getInstance() : Bot::getInstance()->auth($token, $config, $migration);
    }
}

if (!function_exists('update')) {
    /**
     * Undocumented function
     *
     * @param string $key
     * @param mixed $default
     * @return string|int|Collection
     */
    function update($key = null, $default = null)
    {
        return Update::get($key, $default);
    }
}

if (!function_exists('config')) {
    /**
     * @param string $key
     * @param mixed $default
     * @return string|int|Collection
     */
    function config($key = null, $default = null)
    {
        return Bot::getInstance()->config($key, $default);
    }
}

if (!function_exists('say')) {
    /**
     * @param string $text
     * @param string|array $keyboard
     * @param array $extra
     * @return Collection
     */
    function say($text, $keyboard = null, $extra = [])
    {
        return Bot::getInstance()->say($text, $keyboard, $extra);
    }
}

if (!function_exists('reply')) {
    /**
     * @param string $text
     * @param string|array $keyboard
     * @param array $extra
     * @return Collection
     */
    function reply($text, $keyboard = null, $extra = [])
    {
        return Bot::getInstance()->reply($text, $keyboard, $extra);
    }
}

if (!function_exists('notify')) {
    /**
     * @param string $text
     * @param boolean $showAlert
     * @param array $extra
     * @return Collection
     */
    function notify($text, $showAlert = false, $extra = [])
    {
        return Bot::getInstance()->notify($text, $showAlert, $extra);
    }
}

if (!function_exists('action')) {
    /**
     * @param string $action
     * @param array $extra
     * @return Collection
     */
    function action($action = 'typing', $extra = [])
    {
        return Bot::getInstance()->action($action, $extra);
    }
}

if (!function_exists('dice')) {
    /**
     * @param string $emoji
     * @param string|array $keyboard
     * @param array $extra
     * @return Collection
     */
    function dice($emoji = '🎲', $keyboard = null, $extra = [])
    {
        return Bot::getInstance()->dice($emoji, $keyboard, $extra);
    }
}

if (!function_exists('plural')) {
    /**
     * @param string|int $n
     * @param array $forms
     * @return string
     */
    function plural($n, array $forms)
    {
        return Helpers::plural($n, $forms);
    }
}

if (!function_exists('lang')) {
    /**
     * Undocumented function
     *
     * @param string|int $key
     * @param array $replace
     * @param string $language
     * @return mixed Можно вернуть как строку, массив и прочее.
     */
    function lang($key, array $replace = null, string $language = null)
    {
        return Bot::getInstance()->lang()->get($key, $replace, $language);
    }
}

if (!function_exists('helper')) {
    /**
     * @return \Telegram\Support\Helpers
     */
    function helper()
    {
        return Bot::getInstance()->helper();
    }
}

if (!function_exists('store')) {
    /**
     * @return \Telegram\Modules\Store
     */
    function store()
    {
        return Bot::getInstance()->store();
    }
}

if (!function_exists('cache')) {
    /**
     * @return \Memcached|\Redis
     */
    function cache()
    {
        return Bot::getInstance()->cache();
    }
}

if (!function_exists('state')) {
    /**
     * @return \Telegram\Modules\State
     */
    function state()
    {
        return Bot::getInstance()->state();
    }
}

if (!function_exists('user')) {
    /**
     * @return \Telegram\Modules\User
     */
    function user()
    {
        return Bot::getInstance()->user();
    }
}

if (!function_exists('db')) {
    /**
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Capsule\Manager
     */
    function db($table = null)
    {
        return Bot::getInstance()->db($table);
    }
}

if (!function_exists('log')) {
    /**
     * @return \Telegram\Modules\Log|void
     */
    function log($data = false, $type = 'auto', $postfix = 'bot')
    {
        return $data ? Bot::getInstance()->log()->write($data, $type, $postfix) : Bot::getInstance()->log();
    }
}

if (!function_exists('upload_file')) {
    /**
     * @return mixed
     */
    function upload_file($path = null)
    {
        return $path ? Bot::getInstance()->helper()->upload($path) : false;
    }
}
