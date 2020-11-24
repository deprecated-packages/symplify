<?php

declare(strict_types=1);

<<<<<<< HEAD
<<<<<<< HEAD
use Migrify\ClassPresence\HttpKernel\ClassPresenceKernel;
use Migrify\MigrifyKernel\Bootstrap\KernelBootAndApplicationRun;
=======
use Symplify\ClassPresence\HttpKernel\ClassPresenceKernel;
use Symplify\SymplifyKernel\ValueObject\KernelBootAndApplicationRun;
>>>>>>> 7e1cbd8ad... fixup! fixup! misc
=======
use Symplify\ClassPresence\HttpKernel\ClassPresenceKernel;
<<<<<<< HEAD
use Symplify\symplifyKernel\Bootstrap\KernelBootAndApplicationRun;
>>>>>>> 434bcd4b3... rename Migrify to Symplify
=======
use Symplify\SymplifyKernel\Bootstrap\KernelBootAndApplicationRun;
>>>>>>> 1a08239af... misc

$possibleAutoloadPaths = [
    // after split package
    __DIR__ . '/../vendor/autoload.php',
    // dependency
    __DIR__ . '/../../../autoload.php',
    // monorepo
    __DIR__ . '/../../../vendor/autoload.php',
];

foreach ($possibleAutoloadPaths as $possibleAutoloadPath) {
    if (file_exists($possibleAutoloadPath)) {
        require_once $possibleAutoloadPath;

        break;
    }
}

$kernelBootAndApplicationRun = new KernelBootAndApplicationRun(ClassPresenceKernel::class);
$kernelBootAndApplicationRun->run();
