<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoFactoryInConstructorRule\Fixture;

class NotFactory
{
}

final class WithConstructorWithoutFactory
{
    public function __construct(NotFactory $notFactory)
    {
        $this->property = $notFactory;
    }
}
