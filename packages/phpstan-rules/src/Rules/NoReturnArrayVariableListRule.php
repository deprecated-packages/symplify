<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use Symplify\EasyTesting\PHPUnit\StaticPHPUnitEnvironment;
use Symplify\PHPStanRules\ParentMethodAnalyser;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoReturnArrayVariableListRule\NoReturnArrayVariableListRuleTest
 */
final class NoReturnArrayVariableListRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use value object over return of values';

    /**
     * @var string
     * @see https://regex101.com/r/C5d1zH/1
     */
    private const TESTS_DIRECTORY_REGEX = '#\/Tests\/#i';

    public function __construct(
        private ParentMethodAnalyser $parentMethodAnalyser
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Return_::class];
    }

    /**
     * @param Return_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($this->shouldSkip($scope, $node)) {
            return [];
        }

        /** @var Array_ $array */
        $array = $node->expr;

        $itemCount = count($array->items);
        if ($itemCount < 2) {
            return [];
        }

        $exprCount = $this->resolveExprCount($array);
        if ($exprCount < 2) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class ReturnVariables
{
    public function run($value, $value2): array
    {
        return [$value, $value2];
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class ReturnVariables
{
    public function run($value, $value2): ValueObject
    {
        return new ValueObject($value, $value2);
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function shouldSkip(Scope $scope, Return_ $return): bool
    {
        // skip tests
        if (Strings::match(
            $scope->getFile(),
            self::TESTS_DIRECTORY_REGEX
        ) && ! StaticPHPUnitEnvironment::isPHPUnitRun()) {
            return true;
        }

        $namespace = $scope->getNamespace();
        if ($namespace === null) {
            return true;
        }

        if (str_contains($namespace, 'Enum')) {
            return true;
        }

        if (str_contains($namespace, 'ValueObject')) {
            return true;
        }

        if (! $return->expr instanceof Array_) {
            return true;
        }

        // guarded by parent method

        $functionLike = $scope->getFunction();
        if ($functionLike instanceof MethodReflection) {
            return $this->parentMethodAnalyser->hasParentClassMethodWithSameName($scope, $functionLike->getName());
        }

        return false;
    }

    private function resolveExprCount(Array_ $array): int
    {
        $exprCount = 0;
        foreach ($array->items as $item) {
            if (! $item instanceof ArrayItem) {
                continue;
            }

            if ($item->value instanceof New_) {
                continue;
            }

            ++$exprCount;
        }

        return $exprCount;
    }
}
