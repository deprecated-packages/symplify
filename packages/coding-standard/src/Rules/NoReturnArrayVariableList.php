<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\PHPStan\ParentMethodAnalyser;
use Symplify\EasyTesting\PHPUnit\StaticPHPUnitEnvironment;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoReturnArrayVariableList\NoReturnArrayVariableListTest
 */
final class NoReturnArrayVariableList implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use value object over return of values';

    /**
     * @var ParentMethodAnalyser
     */
    private $parentMethodAnalyser;

    public function __construct(ParentMethodAnalyser $parentMethodAnalyser)
    {
        $this->parentMethodAnalyser = $parentMethodAnalyser;
    }

    public function getNodeType(): string
    {
        return Return_::class;
    }

    /**
     * @param Return_ $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($this->shouldSkip($scope, $node)) {
            return [];
        }

        /** @var Array_ $array */
        $array = $node->expr;

        $itemCount = count((array) $array->items);
        if ($itemCount < 2) {
            return [];
        }

        $exprCount = $this->resolveExprCount($array);
        if ($exprCount < 2) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    private function shouldSkip(Scope $scope, $node): bool
    {
        // skip tests
        if (Strings::match($scope->getFile(), '#\/Tests\/#i') && ! StaticPHPUnitEnvironment::isPHPUnitRun()) {
            return true;
        }

        // skip value objects
        if (Strings::match($scope->getFile(), '#\/ValueObject\/#i')) {
            return true;
        }

        if (! $node->expr instanceof Array_) {
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
        foreach ((array) $array->items as $item) {
            if (! $item instanceof ArrayItem) {
                continue;
            }

            if (! $item->value instanceof Expr) {
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
