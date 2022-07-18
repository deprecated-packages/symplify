<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMissingAssingNoVoidMethodCallRule\Source;

final class ReturnMethodStatic
{
    public function getStatic(): static
    {
    }
}
