<?php declare(strict_types=1);

use Symplify\ChangelogLinker\DependencyInjection\ContainerFactory;

$config = null;
$argvInput = new Symfony\Component\Console\Input\ArgvInput();
if ($argvInput->hasParameterOption('--config')) {
    $config = $argvInput->getParameterOption('--config');
}

$containerFactory = new ContainerFactory();
if ($config) {
    return $containerFactory->createWithConfig($config);
}

return $containerFactory->create();
