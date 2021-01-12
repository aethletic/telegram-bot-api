<?php

namespace Telegram\Modules;

use Telegram\Bot;

class Store
{
    private static $data = [];
    private static $driver;
    private static $dir;
    private static $db;
    private static $bot;
    private static $personalId;

    public static function initialize()
    {
        self::$bot = Bot::getInstance();
        self::$driver = strtolower(self::$bot->config('store.driver'));

        self::$personalId = self::$bot->update('*.from.id', self::$bot->update('*.chat.id'));

        switch (self::$driver) {
            case 'file':
                self::$dir = rtrim(self::$bot->config('store.file.dir'), '\/');
                break;

            case 'database':
                self::$db = self::$bot->db();
                break;

            default:
                // code ...
                break;
        }
    }

    /**
     * @param $key
     * @param $value
     * @param boolean $personal True привязано к пользовтаелю, false глобально
     * @return void
     */
    public static function set($key, $value, bool $personal = false)
    {
        $key = $personal ? self::$personalId.$key : $key;

        switch (self::$driver) {
            case 'file':
                file_put_contents(self::$dir . '/' . md5($key), serialize($value));
                break;

            case 'database':
                self::has($key) ? self::$db->table('store')->where('name', md5($key))->update(['name' => md5($key), 'value' => base64_encode(serialize($value))]) : self::$db->table('store')->insert(['name' => md5($key), 'value' => base64_encode(serialize($value))]);
                break;

            default:
                self::$data[md5($key)] = $value;
                break;
        }
    }

    public static function get($key, $default = null, $personal = false)
    {
        $key = $personal ? self::$personalId.$key : $key;

        switch (self::$driver) {
            case 'file':
                return self::has($key) ? unserialize(file_get_contents(self::$dir . '/' . md5($key))) : $default;
                break;

            case 'database':
                return self::has($key) ? unserialize(base64_decode(self::$db->table('store')->select('value')->where('name', md5($key))->first()->value)) : $default;
                break;

            default:
                return self::has($key) ? self::$data[md5($key)] : $default;
                break;
        }
    }

    public static function has($key, $personal = false)
    {
        $key = $personal ? self::$personalId.$key : $key;

        
        switch (self::$driver) {
            case 'file':
                return file_exists(self::$dir . '/' . md5($key));
                break;

            case 'database':
                return self::$db->table('store')->where('name', md5($key))->exists();
                break;

            default:
                return array_key_exists(md5($key), self::$data);
                break;
        }
    }

    public static function delete($key, $personal = false)
    {
        $key = $personal ? self::$personalId.$key : $key;

        switch (self::$driver) {
            case 'file':
                self::has($key) ? unlink(self::$dir . '/' . md5($key)) : false;
                break;

            case 'database':
                self::has($key) ? self::$db->table('store')->where('name', md5($key))->delete() : false;
                break;

            default:
                unset(self::$data[md5($key)]);
                break;
        }
    }
}
