<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\NodeAnalyzer;

use Latte\Engine;
use Nette\Application\UI\Template;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\PropertyFetch;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use Symplify\Astral\TypeAnalyzer\ContainsTypeAnalyser;

/**
 * @api
 */
final class NetteTypeAnalyzer
{
    /**
     * @var array<class-string<Engine|Template>>
     */
    private const TEMPLATE_TYPES = [
        'Latte\Engine',
        'Nette\Application\UI\Template',
        'Nette\Application\UI\ITemplate',
        'Nette\Bridges\ApplicationLatte\Template',
        'Nette\Bridges\ApplicationLatte\DefaultTemplate',
    ];

    public function __construct(
        private ContainsTypeAnalyser $containsTypeAnalyser
    ) {
    }

    /**
     * E.g. $this->template->key
     */
    public function isTemplateMagicPropertyType(Expr $expr, Scope $scope): bool
    {
        if (! $expr instanceof PropertyFetch) {
            return false;
        }

        if (! $expr->var instanceof PropertyFetch) {
            return false;
        }

        return $this->isTemplateType($expr->var, $scope);
    }

    /**
     * E.g. $this->template
     */
    public function isTemplateType(Expr $expr, Scope $scope): bool
    {
        return $this->containsTypeAnalyser->containsExprTypes($expr, $scope, self::TEMPLATE_TYPES);
    }

    /**
     * This type has getComponent() method
     */
    public function isInsideComponentContainer(Scope $scope): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        // this type has getComponent() method
        return $classReflection->isSubclassOf('Nette\ComponentModel\Container');
    }
}
