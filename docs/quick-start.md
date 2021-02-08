# Быстрый старт

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

## 📌 Команды

После установки библиотеки будут доступны некоторые команды в терминале.

**Создать файл конфигурации бота:**

```bash
$ vendor/bin/bot --config ./
```

**Создать готовую файловую структуру (skeleton):**

```bash
$ vendor/bin/bot --init ./
```

**Показать список доступных команд:**

```bash
$ vendor/bin/bot --help
```