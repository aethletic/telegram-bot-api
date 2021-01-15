<?php

namespace Telegram;

use Container\Container;
use Chipslays\Collection\Collection;
use Telegram\Traits\Telegram\Aliases as TelegramAliases;
use Telegram\Traits\Telegram\Methods;
use Telegram\Traits\Telegram\Replies;
use Telegram\Traits\Router;
use Telegram\Traits\Request;
use Telegram\Traits\Aliases as BotAliases;
use Telegram\Traits\Common;
use Telegram\Support\Helpers;
use Telegram\Modules\Store;
use Telegram\Modules\Log;
use Telegram\Modules\State;
use Telegram\Modules\Localization;
use Telegram\Modules\Cache;
use Telegram\Modules\User;
use Telegram\Modules\Database;
use Telegram\Database\Migration;

class Bot extends Container
{
    use Request;
    use TelegramAliases;
    use BotAliases;
    use Methods;
    use Replies;
    use Router;
    use Common;

    /**
     * Секретный токен бота
     * 
     * @var string
     */
    private $token;

    /**
     * Полная конфигурация бота со всеми параметрами.
     * Можно скопировать в отдельный файл и подключать его вторым парамтром, как вариант.
     * 
     * @var array
     */
    private $config = [

        /**
         * Параметры бота.
         */
        'bot' => [

            /**
             * Токен бота, который был получен у @BotFather.
             */
            'token' => '1234567890:ABC_TOKEN',

            /**
             * Имя бота, например, такое же как указано у @BotFather.
             */
            'name' => 'MyBot',

            /**
             * Юзернейм бота, без собаки (@).
             */
            'username' => 'MyTelegram_bot',

            /**
             * Обработчик Webhook.
             */
            'handler' => 'https://example.com/bot.php',

            /**
             * Версия бота.
             */
            'version' => '1.0.0',
        ],

        /**
         * Общие настройки.
         */
        'common' => [

            /**
             * Временная зона.
             * 
             * bool false - Чтобы не использовать этот параметр
             * 
             * @see https://www.php.net/manual/en/timezones.php
             */
            'timezone' => 'UTC',

            /**
             * Максимальная нагрузка на сервер.
             */
            'max_system_load' => 1,
        ],

        /**
         * Параметры Telegram.
         */
        'telegram' => [

            /** 
             * Разметка текста по умолчанию.
             * Если нужно отправить сообщение с другой разметкой, используйте параметр:
             * $extra = ['parse_mode' => 'markdown']
             */
            'parse_mode' => 'html',

            /**
             * "Шифрование" параметра `callback_data` у inline-клавиатуры .
             * NOTE: не храните важные данные в этом параметре, т. к. его можно посмотреть на стороне юзера.
             * safe_callback сделан для усложнения распознования, но не гарантирует 100% зашиты. 
             * 
             * Поддерживает: 
             * bool false - ничего не использовать (unsafe)
             * string "encode" - пережатая строка в base64 (unsafe)
             * string "hash" - хеширование строки в md5 в базе данных, при получении - сверка (safe)
             */
            'safe_callback' => 'encode',
        ],

        /**
         * Параметры администратора.
         */
        'admin' => [

            /**
             * Список администраторов, где ключ - юзернейм или ID юзера, а значение - пароль.
             */
            'list' => [
                'chipslays' => 'password',
                '436432850' => 'password',
            ]
        ],

        /**
         * Модуль User.
         * 
         * Позволяет взаимодействовать с юзером.
         * Хранение данных, управление, бан, флуд и прочее.
         */
        'user' => [

            /**
             * Включение/выключение модуля.
             */
            'enable' => false,

            /**
             * Метод хранения данных.
             * 
             * Поддерживает:
             * string "store" - хранение с помощью модуля Store.
             * string "database" - хранение в отдельной таблице (users) в базе данных.
             */
            'driver' => 'store',

            /**
             * Через сколько секунд должно быть обработано следующее сообщение от юзера.
             */
            'flood_time' => 1,
        ],

        /**
         * Модуль Database.
         * 
         * Позволяет взаимодействовать с базой данных.
         * Используется библиотека от Laravel.
         * 
         * @see https://laravel.com/docs/8.x/database
         */
        'database' => [

            /**
             * Включение/выключение модуля.
             */
            'enable' => false,

            /**
             * Драйвер базы данных.
             * 
             * Поддерживает:
             * string "sqlite"
             * string "mysql"
             */
            'driver' => 'mysql',
            'sqlite' => [
                'database' => '/path/to/database.sqlite',
            ],
            'mysql' => [
                'host'      => 'localhost',
                'database'  => 'telegram_test',
                'username'  => 'mysql',
                'password'  => 'mysql',
                'charset'   => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
        ],

        /**
         * Модуль Cache.
         * 
         * Повзоляет взаимодействовать с Memcached и Redis.
         * Внимение, у вас должно быть установленно Memcached и/или Redis.
         */
        'cache' => [

            /**
             * Включение/выключение модуля.
             */
            'enable' => false,

            /**
             * Драйвер кеша.
             *
             * Поддерживает:
             * string "memcached"
             * string "redis"
             * 
             * @see https://www.php.net/manual/ru/book.memcached.php
             * @see https://github.com/phpredis/phpredis
             */
            'driver' => 'memcached',

            'memcached' => [
                'host'  => 'localhost',
                'port' => '11211',
            ],

            'redis' => [
                'host'  => '127.0.0.1',
                'port' => '6379',
            ],
        ],

        /**
         * Модуль Store.
         * 
         * Позвоялет сохранять данные для последущего использования. 
         * Все сохраненные данные сериализуются, поэтому пожно сохранить массивы и т. п.
         * Позволяет сохранять как глобно данные, так и данные привязанные к конкретному юзеру.
         */
        'store' => [

            /**
             * Включение/выключение модуля.
             */
            'enable' => false,

            /**
             * Драйвер кеша.
             *
             * Поддерживает:
             * string "file" - хранение в файлах (не рекомендуется для высоконагруженных ботов, возможна потеря данных).
             * string "database" - хранение в отдельной таблице `store`.
             * bool false - хранение в оперативной памяти (подходит только для Longpoll).
             * 
             * @see https://www.php.net/manual/ru/book.memcached.php
             * @see https://github.com/phpredis/phpredis
             */
            'driver' => 'file',

            /**
             * Параметры драйвера `file`.
             */
            'file' => [

                /**
                 * Директория для хранения файлов с данными.
                 */
                'dir' => __DIR__ . '/storage/store',
            ],
        ],

        /**
         * Модуль State.
         * 
         * Позволяет задавать стейты для юзеров.
         * С их помощью можно легко организовать цепочку диалога, например, форму сбора данных. 
         * Стейт позволяет хранить название стейта и данные.
         */
        'state' => [

            /**
             * Включение/выключение модуля.
             */
            'enable' => false,
        ],

        /**
         * Модуль Localization.
         */
        'localization' => [

            /**
             * Язык по умолчанию если у пользователя не определен язык, не найден файл локализации.
             */
            'default_language' => 'ru',

            /**
             * Директория с файлами локализации.
             * Файлы локализации должны иметь название языка с раширением .php, который возвращает массив.
             * Например: ru.php
             */
            'dir' => __DIR__ . '/localization',
        ],

        /**
         * Модуль Log.
         */
        'log' => [

            /**
             * Включение/выключение модуля.
             */
            'enable' => false,

            /**
             * Автоматически логировать входящий Update массив.
             * Запись происходит после отработки бота, чтобы не замедлять процесс ответа.
             */
            'auto' => true,

            /**
             * Директория с лог-файлами.
             * Название файла: DD.MM.YYYY_<POSTFIX>.log
             */
            'dir' => __DIR__ . '/storage/logs',
        ],

        /**
         * Компоненты. 
         * 
         * Позволяет внедрять/переиспользовать части кода как компоненты.
         * Соддержит массив с информацией о компонентах.
         */
        'components' => [

            /**
             * Уникальный ключ компонента
             */
            'vendor.component' => [

                /**
                 * Включение/выключение компонента.
                 */
                'enable' => false,

                /**
                 * Файл инициализации компонента (входная точка).
                 */
                'entrypoint' => __DIR__ . '/components/vendor/component/component.php',
            ],
        ],
    ];

    /**
     * Обновление от Telegram
     * 
     * @var Collection
     */
    private $update;

    private const TELEGRAM_API_URL = 'https://api.telegram.org/bot';

    private const TELEGRAM_API_FILE = 'https://api.telegram.org/file/bot';

    public const DEFAULT_SORT_VALUE = 500;

    /**
     * Track executed time
     *
     * @var float
     */
    private $startTime;

    /**
     * @var int
     */
    private $chatOrFromId;

    /**
     * Авторизация, установка конфигурации бота, подключение необходимых модулей.
     * 
     * @param string $token
     * @param array $config 
     * @param boolean $migration Осторожно! True - накатить миграцию, False - откатить миграцию 
     * @return Bot
     */
    public function auth(string $token = null, array $config = [], $migration = null)
    {
        if (!$token) {
            throw new \Exception("Missed requred parameter `token`");
        }

        $this->startTime = microtime(true);

        $this->token = $token;
        $this->config = new Collection(array_merge($this->config, $config));

        $this->config('bot.token', $token);

        $this->curl = curl_init();

        $this->keyboard = new Keyboard;
        $this->helper = new Helpers;

        if ($this->config('cache.enable')) {
            Cache::initialize();
            $this->cache = new Cache;
        }

        if ($this->config('database.enable')) {
            $this->db = Database::connect();
        }

        if ($this->config('log.enable')) {
            Log::initialize($this->config('log.dir'));
            $this->log = new Log;
        }

        if ($timezone = $this->config('common.timezone')) {
            date_default_timezone_set($timezone);
        }

        if ($migration === true) {
            Migration::up();
        } elseif ($migration === false) {
            Migration::down();
        }

        return $this;
    }

    /**
     * Пуллинг Webhook обновлений.
     * 
     * @param string|array|null $update
     * @return Bot
     */
    public function webhook($update = null)
    {
        Update::initialize();
        Update::set($update);

        $this->chatOrFromId = $this->update('*.chat.id', $this->update('*.from.id'));

        $this->decodeCallback();

        if ($this->config('store.enable')) {
            Store::initialize();
            $this->store = new Store;
        }

        if ($this->config('state.enable')) {
            State::initialize();
            $this->state = new State;
        }

        $userEnabled = $this->config('user.enable');
        if ($userEnabled) {
            User::initialize();
            $this->user = new User;
        }

        // Локлизация
        $defaultLang = $this->config('localization.default_language', 'en');

        $lang = $userEnabled ? $this->user()->lang : Update::get('*.from.language_code', $defaultLang);
        Localization::load($lang, $defaultLang);
        $this->lang = new Localization;

        // Подключение компонентов
        $this->loadComponents();

        return $this;
    }

    /**
     * Подключение компонентов
     *
     * @return void
     */
    private function loadComponents()
    {
        $components = $this->config()->get('components');

        if (!$components) {
            return;
        }

        foreach ($components as $component) {
            if (!$component['enable'] ?? null) {
                continue;
            }

            if (file_exists($component['entrypoint'] ?? null)) {
                require_once $component['entrypoint'];
            }
        }
    }

    /**
     * Вызвать функцию/метод.
     * 
     * @param $func
     * @param array $args
     * @return mixed
     */
    private function callFunc($func, array $args = [])
    {
        if (is_string($func) && strpos($func, '@') !== false) {
            $func = explode('@', $func);
            array_unshift($args, $this, $this->update());
        }

        return call_user_func_array($func, $args);
    }
}
