<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;
use Symplify\RuleDocGenerator\ValueObject\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenSpreadOperatorRule\ForbiddenSpreadOperatorRuleTest
 */
final class ForbiddenSpreadOperatorRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Spread operator is not allowed.';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Arg::class, ClassMethod::class, Function_::class];
    }

    /**
     * @param Arg|ClassMethod|Function_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($node instanceof Arg && $node->unpack) {
            return [self::ERROR_MESSAGE];
        }

        if (($node instanceof ClassMethod || $node instanceof Function_) && $this->hasVariadicParam($node)) {
            return [self::ERROR_MESSAGE];
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
$args = [$firstValue, $secondValue];
$message =  sprintf('%s', ...$args);
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$message =  sprintf('%s', $firstValue, $secondValue);
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @param ClassMethod|Function_ $node
     */
    private function hasVariadicParam(Node $node): bool
    {
        foreach ((array) $node->params as $param) {
            if ($param->variadic) {
                return true;
            }
        }

        return false;
    }
}
