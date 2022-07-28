<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Contract;

use PhpParser\Node\Stmt;

interface RoutingCaseConverterInterface
{
    public function match(string $key, mixed $values): bool;

    public function convertToMethodCall(string $key, mixed $values): Stmt;
}
