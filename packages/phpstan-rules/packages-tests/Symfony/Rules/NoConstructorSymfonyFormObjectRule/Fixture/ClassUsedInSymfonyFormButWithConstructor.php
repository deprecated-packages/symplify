<?php
declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Symfony\Rules\NoConstructorSymfonyFormObjectRule\Fixture;

final class ClassUsedInSymfonyFormButWithConstructor
{
    private int $requiredValue;

    public function __construct($requiredValue)
    {
        $this->requiredValue = $requiredValue;
    }
}
