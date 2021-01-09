<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoFactoryInConstructorRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NoFactoryInConstructorRule\Source\NotFactory;

final class SkipWithConstructorWithoutFactory
{
    /**
     * @var NotFactory
     */
    private $property;

    public function __construct(NotFactory $notFactory)
    {
        $this->property = $notFactory;
    }
}
