<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Contract;

use PhpParser\Node\Stmt\Expression;

interface CaseConverterInterface
{
    public function match(string $rootKey, mixed $key, mixed $values): bool;

    public function convertToMethodCall(mixed $key, mixed $values): Expression;
}
