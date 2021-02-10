<?php

namespace Telegram;

use Chipslays\Collection\Collection;

class Update
{
    /**
     * @var Bot
     */
    private static $bot;

    /**
     * @var Collection
     */
    private static $update;

    /**
     * @var array
     */
    private static $commandTags = ['/', '.', '!'];

    /**
     * Инициализировать необходимые переменные.
     *
     * @return void
     */
    public static function initialize()
    {
        if (!self::$bot) {
            self::$bot = Bot::getInstance();
        }
    }

    /**
     * Установить апдейт.
     *
     * @param string|array|null
     * @param array|null|string $update
     *
     * @return void
     */
    public static function set($update = null): void
    {
        if ($update) {
            $update = is_array($update) ? $update : json_decode($update, true);
        } else {
            $input = file_get_contents('php://input');
            $update = $input ? json_decode($input, true) : null;
        }



        self::$update = $update ? new Collection($update) : null;
    }

    /**
     * Получить значение из апдейта или получить объект обновления.
     *
     * @param string|null $keys
     * @param mixed $default
     * @return Collection|string|integer
     */
    public static function get($keys = null, $default = null)
    {
        return $keys ? self::$update->get($keys, $default) : self::$update;
    }

    /**
     * Получить массив входящего обновления.
     *
     * @return array|false
     */
    public static function toArray()
    {
        return self::is() ? self::$update->toArray() : false;
    }

    /**
     * Проверка наличия обновления.
     *
     * @return boolean
     */
    public static function is(): bool
    {
        return !is_null(self::$update);
    }

    /**
     * Проверка наличия обновления.
     *
     * @return boolean
     */
    public static function hasUpdate(): bool
    {
        return self::is();
    }

    /**
     * Проверка наличия ключа (dot notation)
     *
     * @param int|string $key
     *
     * @return boolean
     */
    public static function has($key): bool
    {
        return self::$update->has($key);
    }

    /**
     * Алиас для Bot::on()
     *
     * @param string|array $data
     * @param $func Обработчик события, чтобы прервать цепочку выполнения, необходимо вернуть FALSE.
     * @param integer $sort Сортировка, чем меньше число, тем функция выполнится раньше.
     * @return Bot
     */
    public static function on($data, $func, $sort = Bot::DEFAULT_SORT_VALUE)
    {
        return self::$bot->on($data, $func, $sort);
    }

    /**
     * Установить символы с которых сообщение будет считаться командой
     * По умолчанию ['/', '.', '!']
     *
     * @param array $tags
     * @return void
     */
    public static function setCommandTags(array $tags)
    {
        self::$commandTags = $tags;
    }

    public static function getCommandTags()
    {
        return self::$commandTags;
    }

    public static function isMessage(): bool
    {
        return self::has('message');
    }

    /**
     * @return Collection
     */
    public static function getMessage()
    {
        return new Collection(self::get('message'));
    }

    public static function isEditedMessage(): bool
    {
        return self::has('edited_message');
    }

    /**
     * @return Collection
     */
    public static function getEditedMessage()
    {
        return new Collection(self::get('edited_message'));
    }

    public static function isChannelPost(): bool
    {
        return self::has('channel_post');
    }

    /**
     * @return Collection
     */
    public static function getChannelPost()
    {
        return new Collection(self::get('channel_post'));
    }

    public static function isEditedChannelPost(): bool
    {
        return self::has('edited_channel_post');
    }

    /**
     * @return Collection
     */
    public static function getEditedChannelPost()
    {
        return new Collection(self::get('edited_channel_post'));
    }

    public static function isInlineQuery(): bool
    {
        return self::has('inline_query');
    }

    /**
     * @return Collection
     */
    public static function getInlineQuery()
    {
        return new Collection(self::get('inline_query'));
    }

    public static function isChosenInlineResult(): bool
    {
        return self::has('chosen_inline_result');
    }

    /**
     * @return Collection
     */
    public static function getChosenInlineResult()
    {
        return new Collection(self::get('chosen_inline_result'));
    }

    public static function isCallbackQuery(): bool
    {
        return self::has('callback_query');
    }

    /**
     * @return Collection
     */
    public static function getCallbackQuery()
    {
        return new Collection(self::get('callback_query'));
    }

    public static function isShippingQuery(): bool
    {
        return self::has('shipping_query');
    }

    /**
     * @return Collection
     */
    public static function getShippingQuery()
    {
        return new Collection(self::get('shipping_query'));
    }

