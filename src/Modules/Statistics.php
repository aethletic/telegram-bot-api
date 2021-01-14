<?php

namespace Telegram\Modules;

use Telegram\Bot;
use Telegram\Update;
use Telegram\Support\Helpers;

class Statistics
{
    /**
     * @return void
     */
    public static function collect()
    {
        $bot = Bot::getInstance();
        $date = Helpers::midnight();

        // messages stats
        if ($bot->config('statistics.messages')) {
            $isNewDate = $bot->db('stats_messages')->where('date', $date)->count() == 0;
            if ($isNewDate) {
                $bot->db('stats_messages')->insert([
                    'date' => $date,
                    'count' => 1,
                ]);
            } else {
                $bot->db('stats_messages')->where('date', $date)->increment('count', 1);
            }
        }


        // new users stats
        if ($bot->config('statistics.users')) {
            $isNewDate = $bot->db()->table('stats_users')->where('date', $date)->count() == 0;
            if ($bot->user()->firstTime()) {
                if ($isNewDate) {
                    $bot->db('stats_users')->insert([
                        'date' => $date,
                        'count' => 1,
                    ]);
                } else {
                    $bot->db('stats_users')->where('date', $date)->increment('count', 1);
                }
            } else {
                if ($isNewDate) {
                    $bot->db('stats_users')->insert([
                        'date' => $date,
                        'count' => 0,
                    ]);
                }
            }
        }

        // incoming update log
        if ($bot->config('statistics.updates')) {
            if (!Update::is()) {
                return;
            }

            $insert = [
                'date' => time(),
                'user_id' => $bot->update('*.from.id'),
                'user' => $bot->update('*.from.first_name'),
                'value' => json_encode(Update::toArray(), JSON_UNESCAPED_UNICODE)
            ];

            $bot->db('messages')->insert($insert);
        }
    }
}
