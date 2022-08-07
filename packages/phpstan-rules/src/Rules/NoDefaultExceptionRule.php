<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Throw_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Throwable;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoDefaultExceptionRule\NoDefaultExceptionRuleTest
 */
final class NoDefaultExceptionRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use custom exceptions instead of native "%s"';

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return Throw_::class;
    }

    /**
     * @param Throw_ $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $thrownExpr = $node->expr;
        if (! $thrownExpr instanceof New_) {
            return [];
        }

        if (! $thrownExpr->class instanceof Name) {
            return [];
        }

        $exceptionClassName = $thrownExpr->class->toString();
        if (! is_a($exceptionClassName, Throwable::class, true)) {
            return [];
        }

        // fast way to detect native exceptions
        if (\str_contains($exceptionClassName, '\\')) {
            return [];
        }

        return [sprintf(self::ERROR_MESSAGE, $exceptionClassName)];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
throw new RuntimeException('...');
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use App\Exception\FileNotFoundException;

throw new FileNotFoundException('...');
CODE_SAMPLE
            ),
        ]);
    }
}
