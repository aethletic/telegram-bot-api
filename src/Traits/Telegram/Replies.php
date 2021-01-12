<?php

namespace Telegram\Traits\Telegram;

trait Replies
{
    public function replyChatAction($action = 'typing', $extra = [])
    {
        return $this->sendChatAction($this->chatOrFromId, $action, $extra);
    }

    public function replyToReply($messageId, $text = '', $keyboard = null, $extra = [])
    {
        return $this->sendReply($this->chatOrFromId, $messageId, $text, $keyboard, $extra);
    }

    public function replyMessage($text, $keyboard = null, $extra = [])
    {
        return $this->sendMessage($this->chatOrFromId, $keyboard, $extra);
    }

    public function replyForwardMessage($fromChatId, $messageId, $extra = [])
    {
        return $this->forwardMessage($this->chatOrFromId, $fromChatId, $messageId, $extra);
    }

    public function replyCopyMessage($fromChatId, $messageId, $extra = [])
    {
        return $this->copyMessage($this->chatOrFromId, $fromChatId, $messageId, $extra);
    }

    public function replyPhoto($photo, $caption = '', $keyboard = null, $extra = [])
    {
        return $this->sendPhoto($this->chatOrFromId, $photo, $caption, $keyboard, $extra);
    }

    public function replyAudio($audio, $caption = '', $keyboard = null, $extra = [])
    {
        return $this->sendAudio($this->chatOrFromId, $audio, $caption, $keyboard, $extra);
    }

    public function replyDocument($document, $caption = '', $keyboard = null, $extra = [])
    {
        return $this->sendDocument($this->chatOrFromId, $document, $caption, $keyboard, $extra);
    }

    public function replyAnimation($animation, $caption = '', $keyboard = null, $extra = [])
    {
        return $this->sendAnimation($this->chatOrFromId, $animation, $caption, $keyboard, $extra);
    }

    public function replyVideo($video, $caption = '', $keyboard = null, $extra = [])
    {
        return $this->sendVideo($this->chatOrFromId, $video, $caption, $keyboard, $extra);
    }

    public function replyVideoNote($videoNote, $keyboard = null, $extra = [])
    {
        return $this->sendVideoNote($this->chatOrFromId, $videoNote, $keyboard, $extra);
    }

    public function replySticker($sticker, $keyboard = null, $extra = [])
    {
        return $this->sendSticker($this->chatOrFromId, $sticker, $keyboard, $extra);
    }

    public function replyVoice($voice, $caption = '', $keyboard = null, $extra = [])
    {
        return $this->sendVoice($this->chatOrFromId, $voice, $caption, $keyboard, $extra);
    }

    public function replyMediaGroup($media, $extra = [])
    {
        return $this->sendMediaGroup($this->chatOrFromId, $media, $extra);
    }

    public function replyLocation($latitude, $longitude, $keyboard = null, $extra = [])
    {
        return $this->sendLocation($this->chatOrFromId, $latitude, $longitude, $keyboard, $extra);
    }

    public function replyDice($emoji = '🎲', $keyboard = null, $extra = [])
    {
        return $this->sendDice($this->chatOrFromId, $emoji, $keyboard, $extra);
    }
}
