<?php

declare(strict_types=1);

namespace Symplify\TemplatePHPStanCompiler\Contract;

interface UsedVariableNamesResolverInterface
{
    /**
     * @return string[]
     */
    public function resolveFromFilePath(string $filePath): array;
}
