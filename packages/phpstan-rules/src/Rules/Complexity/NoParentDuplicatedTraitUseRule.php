<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Complexity;

use PhpParser\Node;
use PhpParser\Node\Stmt\TraitUse;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Complexity\NoParentDuplicatedTraitUseRule\NoParentDuplicatedTraitUseRuleTest
 */
final class NoParentDuplicatedTraitUseRule implements \PHPStan\Rules\Rule, \Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'The "%s" trait is already used in parent class. Remove it here';

    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeType(): string
    {
        return TraitUse::class;
    }

    /**
     * @param TraitUse $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $parentUsedTraitNames = $this->resolveParentClassUsedTraitNames($scope);
        if ($parentUsedTraitNames === []) {
            return [];
        }

        $errorMessages = [];
        foreach ($node->traits as $traitName) {
            if (! $this->simpleNameResolver->isNames($traitName, $parentUsedTraitNames)) {
                continue;
            }

            $errorMessages[] = sprintf(self::ERROR_MESSAGE, $traitName);
        }

        return $errorMessages;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            self::ERROR_MESSAGE,
            [new CodeSample(
                <<<'CODE_SAMPLE'
class ParentClass
{
    use SomeTrait;
}

class SomeClass extends ParentClass
{
    use SomeTrait;
}
CODE_SAMPLE
    ,
                <<<'CODE_SAMPLE'
class ParentClass
{
    use SomeTrait;
}

class SomeClass extends ParentClass
{
}
CODE_SAMPLE
            )]
        );
    }

    /**
     * @return string[]
     */
    private function resolveParentClassUsedTraitNames(Scope $scope): array
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        $parentClassReflection = $classReflection->getParentClass();
        if (! $parentClassReflection instanceof ClassReflection) {
            return [];
        }

        return array_keys($parentClassReflection->getTraits());
    }
}
