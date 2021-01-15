<?php

$bot->command('/^\/start (.*?)$/iu', 'MainController@start', 1);
$bot->hear('{echo}', 'MainController@echo');
