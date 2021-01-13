# WIP: PHP Telegram Bot API

Простая и гибкая библиотека для создания ботов Telegram на языке PHP.

## Установка

```bash
$ composer require chipslays/telegram-bot-api
```

## Пример
```php 
require 'vendor/autoload.php';

$bot = bot('1234567890:BOT_TOKEN')->webhook();

$bot->hear('Меня зовут {name}', fn ($name) => reply("Привет {$name}!"));

$bot->run();
```

Больше примеров можно посмотреть [здесь](https://github.com/aethletic/telegram-bot-api/tree/master/examples).

## Документация
Подробная документация по использованию находится [здесь]().

## License
Released under the MIT public license. See the enclosed [**`LICENSE`**](https://github.com/aethletic/telegram-bot-api/blob/master/license) for details.
