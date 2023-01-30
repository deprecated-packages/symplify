<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\NodeFactory;

use PhpParser\Node\Stmt;
use Symplify\PhpConfigPrinter\CaseConverter\NestedCaseConverter\InstanceOfNestedCaseConverter;

final class ContainerNestedNodesFactory
{
    public function __construct(
        private readonly InstanceOfNestedCaseConverter $instanceOfNestedCaseConverter
    ) {
    }

    /**
     * @api
     * @param mixed[] $nestedValues
     * @return Stmt[]
     */
    public function createFromValues(array $nestedValues, string $key, int|string $nestedKey): array
    {
        $nestedStmts = [];

        foreach ($nestedValues as $subNestedKey => $subNestedValue) {
            if (! $this->instanceOfNestedCaseConverter->isMatch($key, $nestedKey)) {
                continue;
            }

            $nestedStmts[] = $this->instanceOfNestedCaseConverter->convertToMethodCall(
                $subNestedKey,
                $subNestedValue
            );
        }

        return $nestedStmts;
    }
}
