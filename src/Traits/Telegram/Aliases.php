<?php

namespace Telegram\Traits\Telegram;

use Telegram\Support\Helpers;
use Telegram\Update;

trait Aliases
{
    public function sendReply($chatId, $messageId, $text = '', $keyboard = null, $extra = [])
    {
        return $this->request('sendMessage', $this->buildRequestParams([
            'chat_id' => $chatId,
            'text' => $text,
            'reply_to_message_id' => $messageId,
        ], $keyboard, $extra));
    }

    public function say($text, $keyboard = null, $extra = [])
    {
        return $this->sendMessage(
            $this->chatOrFromId,
            Helpers::shuffle($text),
            $keyboard,
            $extra
        );
    }

    public function reply($text, $keyboard = null, $extra = [])
    {
        return $this->sendMessage(
            $this->chatOrFromId,
            Helpers::shuffle($text),
            $keyboard,
            array_merge($extra, ['reply_to_message_id' => $this->update('*.message_id')])
        );
    }

    public function print($text)
    {
        return $this->say(print_r($text, true));
    }

    public function notify($text, $showAlert = false, $extra = [])
    {
        return $this->request('answerCallbackQuery', $this->buildRequestParams([
            'callback_query_id' => $this->update('callback_query.id'),
            'text' => $text,
            'show_alert' => $showAlert,
        ], null, $extra));
    }

    public function action($action = 'typing', $extra = [])
    {
        return $this->request('sendChatAction', $this->buildRequestParams([
            'chat_id' => $this->chatOrFromId,
            'action' => $action,
        ], null, $extra));

        return $this;
    }

    public function dice($emoji = '🎲', $keyboard = null, $extra = [])
    {
        return $this->sendDice($this->chatOrFromId, $emoji, $keyboard, $extra);
    }

    public function isActive($chatId, $action = 'typing', $extra = [])
    {
        $response = $this->request('sendChatAction', $this->buildRequestParams([
            'chat_id' => $chatId,
            'action' => $action,
        ], null, $extra));

        return !is_null($response) ? $response->get('ok') : false;
    }

    public function saveFile($fileUrl, $savePath)
    {
        $extension = strpos(basename($fileUrl), '.') !== false ? end(explode('.', basename($fileUrl))) : '';
        $savePath = str_ireplace(['{ext}', '{extension}', '{file_ext}'], $extension, $savePath);
        $savePath = str_ireplace(['{base}', '{basename}', '{base_name}', '{name}'], basename($fileUrl), $savePath);
        $savePath = str_ireplace(['{time}'], time(), $savePath);
        $savePath = str_ireplace(['{md5}'], md5(time() . mt_rand()), $savePath);
        $savePath = str_ireplace(['{rand}', '{random}', '{rand_name}', '{random_name}'], md5(time() . mt_rand()) . ".$extension", $savePath);

        file_put_contents($savePath, file_get_contents($this->buildRequestFileUrl($fileUrl)));

        return basename($savePath);
    }

    public function sendJson()
    {
        if (!Update::is()) {
            return false;
        }

        return $this->say('<code>' . json_encode($this->update->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</code>');
    }

    /**
     * Меняющееся сообщение с задержкой
     * 
     * @param array $elements Массив с сообщениями
     * @param integer|float $delay Задержка
     * @return boolean
     */
    public function loading(array $elements = [], $delay = 1)
    {
        $messageId = false;
        while ($element = array_shift($elements)) {
            if (!$messageId) {
                $result = $this->say($element)->get('result');
                $messageId = $result['message_id'];
            } else {
                $this->editMessageText($messageId, $this->update('*.chat.id'), $element);
            }
            $this->wait($delay);
        }
        return true;
    }
}
