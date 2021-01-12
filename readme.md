# PHP Telegram Bot API

Простой и гибкий инструмент для создания ботов Telegram.

```php 
require 'vendor/autoload.php';

$bot = bot('1234567890:BOT_TOKEN')->webhook();

$bot->hear('Меня зовут {name}', fn ($name) => reply("Привет {$name}!"));

$bot->run();
```
