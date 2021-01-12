<?php

namespace Telegram\Support;

class Arr
{
    /**
     * Получить значение из массива. (dot.notation support)
     * 
     * @param array $array
     * @param string|array $keys
     * @param mixed $default
     * @param string $separator
     * @return mixed
     */
    public static function get(array $array, $keys, $default = null, string $separator = '.')
    {
        $thisDefault = 'NOT__FOUND__4MfvLvGl:htK$ZAiRoEylHo7?CgIrCDDQWrT2qUBSazEd?mpwFPq@pWRFbtjqhHJ=R8Y!w4hujyWWNwEnsZ1VmQ?';

        $keys = !is_array($keys) ? explode($separator, $keys) : $keys;

        while ($key = array_shift($keys)) {
            if (array_key_exists($key, $array)) {
                $array = &$array[$key];
            } else {
                if ($key == '*') {
                    foreach ($array as $value) {
                        $result = is_array($value) ? static::get($value, $keys, $thisDefault) : $thisDefault;
                        if ($result !== $thisDefault) {
                            return $result;
                        }
                    }
                } else {
                    return $default;
                }
            }
        }

        return $array;
    }

    /**
     * Добавить/перезаписать значение в массив. (dot.notation support)
     * 
     * @param array $array
     * @param string $keys
     * @param mixed $value
     * @param string $separator
     * @return void
     */
    public static function set(array &$array, string $keys, $value = null, string $separator = '.')
    {
        $keys = explode($separator, $keys);

        foreach ($keys as $key) {
            $array = &$array[$key];
        }

        $array = $value;
    }

    /**
     * Проверить наличии ключа в массиве. (dot.notation support)
     * 
     * @param array $array
     * @param string|array $keys
     * @param string $separator
     * @return boolean
     */
    public static function has(array $array, $keys, string $separator = '.')
    {
        return self::get($array, $keys, false, $separator) ? true : false;
    }
}
