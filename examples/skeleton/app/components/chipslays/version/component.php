<?php

require __DIR__ . '/autoload.php';

/**
 * Показывает версию бота.
 * 
 * @command /version 
 */
$this->command('/version', function () {
    say(lang('CHIPSLAYS_BOT_VERSION', [
        '{version}' => $this->config('bot.version'),
    ]));
});