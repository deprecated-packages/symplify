<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use ReflectionClass;
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

        if ($parent->extends === null) {
            return [self::ERROR_MESSAGE];
        }

        if ($node instanceof Property) {
            return [];
        }

        $methodName = (string) $node->name;
        if ($this->parentMethodAnalyser->hasParentClassMethodWithSameName($scope, $methodName)) {
            return [];
        }

        if ($this->isExistInTraits($parent, $methodName)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    private function isExistInTraits(Class_ $class, string $methodName): bool
    {
        $usedTraits = class_uses((string) $class);
        foreach ($usedTraits as $trait) {
            $r = new ReflectionClass((string) $trait);
            if (in_array($methodName, $r->getMethods(), true)) {
                return true;
            }
        }

        return false;
    }
}
