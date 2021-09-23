<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\ValueObject;

use PhpParser\Node\Expr\Array_;

final class RenderTemplateWithParameters
{
    /**
     * @param non-empty-array&string[] $templateFilePaths
     * @param Array_ $parametersArray
     */
    public function __construct(
        private array $templateFilePaths,
        private Array_ $parametersArray
    ) {
    }

    /**
     * @return non-empty-array&string[]
     */
    public function getTemplateFilePaths(): array
    {
        return $this->templateFilePaths;
    }

    public function getParametersArray(): Array_
    {
        return $this->parametersArray;
    }
}
