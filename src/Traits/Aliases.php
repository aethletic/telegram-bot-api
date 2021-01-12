<?php

namespace Telegram\Traits;

use Telegram\Update;

trait Aliases
{
    /**
     * @var Keyboard
     */
    private $keyboard;

    /**
     * @var Log
     */
    private $log;

    /**
     * @var Helpers
     */
    private $helper;

    /**
     * @var Store
     */
    private $store;

    /**
     * @var State
     */
    private $state;

    /**
     * @var Localization
     */
    private $lang;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var User
     */
    private $user;

    /**
     * @var Database
     */
    private $db;

    /**
     * Конструктор клавиатуры. 
     * Пустой параметр возвращает объект Keyboard.
     * Автоматическое определение типа клавиатуры.
     * 
     * @param boolean $keyboard
     * @param boolean $oneTime
     * @param boolean $resize
     * @param boolean $selective
     * @return Keyboard|string
     */
    public function keyboard($keyboard = false, $oneTime = false, $resize = true, $selective = false)
    {
        if (!func_num_args()) {
            return $this->keyboard;
        }

        return $this->keyboard->show($keyboard, $oneTime, $resize, $selective);
    }

    /**
     * @return Log
     */
    public function log()
    {
        return $this->log;
    }

    /**
     * @return Helpers
     */
    public function helper()
    {
        return $this->helper;
    }

    /**
     * Возвращает объект Store если переданы пустые параметры
     * Или получает данные по ключу
     *
     * @param string|int $key
     * @param mixed $default
     * @param boolean $personal True привязано к пользовтаелю, false глобально
     * @return mixed|Store
     */
    public function store($key = null, $default = null, $personal = false)
    {
        return $key ? $this->store->get($key, $default, $personal) : $this->store;
    }

    /**
     * @return State
     */
    public function state()
    {
        return $this->state;
    }

    /**
     * @return Localization
     */
    public function lang($key = null, $replace = null, $language = null)
    {
        return !$key ? $this->lang : $this->lang->get($key, $replace, $language);
    }

    /**
     * @return User
     */
    public function user()
    {
        return $this->user;
    }

    /**
     * @return Database
     */
    public function db($table = null)
    {
        return !$table ? $this->db : $this->db->table($table);
    }

    /**
     * Получить значение из апдейта или получить объект обновления
     * 
     * @param string|null $keys
     * @param mixed $default
     * @return Collection|string|integer
     */
    public function update($keys = null, $default = null)
    {
        return $keys ? Update::get($keys, $default) : Update::get();
    }

    /**
     * Получить значение из конфига или получить объект конфига
     * 
     * @param string|null $keys
     * @param mixed $default
     * @return Collection|string|integer
     */
    public function config($keys = null, $default = null)
    {
        return $keys ? $this->config->get($keys, $default) : $this->config;
    }
}
