<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoNestedFuncCallRule\NoNestedFuncCallRuleTest
 */
final class NoNestedFuncCallRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use separate function calls with readable variable names';

    /**
     * @var string[]
     */
    private const ALLOWED_FUNC_NAMES = [
        'count',
        'trim',
        'ltrim',
        'rtrim',
        'get_class',
        'implode',
        'strlen',
        'substr',
        'getcwd',
        'is_file',
        'file_exists',
        'in_array',
        'str_contains',
        'str_starts_with',
        'str_ends_with',
    ];

    public function __construct(
        private NodeFinder $nodeFinder
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return FuncCall::class;
    }

    /**
     * @param FuncCall $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->name instanceof Name) {
            return [];
        }

        $rootFuncCallName = $node->name->toString();
        if ($rootFuncCallName === 'assert') {
            return [];
        }

        $allowedNames = array_diff(self::ALLOWED_FUNC_NAMES, [$rootFuncCallName]);

        foreach ($node->getArgs() as $arg) {
            // avoid nesting checks, e.g. usort()
            if ($arg->value instanceof Closure) {
                continue;
            }

            /** @var FuncCall[] $nestedFuncCalls */
            $nestedFuncCalls = $this->nodeFinder->findInstanceOf($arg, FuncCall::class);

            foreach ($nestedFuncCalls as $nestedFuncCall) {
                if (! $nestedFuncCall->name instanceof Name) {
                    continue;
                }

                $nestedFuncCallName = $nestedFuncCall->name->toString();
                if (in_array($nestedFuncCallName, $allowedNames, true)) {
                    continue;
                }

                return [self::ERROR_MESSAGE];
            }
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
$filteredValues = array_filter(array_map($callback, $items));
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$mappedItems = array_map($callback, $items);
$filteredValues = array_filter($mappedItems);
CODE_SAMPLE
            ),
        ]);
    }
}
