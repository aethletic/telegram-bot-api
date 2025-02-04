<?php

namespace Telegram\Traits;

use Telegram\Update;
use Telegram\Modules\State;
use Telegram\Modules\User;
use Telegram\Modules\Statistics;
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
     * Массив с ответами по умолчанию
     *
     * @var array
     */
    private $defaultAnswers;

    /**
     * Массив с middlewares
     *
     * @var array
     */
    private $middlewares = [];

    private $canContinueEvent = true;

    /**
     * Этот метод вызывается В НАЧАЛЕ выполнения метода RUN.
     * Например, чтобы выполнить необходимые общие действия для webhook и longpoll.
     * 
     * @return void
     */
    private function beforeRun(): void
    {
        // code...
        // echo PHP_EOL . "BEFORE: " . $this->getExecutedTime() . PHP_EOL;
    }

    /**
     * Этот метод вызывается В КОНЦЕ выполнения метода RUN.
     * Используется для того, чтобы выполнить второстепенные задачи и не тормозить ответ.
     * 
     * @return void
     */
    private function afterRun(): void
    {
        // Записываем входящий апдейт в лог файл
        if ($this->config('log.enable') && $this->config('log.auto')) {
            $this->log()->write(Update::toArray(), 'auto');
        }

        // Обновляем последнее сообщение у юзера
        if ($this->config('user.enable')) {
            User::update(['last_message' => time()]);
        }

        // Собираем статистику (сообщений, новые юзеры, апдейты)
        // Здесь нет проверок, они в самом методе `collect`
        if ($this->config('database.enable')) {
            Statistics::collect();
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
        
        if ($this->events === []) {
            // Если не поймано никаких действий, то выолняем дефолтные события.
            try {
                $this->executeDefaultAnswers();
            } catch (\Throwable $th) {
                //throw $th;
            }
        } else {
            // Преобразумем многомерный массив во flatten и в цикле выполним события которые ранее валидировали в методе on()
            // Если переданая функция возвращает FALSE, это значит, что нужно прервать цикл.
            try {
                foreach (call_user_func_array('array_merge', $this->events) as $key => $event) {
                    if ($this->callFunc($event['func'], $event['args'] ?? []) === false) {
                        break;
                    }
                }
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        // echo PHP_EOL . "RUN END: " . $this->getExecutedTime() . PHP_EOL;

        $this->afterRun();
    }

    /**
     * Выполнить функции по умолчанию. 
     *
     * @return void
     */
    private function executeDefaultAnswers()
    {
        if (!$this->defaultAnswers) {
            return;
        }

        foreach ($this->defaultAnswers as $answer) {
            foreach ($answer['data'] as $key) {
                if (Update::has($key)) {
                    $this->callFunc($answer['func']);
                    return;
                }
            }
        }
    }

    /**
     * Выполнить функцию если не было поймано ни одно событие.
     *
     * @param string|array $data Ключ (массив значит "ИЛИ", хотя бы один ключ совпадает)
     * @param $func
     * @return void
     */
    public function default($data, $func)
    {
        $this->defaultAnswers[] = [
            'data' => (array) $data,
            'func' => $func,
        ];
    }

    private function canContinueEvent(): bool
    {
        if ($this->canContinueEvent === false) {
            $this->canContinueEvent = true;
            return false;
        }

        return true;
    }

    /**
     * Продолжить выполнение если хотя бы один стейт совпадает
     *
     * @param string|array $states
     * @return Bot
     */
    public function onState($states, $stopWords = [])
    {
        if ($stopWords !== [] && in_array(Update::getText(), $stopWords)) {
            $this->canContinueEvent = false;
            return $this;
        }

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
     * Универсальный обработчик событий
     * 
     * @param string|array $data
     * @param $func Обработчик события, чтобы прервать цепочку выполнения, необходимо вернуть FALSE.
     * @param integer $sort Сортировка, чем меньше число, тем функция выполнится раньше.
     * @return Bot
     */
    public function on($data, $func, $sort = null)
    {
        /** Middlewares (inline), states, etc.. */
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

            // $tmp = str_replace(' ', '\s', $value);

            if ($found == $value) {
                $this->events[$sort][] = [
                    'value' => $value,
                    'func' => $func,
                ];
                break;
            }

            /**
             * ['key' => 'my name is {name}']
             * 
             * command(?: (.*?))?(?: (.*?))?$
             */
            $value = preg_replace('~.?{(.*?)\?}~', '(?: (.*?))?', $value);
            $pattern = '~^' . preg_replace('/{(.*?)}/', '(.*?)', $value) . '$~';

            if (@preg_match_all($pattern, $found, $matches)) {
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

    private function resetCanitunueEventAndReturnSelf()
    {
        $this->canContinueEvent = true;
        return $this;
    }

    /**
     * Обработка входящий текстовых сообщений и/или измененных текстовых сообщений
     *
     * @param string|array $data
     * @param $func Обработчик события, чтобы прервать цепочку выполнения, необходимо вернуть FALSE.
     * @param integer $sort Сортировка, чем меньше число, тем функция выполнится раньше.
     *
     * @return \Telegram\Bot
     */
    public function onMessage($data, $func, $sort = null): \Telegram\Bot
    {
        if (!Update::isMessage() || Update::isCommand()) {
            return $this->resetCanitunueEventAndReturnSelf();
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

    public function onEditedMessage($data, $func, $sort = null): \Telegram\Bot
    {
        if (!Update::isEditedMessage() || Update::isCommand()) {
            return $this->resetCanitunueEventAndReturnSelf();
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
     *
     * @return Bot
     */
    public function onCommand($data, $func, $sort = null)
    {
        if (!Update::isCommand()) {
            return $this->resetCanitunueEventAndReturnSelf();
        }
        
        $data = array_map(function ($item) {
            if (in_array(mb_substr($item, 0, 1, 'utf-8'), Update::getCommandTags())) {
                // передан текст на отлов как "/команда", "!команда"
                return ['*.text' => $item];
            } else {
                // передан текст на отлов как "команда"
                return ['*.text' => mb_substr(Update::getCommand(), 0, 1, 'utf-8') . $item];
            }
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
     *
     * @return Bot
     */
    public function onCallback($data, $func, $sort = null)
    {
        if (!Update::isCallbackQuery()) {
            return $this->resetCanitunueEventAndReturnSelf();
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
            return $this->resetCanitunueEventAndReturnSelf();
        }

        $data = array_map(function ($item) {
            return ['*.text' => $item];
        }, (array) $data);

        return $this->on($data, $func, $sort);
    }

    public function onEditChannelPost($data, $func, $sort = null)
    {
        if (!Update::isEditedChannelPost()) {
            return $this->resetCanitunueEventAndReturnSelf();
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
     *
     * @return Bot
     */
    public function onInline($data, $func, $sort = null)
    {
        if (!Update::isInlineQuery()) {
            return $this->resetCanitunueEventAndReturnSelf();
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

    public function addMiddleware(string $name, $func): void
    {
        $this->middlewares[$name] = $func;
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

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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
