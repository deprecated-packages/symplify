<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenExtendOfNonAbstractClassRule\ForbiddenExtendOfNonAbstractClassRuleTest
 */
final class ForbiddenExtendOfNonAbstractClassRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Only abstract classes can be extended';

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $node->getClassReflection();

        $parentClassReflection = $classReflection->getParentClass();
        if (! $parentClassReflection instanceof ClassReflection) {
            return [];
        }

        if ($parentClassReflection->isAbstract()) {
            return [];
        }

        // skip native PHP classes
        if ($parentClassReflection->isBuiltin()) {
            return [];
        }

        // skip vendor based classes, as designed for extension
        $fileName = $parentClassReflection->getFileName();
        if (is_string($fileName) && str_contains($fileName, 'vendor')) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class SomeClass extends ParentClass
{
}

class ParentClass
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class SomeClass extends ParentClass
{
}

abstract class ParentClass
{
}
CODE_SAMPLE
            ),
        ]);
    }
}
