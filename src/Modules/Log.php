<?php

namespace Telegram\Modules;

class Log
{
    private static $dir;

    /**
     * @param string $dir
     *
     * @return false|null
     */
    public static function initialize(string $dir)
    {
        if (!$dir) {
            return false;
        }

        if (!file_exists($dir)) {
            throw new \Exception("Log directory `{$dir}` not exists.");
        }

        self::$dir = rtrim($dir, '/');
    }

    /**
     * @return void
     */
    public static function write($data = false, $type = 'auto', $postfix = 'bot')
    {
        if (!$data) {
            return;
        }

        $date = date("d.m.Y, H:i:s");
        $data = is_array($data) ? json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : trim($data);
        $log = "[{$date}] [{$type}]\n{$data}";

        $filename = date("d-m-Y") . "_{$postfix}.log";

        file_put_contents(self::$dir . "/{$filename}", $log . PHP_EOL, FILE_APPEND);
    }
}