    public static function isPreCheckoutQuery(): bool
    {
        return self::has('pre_checkout_query');
    }

    /**
     * @return Collection
     */
    public static function getPreCheckoutQuery()
    {
        return new Collection(self::get('pre_checkout_query'));
    }

    public static function isPoll(): bool
    {
        return self::has('poll');
    }

    /**
     * @return Collection
     */
    public static function getPoll()
    {
        return new Collection(self::get('poll'));
    }

    public static function isPollAnswer(): bool
    {
        return self::has('poll_answer');
    }

    /**
     * @return Collection
     */
    public static function getPollAnswer()
    {
        return new Collection(self::get('poll_answer'));
    }

    public static function isCommand(): bool
    {
        if (!self::isMessage() && !self::isEditedMessage()) {
            return false;
        }

        if (!$text = self::get('*.text', false)) {
            return false;
        }

        return in_array(mb_substr($text, 0, 1, 'utf-8'), self::$commandTags);
    }

    /**
     * @return int|string
     */
    public static function getCommand()
    {
        return self::get('*.text');
    }

    public static function isBot(): bool
    {
        return self::has('*.from.is_bot');
    }

    public static function isSticker(): bool
    {
        return self::has('*.sticker');
    }

    /**
     * @return Collection
     */
    public static function getSticker()
    {
        return new Collection(self::get('*.sticker'));
    }

    public static function isVoice(): bool
    {
        return self::has('*.voice');
    }

    /**
     * @return Collection
     */
    public static function getVoice()
    {
        return new Collection(self::get('*.voice'));
    }

    public static function isAnimation(): bool
    {
        return self::has('*.animation');
    }

    /**
     * @return Collection
     */
    public static function getAnimation()
    {
        return new Collection(self::get('*.animation'));
    }

    public static function isDocument(): bool
    {
        return self::has('*.document');
    }

    /**
     * @return Collection
     */
    public static function getDocument()
    {
        return new Collection(self::get('*.document'));
    }

    public static function isAudio(): bool
    {
        return self::has('*.audio');
    }

    /**
     * @return Collection
     */
    public static function getAudio()
    {
        return new Collection(self::get('*.audio'));
    }

    public static function isPhoto(): bool
    {
        return self::has('*.photo');
    }

    /**
     * @return Collection
     */
    public static function getPhoto()
    {
        return new Collection(self::get('*.photo'));
    }

    public static function isVideo(): bool
    {
        return self::has('*.video');
    }

    /**
     * @return Collection
     */
    public static function getVideo()
    {
        return new Collection(self::get('*.video'));
    }

    public static function isVideoNote(): bool
    {
        return self::has('*.video_note');
    }

    /**
     * @return Collection
     */
    public static function getVideoNote()
    {
        return new Collection(self::get('*.video_note'));
    }

    public static function isContact(): bool
    {
        return self::has('*.contact');
    }

    /**
     * @return Collection
     */
    public static function getContact()
    {
        return new Collection(self::get('*.contact'));
    }

    public static function isLocation(): bool
    {
        return self::has('*.location');
    }

    /**
     * @return Collection
     */
    public static function getLocation()
    {
        return new Collection(self::get('*.location'));
    }

    public static function isVenue(): bool
    {
        return self::has('*.venue');
    }

    /**
     * @return Collection
     */
    public static function getVenue()
    {
        return new Collection(self::get('*.venue'));
    }

    public static function isDice(): bool
    {
        return self::has('*.dice');
    }

    /**
     * @return Collection
     */
    public static function getDice()
    {
        return new Collection(self::get('*.dice'));
    }

    public static function isNewChatMembers(): bool
    {
        return self::has('*.new_chat_members');
    }

    /**
     * @return Collection
     */
    public static function getNewChatMembers()
    {
        return new Collection(self::get('*.new_chat_members'));
    }

    public static function isLeftChatMember(): bool
    {
        return self::has('*.left_chat_member');
    }

    /**
     * @return Collection
     */
    public static function getLeftChatMember()
    {
        return new Collection(self::get('*.left_chat_member'));
    }

    public static function isNewChatTitle(): bool
    {
        return self::has('*.new_chat_title');
    }

    /**
     * @return int|string
     */
    public static function getNewChatTitle()
    {
        return self::get('*.new_chat_title');
    }

    public static function isNewChatPhoto(): bool
    {
        return self::has('*.new_chat_photo');
    }

    /**
     * @return Collection
     */
    public static function getNewChatPhoto()
    {
        return new Collection(self::get('*.new_chat_photo'));
    }

