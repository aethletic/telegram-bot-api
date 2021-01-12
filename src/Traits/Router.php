<?php

namespace Telegram\Traits;

use Telegram\Update;
use Telegram\Modules\State;
use Telegram\Modules\User;
use Telegram\Bot;

/**
 * Оброаботчик событий (Events based)
 */
trait Router
{
    /**
     * Массив с событиями которые поймал бот
     *
     * @var array
     */
    private $events = [];

    /**
     * Массив с middlewares
     *
     * @var array
     */
    private $middlewares = [];

    private $canContinueEvent = true;

    private function beforeRun()
    {
        // code...
        // echo PHP_EOL . "BEFORE: " . $this->getExecutedTime() . PHP_EOL;
    }

    private function afterRun()
    {
        // Записываем входящий апдейт в лог файл
        if ($this->config('log.enable') && $this->config('log.auto')) {
            $this->log()->write(Update::toArray(), 'auto');
        }

        if ($this->config('user.enable')) {
            User::update(['last_message' => time()]);
        }

        // echo PHP_EOL . "AFTER: " . $this->getExecutedTime() . PHP_EOL;
    }

    /**
     * Выполнить все обработанные события из метода on()
     * 
     * @return void
     */
    public function run()
    {
        // Сортируем элементы в порядке возрастания (где ключ - это параметр $sort из метода on())
        ksort($this->events);

        $this->beforeRun();

        // Преобразумем многомерный массив во flatten и в цикле выполним события которые ранее валидировали в методе on()
        // Если переданая функция возвращает FALSE, это значит, что нужно прервать цикл.
        foreach (call_user_func_array('array_merge', $this->events) as $key => $event) {
            if ($this->callFunc($event['func'], $event['args'] ?? []) === false) {
                break;
            }
        }
        // echo PHP_EOL . "RUN END: " . $this->getExecutedTime() . PHP_EOL;

        $this->afterRun();
    }

    private function canContinueEvent()
    {
        if (!$this->canContinueEvent) {
            $this->canContinueEvent = true;
            return false;
        }

        return true;
    }

    /**
     * Универсальный обработчик событий
     * 
     * @param string|array $data
     * @param $func Обработчик события, чтобы прервать цепочку выполнения, необходимо вернуть FALSE.
     * @param integer $sort Сортировка, чем меньше число, тем функция выполнится раньше.
     * @return Bot
     */
    public function on($data, $func, $sort = null)
    {
        /** Middlewares (inline), etc.. */
        if (!$this->canContinueEvent()) {
            return $this;
        }

        $sort = (int) $sort ?? self::DEFAULT_SORT_VALUE;

        foreach ((array) $data as $key => $value) {

            /**
             * Любые значения
             */
            if ($value == '{any}' || is_null($value) || $value == '') {
                $this->events[$sort][] = [
                    'value' => $value,
                    'func' => $func,
                ];
                break;
            }

            /**
             * Формат: 
             * [
             *   ['*.text' => '/text1/i'],
             *   ['*.text' => '/text2/i'],
             * ]
             */
            if (is_numeric($key) && is_array($value)) {
                $this->on($value, $func);
                continue;
            }

            /**
             * Формат: 
             * ['*.text', '*.sticker']
             */
            if (is_numeric($key) && $this->update()->has($value)) {
                $this->events[$sort][] = [
                    'value' => $value,
                    'func' => $func,
                ];
                break;
            }

            /**
             * Формат: 
             * ['*.text' => 'hello']
             * ['*.text' => 'hello {name}']
             */
            if (!$found = $this->update($key, false)) {
                continue;
            }

            $matches = [];

            if ($found == $value || preg_match_all('~^' . preg_replace('/{(.*?)}/', '(.*?)', $value) . '$~', $found, $matches)) {
                $this->events[$sort][] = [
                    'value' => $value,
                    'func' => $func,
                    'args' => count($matches) > 1 ? array_map(function ($item) {
                        return array_shift($item);
                    }, array_slice($matches, 1)) : [],
                ];
                break;
            }

            /**
             * RegEx паттерн
             */
            if (@preg_match_all($value, $found, $matches)) {
                $this->events[$sort][] = [
                    'value' => $value,
                    'func' => $func,
                    'args' => count($matches) > 1 ? array_map(function ($item) {
                        return array_shift($item);
                    }, array_slice($matches, 1)) : [],
                ];
                break;
            }
        }

        return $this;
    }

