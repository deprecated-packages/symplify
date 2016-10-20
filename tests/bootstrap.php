<?php

include __DIR__ . '/../vendor/autoload.php';

// clear cache
register_shutdown_function(function () {
    $tempDirectories = getTempAndLogDirectories();
    foreach ($tempDirectories as $path => $info) {
        Nette\Utils\FileSystem::delete($path);
    }
});

function getTempAndLogDirectories() : array
{
    $finder = Nette\Utils\Finder::findDirectories('cache', 'logs')->from(__DIR__.'/../src');
    return iterator_to_array($finder->getIterator());
}
