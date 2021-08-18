<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoFactoryInConstructorRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NoFactoryInConstructorRule\Source\SomeFactory;

final class SkipException extends \Exception
{
    public function __construct(SomeFactory $someFactory)
    {
        $value = $someFactory->create();
        parent::__construct('message', $value);
    }
}
