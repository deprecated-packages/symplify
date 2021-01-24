<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Contract;

interface ControllerWithDataProviderInterface
{
    public function getControllerClass(): string;

    public function getControllerMethod(): string;

    /**
     * @return mixed[]
     */
    public function getArguments(): array;
}
