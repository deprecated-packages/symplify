<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use PHPStan\Type\MixedType;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedMethodCallerRule\NoMixedMethodCallerRuleTest
 */
final class NoMixedMethodCallerRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Anonymous variables in a method call can lead to false dead methods. Make sure the variable type is known in method name `%s()`.';

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param MethodCall $node
     * @return mixed[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $callerType = $scope->getType($node->var);
        if (! $callerType instanceof MixedType) {
            return [];
        }

        /** @var string $name */
        $name = $node->name instanceof Variable ? $node->name->name : $node->name;

        return [sprintf(self::ERROR_MESSAGE, $name)];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
function run($unknownType)
{
    return $unknownType->call();
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
function run(KnownType $knownType)
{
    return $knownType->call();
}
CODE_SAMPLE
            ),
        ]);
    }
}
