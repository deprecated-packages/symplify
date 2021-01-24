<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoNestedFuncCallRule\NoNestedFuncCallRuleTest
 */
final class NoNestedFuncCallRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use separate function calls with readable variable names';

    /**
     * @var string[]
     */
    private const ALLOWED_FUNC_NAMES = ['count', 'trim', 'ltrim', 'rtrim', 'get_class', 'implode', 'strlen', 'getcwd'];

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    /**
     * @param FuncCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        foreach ($node->args as $arg) {
            if (! $arg->value instanceof FuncCall) {
                continue;
            }

            $funcCall = $arg->value;

            if ($this->simpleNameResolver->isNames($funcCall, self::ALLOWED_FUNC_NAMES)) {
                continue;
            }

            return [self::ERROR_MESSAGE];
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        return array_filter(array_map($callback, $items));
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        $mappedItems = array_map($callback, $items);
        return array_filter($mappedItems);
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
