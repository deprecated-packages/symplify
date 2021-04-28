<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandardTester\Contract;

interface ConfigAwareInterface
{
    public function provideConfig(): string;
}
