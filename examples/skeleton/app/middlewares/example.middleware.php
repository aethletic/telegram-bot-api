<?php 

$bot->addMiddleware('example', function (Closure $next) {
    /** do something before */
    $next();
    /** do something after */
});