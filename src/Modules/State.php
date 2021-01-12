<?php

namespace Telegram\Modules;

use Telegram\Bot;
use Telegram\Update;

class State
{
    public static $name = null;
    public static $data = null;

    /**
     * User ID from update.
     *
     * @var integer
     */
    private static $currentUserId;

    /**
     * @var Bot
     */
    private static $bot;

    /**
     * @var Illuminate\Database\Capsule\Manager
     */
    private static $db;

    private static $driver;

    public static function initialize()
    {
        self::$bot = Bot::getInstance();

        if (!self::$bot->config('store.enable')) {
            throw new \Exception("Please set enable first `store.enable`.");
        }

        self::$currentUserId = Update::get('*.from.id');

        $state = self::getById(self::$currentUserId);
        self::$name = $state['name'] ?? null;
        self::$data = $state['data'] ?? null;
    }

    public static function get()
    {
        return self::$currentUserId ? self::getById(self::$currentUserId) : false;
    }

    public static function getById($userId)
    {
        return Bot::getInstance()->store()->get(self::userStateFile($userId));
    }

    public static function set($name = null, $data = null)
    {
        self::setById(self::$currentUserId, $name, $data);
    }

    public static function save()
    {
        self::setById(self::$currentUserId, self::$name, self::$data);
    }

    public static function setById($userId, $name = null, $data = null)
    {
        return Bot::getInstance()->store()->set(self::userStateFile($userId), [
            'name' => $name, 
            'data' => $data
        ]);
    }

    public static function clear()
    {
        self::clearById(self::$currentUserId);
    }

    public static function clearById($userId)
    {
        return Bot::getInstance()->store()->delete(self::userStateFile($userId));
    }

    public static function setName($name)
    {
        return Bot::getInstance()->store()->set(self::userStateFile(self::$currentUserId), [
            'name' => $name, 
            'data' => self::$data, 
        ]);
    }

    public static function setData($data)
    {
        return Bot::getInstance()->store()->set(self::userStateFile(self::$currentUserId), [
            'name' => self::$name, 
            'data' => $data,
        ]);
    }

    private static function userStateFile($userId)
    {
        return "{$userId}__USER__STATE__ID";
    }
}