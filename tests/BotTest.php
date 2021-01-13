<?php

use PHPUnit\Framework\TestCase;

use Telegram\Bot;
use Telegram\Update;
use Telegram\Support\Debug;

final class BotTest extends TestCase
{
    /**
     * @var Bot
     */
    protected static $bot;

    public static function setUpBeforeClass(): void
    {
        self::$bot = Bot::getInstance()
            ->auth('1234567890:BOT_TOKEN', require __DIR__ . "/../examples/config.php")
            ->webhook(Debug::MESSAGE);
    }

    protected  function setUp(): void
    {
    }

    protected function tearDown(): void
    {
    }

    public function testBotInstance()
    {
        $this->assertInstanceOf(Bot::class, bot());
        $this->assertInstanceOf(Bot::class, Bot::getInstance());
        $this->assertInstanceOf(Bot::class, self::$bot);
    }

    public function testBotConfigGet()
    {
        self::$bot->config()->set('some.path.to.value', 'data');

        $this->assertEquals('data', self::$bot->config('some.path.to.value'));
        $this->assertEquals('data', self::$bot->config()->get('some.path.to.value'));
    }

    public function testBotUpdateGet()
    {
        Update::set([
            'some' => [
                'path' => [
                    'to' => [
                        'value' => 'data',
                    ],
                ],
            ],
        ]);

        $this->assertEquals('data', self::$bot->update('some.path.to.value'));
        $this->assertEquals('data', self::$bot->update()->get('some.path.to.value'));
    }
}
