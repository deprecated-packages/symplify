<?php

declare(strict_types=1);

namespace Symplify\AutowireArrayParameter\Tests\SourcePhp8\SkipMe;

use Symplify\AutowireArrayParameter\Tests\Source\Contract\FirstCollectedInterface;
use Symplify\AutowireArrayParameter\Tests\Source\Contract\SecondCollectedInterface;

final class UnionTypes
{
    public function __construct(
        public FirstCollectedInterface|SecondCollectedInterface $collectedInterface
    ) {
    }
}
