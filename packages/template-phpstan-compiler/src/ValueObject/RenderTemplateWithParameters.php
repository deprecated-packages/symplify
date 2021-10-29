<?php

declare(strict_types=1);

namespace Symplify\TemplatePHPStanCompiler\ValueObject;

use PhpParser\Node\Expr\Array_;

/**
 * @api
 */
final class RenderTemplateWithParameters
{
    public function __construct(
        private string $templateFilePath,
        private Array_ $parametersArray
    ) {
    }

    public function getTemplateFilePath(): string
    {
        return $this->templateFilePath;
    }

    public function getParametersArray(): Array_
    {
        return $this->parametersArray;
    }
}
