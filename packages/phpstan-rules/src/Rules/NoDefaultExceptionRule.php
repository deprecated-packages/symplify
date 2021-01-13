<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt\Throw_;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Throwable;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoDefaultExceptionRule\NoDefaultExceptionRuleTest
 */
final class NoDefaultExceptionRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use custom exceptions instead of native "%s"';

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
        return [Throw_::class];
    }

    /**
     * @param Throw_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $thrownExpr = $node->expr;
        if (! $thrownExpr instanceof New_) {
            return [];
        }

        $className = $this->simpleNameResolver->getName($thrownExpr->class);
        if ($className === null) {
            return [];
        }

        if (! is_a($className, Throwable::class, true)) {
            return [];
        }

        // fast way to detect native exceptions
        if (Strings::contains($className, '\\')) {
            return [];
        }

        return [sprintf(self::ERROR_MESSAGE, $className)];
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
