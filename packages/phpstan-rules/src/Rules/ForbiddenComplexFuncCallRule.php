<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenComplexFuncCallRule\ForbiddenComplexFuncCallRuleTest
 */
final class ForbiddenComplexFuncCallRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use "%s" function with complex content, make it more readable with extracted method or single-line statement';

    /**
     * @var int
     */
    private const MAXIMUM_ALLOWED_STMT_COUNT = 2;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var string[]
     */
    private $forbiddenComplexFunctions = [];

    /**
     * @param string[] $forbiddenComplexFunctions
     */
    public function __construct(SimpleNameResolver $simpleNameResolver, array $forbiddenComplexFunctions = [])
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->forbiddenComplexFunctions = $forbiddenComplexFunctions;
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
        if (! $this->simpleNameResolver->isNames($node, $this->forbiddenComplexFunctions)) {
            return [];
        }

        $secondArgValue = $node->args[1]->value;
        if (! $secondArgValue instanceof Closure) {
            return [];
        }

        if (count($secondArgValue->stmts) < self::MAXIMUM_ALLOWED_STMT_COUNT) {
            return [];
        }

        $funcCallName = $this->simpleNameResolver->getName($node);
        return [sprintf(self::ERROR_MESSAGE, $funcCallName)];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
$filteredElements = array_filter($elemnets, function ($item) {
    if ($item) {
        return true;
    }

    if ($item === null) {
        return true;
    }

    return false;
};
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$filteredElements = array_filter($elemnets, function ($item) {
    return $item instanceof KeepItSimple;
};
CODE_SAMPLE
            ),
        ]);
    }
}
