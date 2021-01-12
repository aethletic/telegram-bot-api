<?php

namespace Telegram\Modules;

use Telegram\Bot;

class Cache
{
    public static function initialize()
    {
        if (!$config = Bot::getInstance()->config('cache')) {
            return;
        }

        $driver = $config['driver'];

        switch ($driver) {
            case 'memcached':
                if (!class_exists('Memcached')) {
                    return false;
                }

                $cache = new \Memcached();
                $cache->addServer($config[$driver]['host'], $config[$driver]['port']);

                break;

            case 'redis':
                if (!class_exists('Redis')) {
                    return false;
                }

                $cache = new \Redis();
                $cache->connect($config[$driver]['host'], $config[$driver]['port']);

                break;

            default:
                $cache = false;
                break;
        }

        return $cache;
    }
}
