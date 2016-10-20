<?php

require_once __DIR__.'/../vendor/autoload.php';

// clear cache
register_shutdown_function(function () {
    Nette\Utils\FileSystem::delete(__DIR__.'/cache');
    Nette\Utils\FileSystem::delete(__DIR__.'/logs');
});
