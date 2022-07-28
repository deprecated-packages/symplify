<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Contract;

use PhpParser\Node\Stmt;

interface CaseConverterInterface
{
    public function match(string $rootKey, mixed $key, mixed $values): bool;

    public function convertToMethodCallStmt(mixed $key, mixed $values): Stmt;
}
