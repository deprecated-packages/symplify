<?php

declare(strict_types=1);

namespace Symplify\TemplatePHPStanCompiler\ValueObject;

use PhpParser\Node\Expr\Array_;

/**
 * @api
 */
final class RenderTemplateWithParameters
{
    /**
     * @param non-empty-array&string[] $templateFilePaths
     */
    public function __construct(
        private array $templateFilePaths,
        private Array_ $parametersArray
    ) {
    }

    /**
     * @return string[]
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
