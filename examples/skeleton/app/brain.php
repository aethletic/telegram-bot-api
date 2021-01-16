<?php

$bot->command(['/^\/start$/i', '/^\/start (.*?)$/iu'], 'MainController@start', 1);
$bot->hear('{echo}', 'MainController@echo');