    /**
     * Обработка входящий текстовых сообщений и/или измененных текстовых сообщений
     *
     * @param string|array $data
     * @param $func Обработчик события, чтобы прервать цепочку выполнения, необходимо вернуть FALSE.
     * @param integer $sort Сортировка, чем меньше число, тем функция выполнится раньше.
     * @return void
     */
    public function onMessage($data, $func, $sort = null)
    {
        if (!Update::isMessage() || Update::isCommand()) {
            return $this;
        }

        $data = array_map(function ($item) {
            return ['message.text' => $item];
        }, (array) $data);

        return $this->on($data, $func, $sort);
    }

    /**
     * Short alias for `onMessage` method
     */
    public function hear($data, $func, $sort = null)
    {
        return $this->onMessage($data, $func, $sort);
    }

    public function onEditedMessage($data, $func, $sort = null)
    {
        if (!Update::isEditedMessage() || Update::isCommand()) {
            return $this;
        }

        $data = array_map(function ($item) {
            return ['edited_message.text' => $item];
        }, (array) $data);

        return $this->on($data, $func, $sort);
    }

    /**
     * Обработка входящий команд (только текстовые сообщения и/или измененные текствоые сообщения)
     *
     * @param string|array $data
     * @param $func Обработчик события, чтобы прервать цепочку выполнения, необходимо вернуть FALSE.
     * @param integer $sort Сортировка, чем меньше число, тем функция выполнится раньше.
     * @return void
     */
    public function onCommand($data, $func, $sort = null)
    {
        if (!Update::isCommand()) {
            return $this;
        }

        $data = array_map(function ($item) {
            return ['*.text' => $item];
        }, (array) $data);

        return $this->on($data, $func, $sort);
    }

    /**
     * Short alias for `onCommand` method
     */
    public function command($data, $func, $sort = null)
    {
        return $this->onCommand($data, $func, $sort);
    }

    /**
     * Обработка входящего callback_query
     *
     * @param string|array $data
     * @param $func Обработчик события, чтобы прервать цепочку выполнения, необходимо вернуть FALSE.
     * @param integer $sort Сортировка, чем меньше число, тем функция выполнится раньше.
     * @return void
     */
    public function onCallback($data, $func, $sort = null)
    {
        if (!Update::isCallbackQuery()) {
            return $this;
        }

        $data = array_map(function ($item) {
            return ['callback_query.data' => $item];
        }, (array) $data);

        return $this->on($data, $func, $sort);
    }

    /**
     * Short alias for `onCallback` method
     */
    public function callback($data, $func, $sort = null)
    {
        return $this->onCallback($data, $func, $sort);
    }

    public function onChannelPost($data, $func, $sort = null)
    {
        if (!Update::isChannelPost()) {
            return $this;
        }

        $data = array_map(function ($item) {
            return ['*.text' => $item];
        }, (array) $data);

        return $this->on($data, $func, $sort);
    }

    public function onEditChannelPost($data, $func, $sort = null)
    {
        if (!Update::isEditedChannelPost()) {
            return $this;
        }

        $data = array_map(function ($item) {
            return ['*.text' => $item];
        }, (array) $data);

        return $this->on($data, $func, $sort);
    }

    /**
     * Обработка входящего inline_query
     *
     * @param string|array $data
     * @param $func Обработчик события, чтобы прервать цепочку выполнения, необходимо вернуть FALSE.
     * @param integer $sort Сортировка, чем меньше число, тем функция выполнится раньше.
     * @return void
     */
    public function onInline($data, $func, $sort = null)
    {
        if (!Update::isInlineQuery()) {
            return $this;
        }

        $data = array_map(function ($item) {
            return ['inline_query.query' => $item];
        }, (array) $data);

        return $this->on($data, $func, $sort);
    }

    public function middleware(...$args)
    {
        if ($this->canContinueEvent === false) {
            return $this;
        }

        /** 
         * Для цепочного вызова
         * 
         * Добавление:
         * $bot->addMiddleware('name', function () {...});
         * 
         * Использование:
         * $bot->middleware('name')->hear(...);
         * 
         * Поддержка нескольких middlewares
         * $bot->middleware(['first', 'second'])->hear(...);
         * 
         * NOTE: вызывать перед любым событием
         */
        if (count($args) == 1) {
            foreach ((array) $args[0] as $key) {
                $this->canContinueEvent = isset($this->middlewares[$key]) ? $this->callFunc($this->middlewares[$key]) : false;

                if ($this->canContinueEvent === false) {
                    break;
                }
            }

            return $this;
        }

        /** 
         * Для "обертки"
         * 
         * Добавление:
         * $bot->addMiddleware('name', function ($next) {
         *      // do something before
         *      $next()
         *      // do something after 
         * });
         * 
         * Использование:
         * $bot->middleware('name', function () {
         *      // your code here ...
         * });
         */
        if (count($args) == 2) {
            return isset($this->middlewares[$args[0]]) ? $this->callFunc($this->middlewares[$args[0]], [$args[1]]) : false;
        }
    }

