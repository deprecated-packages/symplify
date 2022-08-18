<?php

declare(strict_types=1);

namespace Symplify\AutowireArrayParameter\Tests\DependencyInjection\CompilerPass\Source\SkipMe;

use Symplify\AutowireArrayParameter\Tests\DependencyInjection\CompilerPass\Source\Contract\FirstCollectedInterface;
use Symplify\AutowireArrayParameter\Tests\DependencyInjection\CompilerPass\Source\Contract\SecondCollectedInterface;

final class SkipUnionTypes
{
    public function __construct(
        public FirstCollectedInterface|SecondCollectedInterface $collectedInterface
    ) {
    }
}
