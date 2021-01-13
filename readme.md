# WIP: PHP Telegram Bot API

Простая и гибкая библиотека для создания ботов [Telegram](https://telegram.org/) на языке PHP.

## 👷 Установка

```bash
$ composer require chipslays/telegram-bot-api
```

## 💡 Пример
```php 
require 'vendor/autoload.php';

$bot = bot('1234567890:BOT_TOKEN')->webhook();

$bot->hear('Меня зовут {name}', fn ($name) => reply("Привет {$name}!"));

$bot->run();
```

Больше примеров можно посмотреть [здесь](https://github.com/aethletic/telegram-bot-api/tree/master/examples).

## 📖 Документация

Подробная документация по использованию находится [здесь](https://github.com/chipslays/telegram-bot-api/tree/master/docs).

> **Почему нет отдельных классов для работы с [типами](https://core.telegram.org/bots/api#available-types) обновлений? 🤨**

Библиотека предоставляет гибкие возможности для работы с [Telegram Bot API ](https://core.telegram.org/bots/api), поддерживая как текущие методы/ответы, так и будущие предоставляя для этого универсальные методы, например, как `request()` и `on()`, а так же класс `Update`.

Вместо заранее прописанных классов с типами, используется единственный универсальный класс `Update` для работы с входящим обновлением. 

Например, чтобы получить тип [User](https://core.telegram.org/bots/api#user) представленный коллекцией: 

```php
$user = Update::get('message.from');
```

Далее мы можем получить данные, например, получим `username`, но так как он не у всех есть, передадим вторым параметром значение по умолчанию:

```php
$username = $user->get('username', 'Юзернейм не существует 😥');
```

Да, первое время, если вы не работали ранее с Bot API, то придется часто держать октрытой [страницу с документацией](https://core.telegram.org/bots/api) и смотреть доступные параметры.

## 🔑 License
Released under the MIT public license. See the enclosed [**`LICENSE`**](https://github.com/aethletic/telegram-bot-api/blob/master/license) for details.
