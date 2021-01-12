<?php

namespace Telegram\Support;

class Collection
{
    /**
     * @var array
     */
    protected $items = [];

    public function __construct($array = [])
    {
        $this->items = (array) $array;
    }

    /**
     * Получить значение из коллекции. (dot.notation support)
     *
     * @param string $keys
     * @param mixed $default
     * @param string $separator
     * @return mixed
     */
    public function get(string $keys, $default = null, string $separator = '.')
    {
        return Arr::get($this->items, $keys, $default, $separator);
    }

    /**
     * Добавить/перезаписать значение в коллекции. (dot.notation support)
     *
     * @param string $keys
     * @param mixed $default
     * @param string $separator
     * @return Collection
     */
    public function set(string $keys, $default = null, string $separator = '.')
    {
        Arr::set($this->items, $keys, $default, $separator);
        return $this;
    }

    /**
     * Проверить наличии ключа в коллекции. (dot.notation support)
     *
     * @param string $keys
     * @param string $separator
     * @return boolean
     */
    public function has(string $keys, string $separator = '.')
    {
        return Arr::has($this->items, $keys, $separator);
    }

    /**
     * Возвращает количество элементов в коллекции.
     *
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * Очищает все элементы в коллекции.
     *
     * @return Collection
     */
    public function clear()
    {
        $this->items = [];
        return $this;
    }

    /**
     * Возвращает коллкцию в виде массива.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->items;
    }

    /**
     * Возвращает коллекцию в виде массива.
     *
     * @return object
     */
    public function toObject()
    {
        return (object) $this->items;
    }

    /**
     * Возвращает коллекцию в виде строки.
     *
     * @return string
     */
    public function __toString()
    {
        return print_r($this->items, true);
    }
}
