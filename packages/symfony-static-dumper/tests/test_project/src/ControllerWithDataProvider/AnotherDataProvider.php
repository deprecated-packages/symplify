<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Tests\TestProject\ControllerWithDataProvider;

use Symplify\SymfonyStaticDumper\Contract\ControllerWithDataProviderInterface;
use Symplify\SymfonyStaticDumper\Tests\TestProject\Controller\OneArgumentController;

final class AnotherDataProvider implements ControllerWithDataProviderInterface
{
    public function getControllerClass(): string
    {
        return OneArgumentController::class;
    }

    public function getControllerMethod(): string
    {
        return '__invoke';
    }

    /**
     * @return string[]
     */
    public function getArguments(): array
    {
        return ['1', '2'];
    }
}
