<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoFactoryInConstructorRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NoFactoryInConstructorRule\Source\SomeFactory;

final class WithConstructorWithFactoryWithAssignment
{
    public function __construct(SomeFactory $someFactory)
    {
        $property = $someFactory->create();
    }

}
