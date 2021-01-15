<?php 

$bot->addMiddleware('simple', function () {
    return true; // or false for prevent next action
});