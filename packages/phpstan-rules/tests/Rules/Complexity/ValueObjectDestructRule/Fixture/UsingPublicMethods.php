<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ValueObjectDestructRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Complexity\ValueObjectDestructRule\Source\SomeValueObject;

final class UsingPublicMethods
{
    public function run(SomeValueObject $someValueObject)
    {
        $this->process($someValueObject->getName(), $someValueObject->getSurname());
    }

    private function process(string $getName, string $getSurname)
    {
    }
}
