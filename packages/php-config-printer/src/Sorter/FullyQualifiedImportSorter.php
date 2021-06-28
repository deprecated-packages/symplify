<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Sorter;

use Symplify\PhpConfigPrinter\ValueObject\FullyQualifiedImport;
use Symplify\PhpConfigPrinter\ValueObject\ImportType;

final class FullyQualifiedImportSorter
{
    /**
     * @var array<string, int>
     */
    private const TYPE_ORDER = [
        ImportType::CLASS_TYPE => 0,
        ImportType::CONSTANT_TYPE => 1,
        ImportType::FUNCTION_TYPE => 2,
    ];

    /**
     * @param FullyQualifiedImport[] $imports
     *
     * @return FullyQualifiedImport[]
     */
    public function sortImports(array $imports): array
    {
        $sortByFullQualifiedCallback = static fn ($left, $right): int => strcmp(
            $left->getFullyQualified(),
            $right->getFullyQualified()
        );
        usort($imports, $sortByFullQualifiedCallback);

        $sortByTypeCallback = static fn ($left, $right): int => self::TYPE_ORDER[$left->getType()] <=> self::TYPE_ORDER[$right->getType()];
        usort($imports, $sortByTypeCallback);

        return $imports;
    }
}
