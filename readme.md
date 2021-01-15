<h1 align="center"><b>WIP: PHP Telegram Bot API</b></h1>

<p align="center">Простая и гибкая библиотека для создания ботов <a href="https://telegram.org/">Telegram</a> на языке PHP.</p>

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

---

**Почему нет отдельных классов для работы с [типами](https://core.telegram.org/bots/api#available-types) обновлений? 🤨**

Библиотека предоставляет гибкие возможности для работы с [Telegram Bot API ](https://core.telegram.org/bots/api), поддерживая как текущие методы/ответы, так и будущие используя для этого универсальные методы, например, как `request()` и `on()`, а так же класс `Update`.

Вместо заранее прописанных классов с типами, используется единственный универсальный класс `Update` для работы с входящим обновлением. 

Например, получим тип [**`User`**](https://core.telegram.org/bots/api#user): 

```php
$user = Update::get('message.from');
```

Получим `username`, но так как он не у всех есть, передадим вторым параметром значение по умолчанию:

```php
$username = Update::get('message.from.username', 'Юзернейм не существует 😥');
```

Доступен короткий синтаксис:
```php
$user = update('message.from'); // вместо Update::get();
$user = update()->get('message.from'); // update() вернет объект `Update`;
```

Да, первое время, если вы не работали ранее с Bot API, то придется часто держать открытой [страницу с документацией](https://core.telegram.org/bots/api) и смотреть доступные параметры.

> Библиотека создавалась для себя/своих нужд, поэтому как-то так.

## 🔑 License
Released under the MIT public license. See the enclosed [**`LICENSE`**](https://github.com/aethletic/telegram-bot-api/blob/master/license) for details.
