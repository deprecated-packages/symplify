<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\HttpKernel;

use Symplify\MigrifyKernel\HttpKernel\AbstractMigrifyKernel;
use Symfony\Component\Config\Loader\LoaderInterface;

final class TemplateCheckerKernel extends AbstractMigrifyKernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.php');
    }
}
