<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PackageBuilder\ValueObject\MethodName;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
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

    public function __construct(
        private SimpleNameResolver $simpleNameResolver
    ) {
    }

    /**
     * @return array<class-string<Node>>
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
        if (! $this->simpleNameResolver->isName($node->name, 'getRepository')) {
            return [];
        }

        $callerType = $scope->getType($node->var);
        $entityManagerObjectType = new ObjectType(EntityManagerInterface::class);
        if (! $entityManagerObjectType->isSuperTypeOf($callerType)->yes()) {
            return [];
        }

        $functionReflection = $scope->getFunction();
        if ($functionReflection === null) {
            return [];
        }

        if (in_array($functionReflection->getName(), [MethodName::CONSTRUCTOR, MethodName::SET_UP], true)) {
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
final class SomeRepository
{
    public function __construct(EntityManager $entityManager): void
    {
        $someEntityRepository = $entityManager->getRepository(SomeEntity::class);
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
