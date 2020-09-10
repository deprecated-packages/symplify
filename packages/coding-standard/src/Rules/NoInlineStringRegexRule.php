<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoInlineStringRegexRule\NoInlineStringRegexRuleTest
 */
final class NoInlineStringRegexRule extends AbstractManyNodeTypeRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use local named constant instead of inline string for regex to explain meaning by constant name';

    /**
     * @var string[]
     */
    private const FUNC_CALLS_WITH_FIRST_ARG_REGEX = [
        'preg_match',
        'preg_match_all',
        'preg_split',
        'preg_replace',
        'preg_replace_callback',
    ];

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [StaticCall::class, FuncCall::class];
    }

    /**
     * @param StaticCall|FuncCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($node instanceof FuncCall) {
            if ($node->name instanceof Expr) {
                return [];
            }

            $funcCallName = (string) $node->name;
            if (! in_array($funcCallName, self::FUNC_CALLS_WITH_FIRST_ARG_REGEX, true)) {
                return [];
            }

            $firstArgValue = $node->args[0]->value;

            // it's not string → good
            if (! $firstArgValue instanceof String_) {
                return [];
            }

            return [self::ERROR_MESSAGE];
        }

        return [];
    }
}
