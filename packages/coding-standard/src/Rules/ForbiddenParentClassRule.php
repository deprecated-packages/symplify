<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\ForbiddenParentClassRule\ForbiddenParentClassRuleTest
 */
final class ForbiddenParentClassRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class "%s" inherits from forbidden parent class "%s". Use composition over inheritance instead';

    /**
     * @var string[]
     */
    private $forbiddenParentClasses = [];

    /**
     * @param string[] $forbiddenParentClasses
     */
    public function __construct(array $forbiddenParentClasses = [])
    {
        $this->forbiddenParentClasses = $forbiddenParentClasses;
    }

    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @param Class_ $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->name === null) {
            return [];
        }

        // no parent
        if ($node->extends === null) {
            return [];
        }

        $parentClass = $node->extends->toString();
        if ($this->shouldSkipParentClass($parentClass)) {
            return [];
        }

        $class = $node->namespacedName->toString();

        $errorMessage = sprintf(self::ERROR_MESSAGE, $class, $parentClass);

        return [$errorMessage];
    }

    private function shouldSkipParentClass(string $parentClassName): bool
    {
        foreach ($this->forbiddenParentClasses as $forbiddenParentClass) {
            if ($parentClassName === $forbiddenParentClass) {
                return false;
            }

            if (fnmatch($forbiddenParentClass, $parentClassName)) {
                return false;
            }
        }

        return true;
    }
}
