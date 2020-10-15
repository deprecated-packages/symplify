<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use Symplify\CodingStandard\NodeAnalyzer\DependencyNodeAnalyzer;
use Symplify\CodingStandard\NodeAnalyzer\TypeNodeAnalyzer;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\ForbiddenProtectedPropertyRule\ForbiddenProtectedPropertyRuleTest
 */
final class ForbiddenProtectedPropertyRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Property with protected modifier is not allowed. Use interface instead.';

    /**
     * @var DependencyNodeAnalyzer
     */
    private $dependencyNodeAnalyzer;

    /**
     * @var TypeNodeAnalyzer
     */
    private $typeNodeAnalyzer;

    public function __construct(DependencyNodeAnalyzer $dependencyNodeAnalyzer, TypeNodeAnalyzer $typeNodeAnalyzer)
    {
        $this->dependencyNodeAnalyzer = $dependencyNodeAnalyzer;
        $this->typeNodeAnalyzer = $typeNodeAnalyzer;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Property::class, ClassConst::class];
    }

    /**
     * @param Property|ClassConst $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $node->isProtected()) {
            return [];
        }

        if ($this->dependencyNodeAnalyzer->isInsideAbstractClassAndPassedAsDependencyViaConstructor($node)) {
            return [];
        }

        if ($this->dependencyNodeAnalyzer->isInsideClassAndPassedAsDependencyViaAutowireMethod($node)) {
            return [];
        }

        if ($this->typeNodeAnalyzer->isStaticAndContainerOrKernelType($node)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}
