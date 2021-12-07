<?php

declare(strict_types=1);

namespace Symplify\PHPStanLatteRules\Contract;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use Symplify\LattePHPStanCompiler\ValueObject\ComponentNameAndType;
use Symplify\TemplatePHPStanCompiler\ValueObject\RenderTemplateWithParameters;

interface LatteTemplateHolderInterface
{
    /**
     * call before other methods
     */
    public function check(Node $node, Scope $scope): bool;

    /**
     * @return RenderTemplateWithParameters[]
     */
    public function findRenderTemplateWithParameters(Node $node, Scope $scope): array;

    /**
     * @return ComponentNameAndType[]
     */
    public function findComponentNamesAndTypes(Node $node, Scope $scope): array;
}
