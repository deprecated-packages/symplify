<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\NodeFactory;

use PhpParser\Node\Stmt\Expression;
use Symplify\PhpConfigPrinter\CaseConverter\InstanceOfNestedCaseConverter;

final class ContainerNestedNodesFactory
{
    public function __construct(
        private InstanceOfNestedCaseConverter $instanceOfNestedCaseConverter
    ) {
    }

    /**
     * @return Expression[]
     */
    public function createFromValues(array $nestedValues, string $key, $nestedKey): array
    {
        $nestedNodes = [];

        foreach ($nestedValues as $subNestedKey => $subNestedValue) {
            if (! $this->instanceOfNestedCaseConverter->isMatch($key, $nestedKey)) {
                continue;
            }

            $nestedNodes[] = $this->instanceOfNestedCaseConverter->convertToMethodCall(
                $subNestedKey,
                $subNestedValue
            );
        }

        return $nestedNodes;
    }
}
