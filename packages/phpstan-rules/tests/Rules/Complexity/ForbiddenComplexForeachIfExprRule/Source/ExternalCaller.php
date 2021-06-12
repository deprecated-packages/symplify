<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenComplexForeachIfExprRule\Source;

final class ExternalCaller
{
    public function returnsBool($value): bool
    {
        return mt_rand(0, 100) ? true : false;
    }

    public function returnWhatever($value)
    {
        if ($value) {
            return 'string';
        }

        return 1000;
    }
}