    public function addMiddleware(string $name, $func)
    {
        $this->middlewares[$name] = $func;
    }

    /**
     * Продолжить выполнение если хотя бы один стейт совпадает
     *
     * @param string|array $states
     * @return Bot
     */
    public function onState($states)
    {
        if ($this->canContinueEvent === false) {
            return $this;
        }

        foreach ((array) $states as $stateName) {
            $this->canContinueEvent = State::$name == $stateName;

            if ($this->canContinueEvent === true) {
                break;
            }
        }

        return $this;
    }

    /**
     * Выполнить функцию если все ключи существуют
     *
     * @param string|array $filters
     * @param $func
     * @return mixed
     */
    public function filter($filters, $func)
    {
        foreach ((array) $filters as $value) {
            if (is_array($value)) {
                $key = key($value);
                if (!Update::has($key) || Update::get($key) !== $value[$key]) {
                    return false;
                }
            }

            if (is_string($value) && !Update::has($value)) {
                return false;
            }
        }

        return $this->callFunc($func);
    }

    /**
     * Выполнить функцию если хотя бы один ключ существует
     *
     * @param string|array $filters
     * @param $func
     * @return mixed
     */
    public function filterAny($filters, $func)
    {
        foreach ((array) $filters as $value) {
            if (is_array($value)) {
                $key = key($value);
                if (Update::has($key) && Update::get($key) == $value[$key]) {
                    return $this->callFunc($func);
                }
            }

            if (is_string($value) && Update::has($value)) {
                return $this->callFunc($func);
            }
        }

        return false;
    }

    /**
     * Выполнить функцию если все ключи не существуют
     *
     * @param string|array $filters
     * @param $func
     * @return mixed
     */
    public function filterNot($filters, $func)
    {
        foreach ((array) $filters as $value) {
            if (is_array($value)) {
                $key = key($value);
                if (Update::has($key) || Update::get($key) == $value[$key]) {
                    return false;
                }
            }

            if (is_string($value) && Update::has($value)) {
                return false;
            }
        }

        return $this->callFunc($func);
    }

    /**
     * Выполнить функцию если хотя бы один ключ не существует
     *
     * @param string|array $filters
     * @param $func
     * @return mixed
     */
    public function filterAnyNot($filters, $func)
    {
        foreach ((array) $filters as $value) {
            if (is_array($value)) {
                $key = key($value);
                if (!Update::has($key) && Update::get($key) !== $value[$key]) {
                    return $this->callFunc($func);
                }
            }

            if (is_string($value) && !Update::has($value)) {
                return $this->callFunc($func);
            }
        }

        return false;
    }

    public function onMaxSystemLoad($func, $sort = null)
    {
        $load = $this->getSystemLoad();
        if ($load[0] > $this->config('common.max_system_load')) {
            $sort = (int) $sort ?? self::DEFAULT_SORT_VALUE;

            $this->events[$sort][] = [
                'func' => $func,
                'args' => [$load],
            ];
        }
    }

    public function onFlood($func, $sort = null)
    {
        if (!$this->user()->isFlood()) {
            return;
        }

        $sort = (int) $sort ?? self::DEFAULT_SORT_VALUE;

        $this->events[$sort][] = [
            'func' => $func,
            'args' => [$this->user()->getFloodTime()],
        ];
    }

    public function onAdmin($func, $sort = null)
    {
        if (!$this->user()->isAdmin()) {
            return;
        }

        $sort = (int) $sort ?? self::DEFAULT_SORT_VALUE;

        $this->events[$sort][] = [
            'func' => $func,
        ];
    }

    public function onFirstTime($func, $sort = null)
    {
        if (!$this->user()->firstTime()) {
            return;
        }

        $sort = (int) $sort ?? self::DEFAULT_SORT_VALUE;

        $this->events[$sort][] = [
            'func' => $func,
        ];
    }

    public function onBan($func, $sort = null)
    {
        if (!$this->user()->isBanned()) {
            return;
        }

        $sort = (int) $sort ?? self::DEFAULT_SORT_VALUE;

        $this->events[$sort][] = [
            'func' => $func,
            'args' => [$this->user()->ban_date_from, $this->user()->ban_date_to, $this->user()->ban_comment], // from, to, comment
        ];
    }

    public function onNewVersion($func, $sort = null)
    {
        if (!$this->user()->newVersion()) {
            return;
        }

        $sort = (int) $sort ?? self::DEFAULT_SORT_VALUE;

        $this->events[$sort][] = [
            'func' => $func,
            'args' => [$this->user()->version, $this->config('bot.version')], // old version, new version
        ];
    }
}
