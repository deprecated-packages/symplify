<?php

declare(strict_types=1);

// @see https://cylab.be/blog/53/detect-unused-composer-dependencies?accept-cookies=1

return [
    'composerJsonPath' => __DIR__ . '/composer.json',
    'vendorPath' => __DIR__ . '/vendor/',
    'scanDirectories' => [
        __DIR__ . '/packages'
    ],
];
