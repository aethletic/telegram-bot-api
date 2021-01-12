# PHP Telegram Bot API

Простая и гибкая библиотека для создания ботов Telegram на языке PHP.

## Webhook бот
```php 
require 'vendor/autoload.php';

$bot = bot('1234567890:BOT_TOKEN')->webhook();

$bot->hear('Меня зовут {name}', fn ($name) => reply("Привет {$name}!"));

$bot->run();
```

## Echo бот
```php 
require 'vendor/autoload.php';

$bot = bot('1234567890:BOT_TOKEN')->webhook();

$bot->hear('', fn () => say(update('*.text')));

$bot->run();
```