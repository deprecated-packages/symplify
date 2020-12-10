<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Tests\TestProject\ControllerWithDataProvider;

use Symplify\SymfonyStaticDumper\Contract\ControllerWithDataProviderInterface;
use Symplify\SymfonyStaticDumper\Tests\TestProject\Controller\TwoArgumentsController;

final class SecondControllerDataProvider implements ControllerWithDataProviderInterface
{
    public function getControllerClass(): string
    {
        return TwoArgumentsController::class;
    }

    public function getControllerMethod(): string
    {
        return '__invoke';
    }

    /**
     * @return int[][]|string[][]
     */
    public function getArguments(): array
    {
        return [['test', 1], ['test', 2], ['foo', 1], ['foo', 2]];
    }
}
