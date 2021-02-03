<?php

namespace Telegram\Modules;

use Telegram\Bot;
use Telegram\Update;
use Chipslays\Collection\Collection;

class User
{
    /**
     * @var Bot
     */
    private static $bot;

    private static $driver;

    private static $userId;

    private static $data = [];

    private static $firstTime = false;
    private static $newVersion = false;
    private static $floodTime;

    /**
     * @return void
     */
    public static function initialize()
    {
        self::$bot = Bot::getInstance();

        self::$driver = self::$bot->config('user.driver');

        self::$userId = Update::get('*.from.id', Update::get('*.chat.id'));

        switch (self::$driver) {
            case 'store':
                # code...
                break;

            case 'database':
                # code...
                break;

            default:
                # code...
                break;
        }

        // Юзер существует
        if (self::exists(self::$userId)) {
            self::$data = self::getById(self::$userId);
            self::diffBotVersion();

            if (!self::$data['active']) {
                self::update([
                    'active' => 1,
                ]);
            }

            return;
        }

        /**
         * Вставка нового пользователя
         */

        self::$firstTime = true;

        // получаем источник откуда пришел юзер
        $source = null;
        $text = self::$bot->update('*.text');
        if (Update::isCommand() && $text && stripos($text, '/start') !== false) {
            $text = explode(' ', $text);
            if (is_array($text) && count($text) > 1) {
                unset($text[0]);
                $source = implode(' ', $text);
            }
        }

        // Создаем новую запись о юзере
        $from = new Collection(self::$bot->update('*.from'));
        $firstname = $from->get('first_name', null);
        $lastname = $from->get('last_name', null);

        $data = [
            // Общная информация
            'user_id' => $from->get('id', null), // telegram id юзера
            'active' => 1, // юзер не заблокировал бота
            'fullname' => trim("{$firstname} {$lastname}"), // имя фамилия
            'firstname' => $firstname, // имя
            'lastname' => $lastname, // фамилия
            'username' => $from->get('username', null), // telegram юзернейм
            'lang' => $from->get('language_code', self::$bot->config('localization.default_language', 'en')), // язык

            // Сообщения
            'first_message' => time(), // первое сообщение (дата регистрации) (unix)
            'last_message' => time(), // последнее сообщение (unix)
            'source' => $source, // откуда пользователь пришел (/start botcatalog)

            // Бан
            'banned' => 0, // забанен или нет
            'ban_comment' => null, // комментарий при бане
            'ban_date_from' => null, // бан действует с (unix)
            'ban_date_to' => null, // бан до (unix)

            // Дополнительно
            'role' => 'user', // группа юзера
            'nickname' => null, // никнейм (например для игровых ботов)
            'emoji' => null, // эмодзи/иконка (префикс)

            // Служебное
            'note' => null, // заметка о юзере
            'version' => self::$bot->config('bot.version'), // последняя версия бота с которой взаимодействовал юзер
        ];

        switch (self::$driver) {
            case 'store':
                self::update($data);
                break;

            case 'database':
                self::$bot->db()
                    ->table('users')
                    ->insert($data);
                break;
        }


        self::$data = self::getById(self::$userId);
    }

    public static function isFlood(): bool
    {
        $timeout = self::$bot->config('user.flood_time');
        $diffMessageTime = time() - self::$data['last_message'];

        $timeout = self::$bot->config('user.flood_time');

        self::$floodTime = $timeout - $diffMessageTime;

        return $diffMessageTime <= $timeout;
    }

    public static function getFloodTime()
    {
        return self::$floodTime;
    }

    public static function firstTime()
    {
        return self::$firstTime;
    }

    public static function newVersion()
    {
        return self::$newVersion;
    }

    public static function isBanned(): bool
    {
        return self::$data['banned'] == 1;
    }

    public static function isAdmin(): bool
    {
        $adminList = (array) self::$bot->config('admin.list', []);
        if (array_key_exists(Update::get('*.from.id'), $adminList) || array_key_exists(Update::get('*.from.id'), $adminList)) {
            return true;
        }
        return false;
    }

    public static function save(): void
    {
        $data = self::$data;

        unset($data['id']);

        self::update($data);
    }

    public static function get($key, $default = null)
    {
        return isset(self::$data[$key]) ? self::$data[$key] : $default;
    }

    public static function update(array $data = [])
    {
        return self::updateById(self::$userId, $data);
    }

    /**
     * @param int|string $userId
     */
    public static function getById($userId = null)
    {
        if (!$userId) {
            return;
        }

        switch (self::$driver) {
            case 'store':
                return Store::get(self::filename($userId));
                break;

            case 'database':
                return (array) self::$bot->db()
                    ->table('users')
                    ->where('user_id', $userId)
                    ->first();
                break;

            default:
                # code...
                break;
        }
    }

    public static function updateById($userId = null, array $data = [])
    {
        if (!$userId) {
            return;
        }

        switch (self::$driver) {
            case 'store':
                return Store::set(self::filename($userId), self::merge($data));
                break;

            case 'database':
                return self::$bot->db()
                    ->table('users')
                    ->where('user_id', $userId)
                    ->update($data);
                break;

            default:
                # code...
                break;
        }
    }

    /**
     * @param int|string $userId
     */
    public static function exists($userId = null)
    {
        if (!$userId) {
            return;
        }

        switch (self::$driver) {
            case 'store':
                return Store::has(self::filename($userId));
                break;

            case 'database':
                return self::$bot->db()
                    ->table('users')
                    ->where('user_id', $userId)
                    ->exists();
                break;

            default:
                # code...
                break;
        }
    }

    public function __get($key)
    {
        return self::get($key);
    }

    public function __set($key, $value)
    {
        self::$data[$key] = $value;
    }

    private static function merge(array $data): array
    {
        self::$data = array_merge(self::$data, $data);
        return self::$data;
    }

    private static function diffBotVersion(): void
    {
        $userVersion = (string) self::get('version');
        $currentVersion = (string) self::$bot->config('bot.version');

        self::$newVersion = $userVersion !== $currentVersion;

        if (self::$newVersion) {
            self::update(['version' => $currentVersion]);
            self::$data['version'] = $currentVersion;
        }
    }

    private static function filename($userId): string
    {
        return "{$userId}__USER__DATA_ID";
    }
}
