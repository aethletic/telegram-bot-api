<?php

namespace Telegram\Modules;

use Illuminate\Database\Capsule\Manager as Capsule;
// use Database\Connectors\ConnectionFactory;
use Telegram\Bot;

class Database
{
    private static $db;

    public static function connect(): Capsule
    {
        $bot = Bot::getInstance();

        $driver = $bot->config('database.driver');
        $config = $bot->config('database.' . $driver);
        $config['driver'] = $driver;

        // self::$db = (new ConnectionFactory)->make($config);
        // return self::$db;

        $capsule = new Capsule;
        $capsule->addConnection($config);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        self::$db = $capsule;
        return self::$db;
    }

    public static function table(string $table)
    {
        return self::$db->table($table);
    }
}
