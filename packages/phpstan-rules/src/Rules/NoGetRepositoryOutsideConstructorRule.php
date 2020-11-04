<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\ValueObject\MethodName;
use Symplify\RuleDocGenerator\ValueObject\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoGetRepositoryOutsideConstructorRule\NoGetRepositoryOutsideConstructorRuleTest
 */
final class NoGetRepositoryOutsideConstructorRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use "$entityManager->getRepository()" outside of the constructor of repository service or setUp() method in test case';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param MethodCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $node->name instanceof Identifier) {
            return [];
        }

        $methodCallName = (string) $node->name;
        if ($methodCallName !== 'getRepository') {
            return [];
        }

        $functionReflection = $scope->getFunction();
        if ($functionReflection === null) {
            return [];
        }

        if (in_array($functionReflection->getName(), [MethodName::CONSTRUCTOR, 'setUp'], true)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class SomeController
{
    public function someAction(EntityManager $entityManager): void
    {
        $someEntityRepository = $entityManager->getRepository(SomeEntity::class);
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'

CODE_SAMPLE
            ),
        ]);
    }
}