    public static function isDeleteChatPhoto(): bool
    {
        return self::has('*.delete_chat_photo');
    }

    public static function isChannelChatCreated(): bool
    {
        return self::has('*.channel_chat_created');
    }

    public static function isMigrateToChatId(): bool
    {
        return self::has('*.migrate_to_chat_id');
    }

    /**
     * @return int|string
     */
    public static function getMigrateToChatId()
    {
        return self::get('*.migrate_to_chat_id');
    }

    public static function isMigrateFromChatId(): bool
    {
        return self::has('*.migrate_from_chat_id');
    }

    /**
     * @return int|string
     */
    public static function getMigrateFromChatId()
    {
        return self::get('*.migrate_from_chat_id');
    }

    public static function isPinnedMessage(): bool
    {
        return self::has('*.pinned_message');
    }

    /**
     * @return Collection
     */
    public static function getPinnedMessage()
    {
        return new Collection(self::get('*.pinned_message'));
    }

    public static function isInvoice(): bool
    {
        return self::has('*.invoice');
    }

    /**
     * @return Collection
     */
    public static function getInvoice()
    {
        return new Collection(self::get('*.invoice'));
    }

    public static function isSucessfulPayment(): bool
    {
        return self::has('*.successful_payment');
    }

    /**
     * @return Collection
     */
    public static function getSucessfulPayment()
    {
        return new Collection(self::get('*.successful_payment'));
    }

    public static function isConnectedWebsite(): bool
    {
        return self::has('*.connected_website');
    }

    /**
     * @return Collection|int|string
     */
    public static function getConnectedWebsite()
    {
        return self::get('*.connected_website');
    }

    public static function isPassportData(): bool
    {
        return self::has('*.passport_data');
    }

    /**
     * @return Collection
     */
    public static function getPassportData()
    {
        return new Collection(self::get('*.passport_data'));
    }

    public static function isReplyMarkup(): bool
    {
        return self::has('*.reply_markup');
    }

    /**
     * @return Collection
     */
    public static function getReplyMarkup()
    {
        return new Collection(self::get('*.reply_markup'));
    }

    public static function isReply(): bool
    {
        return self::has('*.reply_to_message');
    }

    /**
     * @return Collection
     */
    public static function getReply()
    {
        return new Collection(self::get('*.reply_to_message'));
    }

    /**
     * @return Collection
     */
    public static function getFrom()
    {
        return new Collection(self::get('*.from'));
    }

    /**
     * @return Collection
     */
    public static function getChat()
    {
        return new Collection(self::get('*.chat'));
    }

    public static function isCaption(): bool
    {
        return self::has('*.caption');
    }

    /**
     * @return int|string
     */
    public static function getCaption()
    {
        return self::get('*.reply_to_message');
    }

    /**
     * @return int|string
     */
    public static function getText()
    {
        return self::get('*.text');
    }

    /**
     * @return int|string
     */
    public static function getTextOrCaption()
    {
        return self::get('*.text', self::get('*.caption'));
    }

    /**
     * @return int|string
     */
    public static function getData()
    {
        return self::get('callback_query.data');
    }

    /**
     * @return int|string
     */
    public static function getQuery()
    {
        return self::get('inline_query.query');
    }

    /**
     * @return int|string
     */
    public static function getId()
    {
        return self::get('update_id');
    }

    /**
     * @return int|string
     */
    public static function getMessageId()
    {
        return self::get('*.message_id');
    }

    /**
     * @return int|string
     */
    public static function getCallbackId()
    {
        return self::get('callback_query.id');
    }

    /**
     * @return int|string
     */
    public static function getPollId()
    {
        return self::get('poll.id');
    }

    /**
     * @return int|string
     */
    public static function getPollAnswerId()
    {
        return self::get('poll_answer.poll_id');
    }

    /**
     * @return int|string
     */
    public static function getInlineId()
    {
        return self::get('inline_query.id');
    }

    public static function isForward(): bool
    {
        return self::has('*.forward_date') || self::has('*.forward_from');
    }

    public static function isSuperGroup(): bool
    {
        return self::get('*.chat.type') == 'supergroup';
    }

    public static function isGroup(): bool
    {
        return self::get('*.chat.type') == 'group';
    }

    public static function isChannel(): bool
    {
        return self::get('*.chat.type') == 'channel';
    }

    public static function isPrivate(): bool
    {
        return self::get('*.chat.type') == 'private';
    }
}
