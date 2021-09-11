<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\NoLatteMissingMethodCallRule\Source;

final class SomeTypeWithMethods
{
    public function getName()
    {
        return 'one';
    }
}
