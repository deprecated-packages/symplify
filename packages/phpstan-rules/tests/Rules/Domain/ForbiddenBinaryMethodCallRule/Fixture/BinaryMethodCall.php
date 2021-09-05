<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Domain\ForbiddenBinaryMethodCallRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Domain\ForbiddenBinaryMethodCallRule\Source\SomeAbstractSearch;

final class BinaryMethodCall
{
    public function run(SomeAbstractSearch $someAbstractSearch)
    {
        if ($someAbstractSearch->getId() !== null) {
            return false;
        }

        return true;
    }
}
