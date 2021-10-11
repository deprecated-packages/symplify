<?php

declare(strict_types=1);

namespace Symplify\LattePHPStanCompiler\Contract;

use Symplify\TemplatePHPStanCompiler\ValueObject\VariableAndType;

interface LatteVariableCollectorInterface
{
    /**
     * @return VariableAndType[]
     */
    public function getVariablesAndTypes(): array;
}
