<?php

namespace Telegram\Support;

/**
 * Вспомогательные функции
 */
class Helpers
{
    /**
     * Перемешивает текст в {{двойных фигурных скобках}}.
     *
     * Например: shuffle('Сегодня {{лето|осень|зима|весна}}')
     * Вернет: случайное слово из фигурных скобок, например, "осень".
     *
     * @param [type] $message
     *
     * @return string
     */
    public static function shuffle(string $message): string
    {
        preg_match_all('/{{(.+?)}}/mi', $message, $sentences);

        if (sizeof($sentences[1]) == 0) {
            return $message;
        }

        foreach ($sentences[1] as $words) {
            $words_array = explode('|', $words);
            $words_array = array_map('trim', $words_array);
            $select = $words_array[array_rand($words_array)];
            $message = str_ireplace('{{' . $words . '}}', $select, $message);
        }

        return $message;
    }

    /**
     * Проверяет является ли строка Json.
     *
     * @param string $string
     * @return boolean
     */
    public static function isRegEx(string $string)
    {
        return @preg_match($string, '') !== false;
    }

    /**
     * Проверяет является ли строка Json.
     *
     * @param string $string
     * @return boolean
     */
    public static function isJson(string $string)
    {
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

    /**
     * Плюрализация (Русский, Украинский???, Белорусский???).
     * 
     * Например: plural(10, ['арбуз', 'арбуза', 'арбузов']) 
     * Вернет: арбузов
     *
     * @param string|int $n 
     * @param array $forms
     * @return string
     */
    public static function plural($n, array $forms)
    {
        return is_float($n) ? $forms[1] : ($n % 10 == 1 && $n % 100 != 11 ? $forms[0] : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? $forms[1] : $forms[2]));
    }

    /**
     * English pluralization.
     *
     * @param string|int $value
     * @param string $phrase
     * @return string
     */
    public static function pluralEng($value, string $phrase)
    {
        $plural = '';
        if ($value > 1) {
            for ($i = 0; $i < strlen($phrase); $i++) {
                if ($i == strlen($phrase) - 1) {
                    $plural .= ($phrase[$i] == 'y') ? 'ies' : (($phrase[$i] == 's' || $phrase[$i] == 'x' || $phrase[$i] == 'z' || $phrase[$i] == 'ch' || $phrase[$i] == 'sh') ? $phrase[$i] . 'es' : $phrase[$i] . 's');
                } else {
                    $plural .= $phrase[$i];
                }
            }
            return $plural;
        }
        return $phrase;
    }

    /**
     * Проверить наличие RTL символов (Arabic, Persian, Hebrew)
     *
     * @param string $string
     *
     * @return bool
     */
    public static function isRtl($string)
    {
        $rtl_chars_pattern = '/[\x{0590}-\x{05ff}\x{0600}-\x{06ff}]/u';
        return preg_match($rtl_chars_pattern, $string);
    }

    /**
     * Выбрать случайный элемент из массива
     *
     * @param array $arr
     * @param boolean $shuffle
     * @return mixed
     */
    public static function random(array $arr, bool $shuffle = true)
    {
        if ($shuffle) {
            shuffle($arr);
        }

        return $arr[array_rand($arr)];
    }

    /**
     * Возвращает время (полночь)
     * Например: 2020-02-02 00:00:00
     *
     * @param boolean $timestamp
     *
     * @return string
     */
    public static function midnight($timestamp = null)
    {
        $timestamp = $timestamp ? $timestamp : time();
        return strtotime(date('Y-m-d', $timestamp) . ' midnight');
    }

    /**
     * Подготавливает файл для загрузки
     *
     * @param string $path
     * @return \CURLFile|bool
     */
    public static function upload(string $path = null, string $mimeType = null, string $postName = null)
    {
        return $path ? new \CURLFile($path, $mimeType, $postName) : false;
    }

    /**
     * Генерирует случайную строку.
     *
     * @param integer $lenght
     * @param array $chars
     * @return string
     */
    public static function getRandomCode(int $lenght = 6, array $chars = null)
    {
        $chars = !$chars ? array_merge(range('a', 'z'), range('A', 'Z'), range(0, 1)) : $chars;

        shuffle($chars);

        $code = '';
        for ($i = 0; $i < $lenght; $i++) {
            $code .= self::random($chars, false);
        }

        return $code;
    }
}
