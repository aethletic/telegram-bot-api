# Update

## set

Установить апдейт.

```php
Telegram::set($update = null): void
```

## get

Универсальный метод, позволяет получить значение из апдейта или получить объект обновления.

```php
Update::get(string $key = null [, mixed $default = null]) : mixed
```

Без параметров метод вернет **апдейт** ввиде коллекции,а не класс `Update`.

> **Этот метод доступен как функция:** `update()`

```php
update(string $key = null [, mixed $default = null]) : mixed

update('message.text');
update()->get('message.text');
```

Список коротких алиасов для этого метода:

```php
Update::getMessage() : Collection
Update::getEditedMessage() : Collection
Update::getChannelPost() : Collection
Update::getEditedChannelPost() : Collection
Update::getInlineQuery() : Collection
Update::getChosenInlineResult() : Collection
Update::getCallbackQuery() : Collection
Update::getShippingQuery() : Collection
Update::getPreCheckoutQuery() : Collection
Update::getPoll() : Collection
Update::getPollAnswer() : Collection
Update::getCommand() : Collection
Update::getVoice() : Collection
Update::getSticker() : Collection
Update::getAnimation() : Collection
Update::getDocument() : Collection
Update::getAudio() : Collection
Update::getPhoto() : Collection
Update::getVideo() : Collection
Update::getVideoNote() : Collection
Update::getContact() : Collection
Update::getLocation() : Collection
Update::getVenue() : Collection
Update::getDice() : Collection
Update::getNewChatMembers() : Collection
Update::getLeftChatMember() : Collection
Update::getNewChatTitle() : Collection
Update::getNewChatPhoto() : Collection
Update::getMigrateToChatId() : int|string
Update::getMigrateFromChatId() : int|string
Update::getPinnedMessage() : Collection
Update::getInvoice() : Collection
Update::getSucessfulPayment() : Collection
Update::getConnectedWebsite() : Collection
Update::getReplyMarkup() : Collection
Update::getReply() : Collection
Update::getFrom() : Collection
Update::getChat() : Collection
Update::getCaption() : Collection
Update::getText() : Collection
Update::getTextOrCaption() : Collection
Update::getData() : Collection
Update::getCallbackData() : int|string
Update::getQuery() : int|string
Update::getId() : int
Update::getMessageId() : int
Update::getCallbackId() : int
Update::getInlineId() : int
Update::getPollId() : int
Update::getPollAnswerId() : int
```

## has

Проверка наличия ключа (dot notation).

```php
Update::has(string $key): bool
```

Список коротких алиасов для этого метода:

```php
Update::isMessage() : bool
Update::isCommand() : bool
Update::isEditedMessage() : bool
Update::isEditedChannelPost() : bool
Update::isInlineQuery() : bool
Update::isChosenInlineResult() : bool
Update::isCallbackQuery() : bool
Update::isShippingQuery() : bool
Update::isPreCheckoutQuery() : bool
Update::isPoll() : bool
Update::isPollAnswer() : bool
Update::isBot() : bool
Update::isSticker() : bool
Update::isVoice() : bool
Update::isAnimation() : bool
Update::isDocument() : bool
Update::isAudio() : bool
Update::isPhoto() : bool
Update::isVideo() : bool
Update::isVideoNote() : bool
Update::isContact() : bool
Update::isLocation() : bool
Update::isVenue() : bool
Update::isDice() : bool
Update::isNewChatMembers() : bool
Update::isLeftChatMember() : bool
Update::isNewChatTitle() : bool
Update::isNewChatPhoto() : bool
Update::isDeleteChatPhoto() : bool
Update::isChannelChatCreated() : bool
Update::isMigrateToChatId() : bool
Update::isMigrateFromChatId() : bool
Update::isPinnedMessage() : bool
Update::isInvoice() : bool
Update::isSucessfulPayment() : bool
Update::isConnectedWebsite() : bool
Update::isPassportData() : bool
Update::isReplyMarkup() : bool
Update::isReply() : bool
Update::isCaption() : bool
Update::isForward() : bool
Update::isSuperGroup() : bool
Update::isGroup() : bool
Update::isChannel() : bool
Update::isPrivate() : bool
```

## toArray

Получить массив входящего обновления.

```php
Update::toArray() : array
```

## is

Проверка наличия обновления.

```php
Update::is() : bool
```

## hasUpdate

Алиас для `Update::is()`.

```php
Update::hasUpdate() : bool
```

## setCommandTags

Установить символы с которых сообщение будет считаться командой.

По умолчанию `$tags = ['/', '.', '!']`

```php
Update::setCommandTags(array $tags) : void
```

## getCommandTags

Получить массив с символами с которых должна начинаться команда.

```php
Update::getCommandTags() : array
```


