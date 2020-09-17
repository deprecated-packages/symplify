<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use Symplify\CodingStandard\PHPStan\ParentMethodAnalyser;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoProtectedElementInFinalClassRule\NoProtectedElementInFinalClassRuleTest
 */
final class NoProtectedElementInFinalClassRule extends AbstractManyNodeTypeRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use protected element in final class';

    /**
     * @var ParentMethodAnalyser
     */
    private $parentMethodAnalyser;

    public function __construct(ParentMethodAnalyser $parentMethodAnalyser)
    {
        $this->parentMethodAnalyser = $parentMethodAnalyser;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Property::class, ClassMethod::class];
    }

    /**
     * @param Property|ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $parent = $node->getAttribute('parent');
        if (! $parent instanceof Class_) {
            return [];
        }

        if (! $parent->isFinal()) {
            return [];
        }

        if (! $node->isProtected()) {
            return [];
        }

        if ($node instanceof Property) {
            return [self::ERROR_MESSAGE];
        }

        $methodName = (string) $node->name;

        if ($parent->extends === null) {
            if ($this->isMethodExistsInTraits($parent, $methodName)) {
                return [];
            }
        }

        if ($this->isMethodExistsInParentClass($scope, $parent, $methodName)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    private function isMethodExistsInParentClass(Scope $scope, Class_ $parent, string $methodName)
    {
        if ($this->parentMethodAnalyser->hasParentClassMethodWithSameName($scope, $methodName)) {
            return true;
        }

        if ($this->isMethodExistsInTraits($parent, $methodName)) {
            return true;
        }

        return false;
    }

    private function isMethodExistsInTraits(Class_ $parent, string $methodName)
    {
        $traits = $parent->getTraitUses();
        foreach ($traits as $trait) {
            if ($trait->getMethod($methodName)) {
                return true;
            }
        }

        return false;
    }
}
