<?php

declare(strict_types=1);

namespace Symplify\PHPStanLatteRules\Contract;

use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symplify\LattePHPStanCompiler\ValueObject\ComponentNameAndType;
use Symplify\TemplatePHPStanCompiler\ValueObject\RenderTemplateWithParameters;

interface LatteTemplateHolderInterface
{
    /**
     * call before other methods
     */
    public function check(ClassMethod $classMethod, Scope $scope): bool;

    /**
     * @return RenderTemplateWithParameters[]
     */
    public function findRenderTemplateWithParameters(ClassMethod $classMethod, Scope $scope): array;

    /**
     * @return ComponentNameAndType[]
     */
    public function findComponentNamesAndTypes(ClassMethod $classMethod, Scope $scope): array;
}
