<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoFactoryInConstructorRule\Fixture;

final class WithConstructorWithFactoryWithMutliAssignment
{
    public function __construct(ThirdFactory $thirdFactory)
    {
        $anotherProperty = $property = $thirdFactory->build();
    }
}
