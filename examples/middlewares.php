<?php

require "../vendor/autoload.php";

// Авторизация
$bot = bot('1234567890:BOT_TOKEN', require 'config.php');

// Poll Webhook Telegram update
$update = $bot->webhook();

// Add middleware
$bot->addMiddleware('admin', function (Closure $next) {

    /** do something before */

    // Проверяем, если юзер не Админ, прерываем выполнение.
    if (!user()->isAdmin()) {
        return;
    }

    // Если юзер Админ, выполняем переданную функцию
    $next();

    /** do something after */
});

// Простой middleware, который должен вернуть True или False. 
// True - продолжить цепочку методов, False - прервать цепочку.
$bot->addMiddleware('sum', function () {
    return (2 + 2) == 4; // true
});

// Аналогичный простой метод
$bot->addMiddleware('checkDate', function () {
    return date('d') === '13'; // только если сегодня 13 число на календаре
});

// Будет выполнено только если сообщение пришло от пользователя который является Админом.
// Так же можно передать массив ['admin', 'sum'], если хотя бы ОДИН из них вернет False, функция не будет выполнена.
$bot->middleware('admin', function () use ($bot) {

    $bot->command('/ban {id}', function ($id) {
        //say("Пользователь {$id} забанен!");
        print_r('1');
    });

    $bot->command('/unban {id}', function ($id) {
        //say("Пользователь {$id} разрабен!");
        print_r('1');
    });
});

// Метод `hear` будет выполнен только если сумма будет равна 4.
$bot->middleware('sum')
    ->hear('Сколько будет 2 + 2?', function () {
        say('Очевидно, будет 5.');
    });

// Метод `hear` будет выполнен только если будет 13 число.
$bot->middleware('checkDate')
    ->hear('Сегодня 13 число?', function () {
        say('Да, сегодня 13 число.');
    });

// Можно передать несколько middlewares, если хотя бы ОДИН из них вернет False, цепочка не будет выполнена.
$bot->middleware(['checkDate', 'sum'])
    ->hear('Сегодня 13 число и 2 + 2 = 4?', function () {
        say('Да.');
    });    

// Можно комбинировать со стейтами.
$bot->onState('someName')
    ->middleware('checkDate')
    ->hear('Сегодня 13 число?', function () {
        say('Да, сегодня 13 число.');
    });

$bot->middleware('checkDate')
    ->onState('someName')
    ->hear('Сегодня 13 число?', function () {
        say('Да, сегодня 13 число.');
    });    

// Выполняем все события
$bot->run();
