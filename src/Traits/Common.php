<?php

namespace Telegram\Traits;

trait Common
{
    /**
     * Ждать определенное время (поддерживает миллисекунды).
     *
     * @param integer|float $delay
     * @return boolean
     */
    public function wait($delay = 1)
    {
        usleep(round($delay * 1000000));
        return true;
    }

    /**
     * Сравнение пароля Администратора (условная авторизация).
     * Если $password корректный, вернет True, иначе False.
     *
     * @param [type] $password
     *
     * @return bool
     */
    public function adminAuth($password): bool
    {
        if (!$this->user()->isAdmin()) {
            return false;
        }

        $username = $this->update('*.from.username');
        $userId = $this->update('*.from.id');
        return $password == $this->config("admin.list.{$username}", $this->config("admin.list.{$userId}", false));
    }

    /**
     * Получить время выполнения.
     *
     * @param integer $lenght
     * @return int|float
     */
    public function getExecutedTime(int $lenght = 6)
    {
        return round(microtime(true) - $this->startTime, $lenght);
    }

    /**
     * Получает среднюю загрузку системы.
     *
     * @return array
     */
    public function getSystemLoad(): array
    {
        return sys_getloadavg();
    }
}
