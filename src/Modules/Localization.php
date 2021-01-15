<?php

namespace Telegram\Modules;

use Telegram\Bot;

class Localization
{
    private static $data = [];

    private static $language;
    private static $default;

    /**
     * @param string $language
     * @param string $default
     * @return void
     */
    public static function load(string $language, string $default = 'en', $path = null)
    {
        $path = $path ?: rtrim(Bot::getInstance()->config('localization.dir'), '\/');

        if (!$path) {
            return;
        }

        $file = "{$path}/{$language}.php";

        self::$language = $language;
        self::$default = $default;
    
        if (!file_exists($file)) {
            
            self::$language = $default;
            $file = "{$path}/{$default}.php";
            if (!file_exists($file)) {
                return;
            }
        }

        self::$data[self::$language] = require $file;
    }

    /**
     * Дополняет локализацю для языка юзера, иначе вызывает дефолтынй язык. 
     *
     * @param string $path
     * @param string $default Если null - будет использован дефолтный язык бота из конфига
     * @return void
     */
    public static function merge(string $path = null, string $default = null)
    {
        if (!$path) {
            return;
        }

        $language = self::$language;
        $default = $default ?: self::$default;

        $file = "{$path}/{$language}.php";

        self::$language = $language;

        if (!file_exists($file)) {
            self::$language = $default;
            $file = "{$path}/{$default}.php";
            if (!file_exists($file)) {
                return;
            }
        }

        if (!array_key_exists($language, self::$data)) {
            self::$data[self::$language] = [];
        }

        self::$data[self::$language] = array_merge(self::$data[self::$language], require $file);
    }

    public static function get($key, $replace = null, $language = null)
    {
        $language = $language ?? self::$language;

        if (!array_key_exists($language, self::$data)) {
            return;
        }

        if (!array_key_exists($key, self::$data[$language])) {
            return;
        }

        $text = self::$data[$language][$key];

        return $replace ? strtr($text, $replace) : $text;
    }
}
