<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassConst;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\ForbiddenArrayWithStringKeysRule\ForbiddenArrayWithStringKeysRuleTest
 */
final class ForbiddenArrayWithStringKeysRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Array with keys is not allowed. Use value object to pass data instead';

    /**
     * @var string
     */
    private const TEXT_FILE_REGEX = '#(Test|TestCase)\.php$#';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Array_::class];
    }

    /**
     * @param Array_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($this->shouldSkip($node, $scope)) {
            return [];
        }

        foreach ($node->items as $arrayItem) {
            if ($arrayItem === null) {
                continue;
            }

            /** @var ArrayItem $arrayItem */
            if ($arrayItem->key === null) {
                continue;
            }

            if (! $arrayItem->key instanceof String_) {
                continue;
            }

            return [self::ERROR_MESSAGE];
        }

        return [];
    }

    private function shouldSkip(Array_ $array, Scope $scope): bool
    {
        if (Strings::match($scope->getFile(), self::TEXT_FILE_REGEX)) {
            return true;
        }

        // skip examples in Rector::getDefinition() method
        if ($scope->getFunctionName() === 'getDefinition') {
            return true;
        }

        if ($scope->getFunctionName() === '__construct') {
            return true;
        }

        return $this->isPartOfClassConstOrNew($array);
    }

    private function isPartOfClassConstOrNew(Node $currentNode): bool
    {
        while ($currentNode = $currentNode->getAttribute('parent')) {
            // constants can have default values
            if ($currentNode instanceof ClassConst) {
                return true;
            }

            // the array with string keys is required by the object parameters
            if ($currentNode instanceof New_) {
                return true;
            }

            if ($currentNode instanceof MethodCall) {
                return true;
            }

            if ($currentNode instanceof StaticCall) {
                return true;
            }

            if ($currentNode instanceof FuncCall) {
                return true;
            }
        }

        return false;
    }
}
