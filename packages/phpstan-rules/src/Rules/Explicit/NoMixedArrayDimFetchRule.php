<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ArrayType;
use PHPStan\Type\IntersectionType;
use PHPStan\Type\MixedType;
use PHPStan\Type\UnionType;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Detect array dim fetch assigns to unknown arrays. The dim type e.g. array<string, mixed> should be defined there.
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedArrayDimFetchRule\NoMixedArrayDimFetchRuleTest
 */
final class NoMixedArrayDimFetchRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Add explicit array type to assigned "%s" expression';

    private Standard $printerStandard;

    public function __construct()
    {
        $this->printerStandard = new Standard();
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Assign::class];
    }

    /**
     * @param Assign $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $node->var instanceof ArrayDimFetch) {
            return [];
        }

        $arrayDimFetch = $node->var;

        // only check for exact dim values
        if ($arrayDimFetch->dim === null) {
            return [];
        }

        if (! $arrayDimFetch->var instanceof PropertyFetch && ! $arrayDimFetch->var instanceof Variable) {
            return [];
        }

        $rootDimFetchType = $scope->getType($arrayDimFetch->var);

        // skip complex types for now
        if ($this->shouldSkipRootDimFetchType($rootDimFetchType)) {
            return [];
        }

        $printedVariable = $this->printerStandard->prettyPrintExpr($arrayDimFetch->var);

        return [sprintf(self::ERROR_MESSAGE, $printedVariable)];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [new CodeSample(
            <<<'CODE_SAMPLE'
class SomeClass
{
    private $items = [];

    public function addItem(string $key, string $value)
    {
        $this->items[$key] = $value;
    }
}
CODE_SAMPLE
,
            <<<'CODE_SAMPLE'
class SomeClass
{
    /**
     * @var array<string, string>
     */
    private $items = [];

    public function addItem(string $key, string $value)
    {
        $this->items[$key] = $value;
    }
}
CODE_SAMPLE
        ),
        ]);
    }

    private function shouldSkipRootDimFetchType(\PHPStan\Type\Type $rootDimFetchType): bool
    {
        if ($rootDimFetchType instanceof UnionType) {
            return true;
        }

        if ($rootDimFetchType instanceof IntersectionType) {
            return true;
        }

        if ($rootDimFetchType instanceof \PHPStan\Type\StringType) {
            return true;
        }

        if ($rootDimFetchType instanceof ArrayType && ! $rootDimFetchType->getKeyType() instanceof MixedType) {
            return true;
        }

        return false;
    }
}
