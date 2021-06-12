<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;
use PHPStan\Analyser\Scope;
use PHPStan\Type\TypeWithClassName;

use Symplify\PackageBuilder\Matcher\ArrayStringAndFnMatcher;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoArrayAccessOnObjectRule\NoArrayAccessOnObjectRuleTest
 */
final class NoArrayAccessOnObjectRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use explicit methods over array access on object';

    /**
     * @var array<class-string>
     */
    private const ALLOWED_CLASSES = ['SplFixedArray', 'SimpleXMLElement'];

    public function __construct(
        private ArrayStringAndFnMatcher $arrayStringAndFnMatcher
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ArrayDimFetch::class];
    }

    /**
     * @param ArrayDimFetch $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $varStaticType = $scope->getType($node->var);

        if (! $varStaticType instanceof TypeWithClassName) {
            return [];
        }

        if ($this->arrayStringAndFnMatcher->isMatchWithIsA($varStaticType->getClassName(), self::ALLOWED_CLASSES)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run(MagicArrayObject $magicArrayObject)
    {
        return $magicArrayObject['more_magic'];
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run(MagicArrayObject $magicArrayObject)
    {
        return $magicArrayObject->getExplicitValue();
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
