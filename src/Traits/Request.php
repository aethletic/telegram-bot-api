<?php

namespace Telegram\Traits;

use Telegram\Update;
use Chipslays\Collection\Collection;

trait Request
{
    /**
     * @var \CurlHandle 
     */
    private $curl;

    /**
     * Универсальный вызов методов Telegram.
     *
     * @param string $method Название метода.
     * @param array $params Массив параметров, где ключ - это навание параметра, а значение - это значение параметра.
     * @param boolean $isFile True если передается файл, False для обычных запросов.
     * @return void
     */
    public function request(string $method, array $params = [], bool $isFile = false)
    {
        if ($isFile) {
            $headers = 'Content-Type: multipart/form-data';
        } else {
            $headers = 'Content-Type: application/json';
            $params = json_encode($params);
        }

        curl_setopt($this->curl, CURLOPT_URL, $this->getRequestUrl($method));
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, [$headers]);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);

        $response = curl_exec($this->curl);

        return new Collection(json_decode($response, true));
    }

    /**
     * Собирает параметры в единый массив.
     *
     * @param array $params
     * @param array|string $keyboard
     * @param array $extra
     * @return void
     */
    private function buildRequestParams($params = [], $keyboard = null, $extra = [])
    {
        if ($keyboard) {
            $params['reply_markup'] = is_array($keyboard) ? $this->keyboard($keyboard) : $keyboard;
        }

        $params['parse_mode'] = $this->config('telegram.parse_mode', 'html');

        return array_merge($params, (array) $extra);
    }

    /**
     * Собирает ссылку для запроса.
     *
     * @param string $method
     * @return string
     */
    private function getRequestUrl($method = null)
    {
        return self::TELEGRAM_API_URL . "{$this->token}/{$method}";
    }

    /**
     * Декодирует/расшифровывает входящий параметр data у callback_query 
     *
     * @return void
     */
    private function decodeCallback()
    {
        if (!Update::is()) {
            return;
        }

        if (!Update::isCallbackQuery()) {
            return;
        }

        if (!$method = $this->config('telegram.safe_callback')) {
            return;
        }

        $data = $this->update('callback_query.data');

        if (!$data) {
            return;
        }

        switch (strtolower($method)) {
            case 'encode':
                $data = gzinflate(base64_decode($data));
                break;
            case 'hash':
                // code...
                break;
        }

        $this->update()->set('callback_query.data', $data);
    }
}
