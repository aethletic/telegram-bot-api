<?php

namespace Telegram\Support;

class Debug
{
    public const MESSAGE = '{
        "update_id": 31348350,
        "message": {
            "message_id": 44414,
            "from": {
                "id": 436432850,
                "is_bot": false,
                "first_name": "чипсы",
                "last_name": "лейс",
                "username": "chipslays",
                "language_code": "ru"
            },
            "chat": {
                "id": 436432850,
                "first_name": "чипсы",
                "last_name": "лейс",
                "username": "chipslays",
                "type": "private"
            },
            "date": 1605891721,
            "text": "My name is Debil"
        }
    }';

    public const CALLBACK_QUERY = '{
        "update_id": 31348301,
        "callback_query": {
            "id": "5881482190582191249",
            "from": {
                "id": 436432850,
                "is_bot": false,
                "first_name": "чипсы",
                "last_name": "лейс",
                "username": "chipslays",
                "language_code": "ru"
            },
            "message": {
                "message_id": 44402,
                "from": {
                    "id": 1086711779,
                    "is_bot": true,
                    "first_name": "MyBot",
                    "username": "MyTestBot"
                },
                "chat": {
                    "id": 436432850,
                    "first_name": "чипсы",
                    "last_name": "лейс",
                    "type": "private"
                },
                "date": 1605461755,
                "text": "Какое-то сообщение...",
                "reply_markup": {
                    "inline_keyboard": [
                        [
                            {
                                "text": "👨",
                                "callback_data": "qwe1"
                            },
                            {
                                "text": "👩",
                                "callback_data": "qwe2"
                            },
                            {
                                "text": "👻",
                                "callback_data": "qwe3"
                            }
                        ]
                    ]
                }
            },
            "chat_instance": "642874091806719723",
            "data": "y62ML05NLkotiS9LzClNjc8sjjc0NjYHAA=="
        }
    }';
}
