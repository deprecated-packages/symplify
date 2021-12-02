<?php

// decoupled in own "*.php" file, so ECS, Rector and PHPStan works out of the box here

declare(strict_types=1);

use Symplify\NeonConfigDumper\Kernel\NeonConfigDumperKernel;
use Symplify\SymplifyKernel\ValueObject\KernelBootAndApplicationRun;

require __DIR__ . '/../../../vendor/autoload.php';

$kernelBootAndApplicationRun = new KernelBootAndApplicationRun(NeonConfigDumperKernel::class);
$kernelBootAndApplicationRun->run();
