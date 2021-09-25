<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Contract\Templates;

interface UsedVariableNamesResolverInterface
{
    /**
     * @return string[]
     */
    public function resolveFromFilePath(string $filePath): array;
}
