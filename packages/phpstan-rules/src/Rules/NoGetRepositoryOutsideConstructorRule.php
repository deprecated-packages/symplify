<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use Symplify\CodingStandard\ValueObject\MethodName;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoGetRepositoryOutsideConstructorRule\NoGetRepositoryOutsideConstructorRuleTest
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
}
