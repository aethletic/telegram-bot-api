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
    public static function load(string $language, string $default = 'en')
    {
        $path = rtrim(Bot::getInstance()->config('localization.dir'), '\/');

        if (!$path) {
            return;
        }

        $file = "{$path}/{$language}.php";

        self::$language = $language;

        if (!file_exists($file)) {
            self::$language = $default;
            $file = "{$path}/{$default}.php";
            if (!file_exists($file)) {
                return;
            }
        }

        self::$default = $default;

        self::$data[self::$language] = require $file;
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