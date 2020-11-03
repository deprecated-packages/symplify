<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

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
use PHPStan\Type\ArrayType;
use Symplify\CodingStandard\ValueObject\MethodName;
use Symplify\CodingStandard\ValueObject\PHPStanAttributeKey;
use Symplify\PHPStanRules\ParentGuard\ParentMethodReturnTypeResolver;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenArrayWithStringKeysRule\ForbiddenArrayWithStringKeysRuleTest
 */
final class ForbiddenArrayWithStringKeysRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Array with keys is not allowed. Use value object to pass data instead';

    /**
     * @var string
     * @see https://regex101.com/r/ddj4mB/2
     */
    private const TEST_FILE_REGEX = '#(Test|TestCase)\.php$#';

    /**
     * @var ParentMethodReturnTypeResolver
     */
    private $parentMethodReturnTypeResolver;

    public function __construct(ParentMethodReturnTypeResolver $parentMethodReturnTypeResolver)
    {
        $this->parentMethodReturnTypeResolver = $parentMethodReturnTypeResolver;
    }

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
        if ($this->shouldSkipArray($node, $scope)) {
            return [];
        }

        if (! $this->isArrayWithStringKey($node)) {
            return [];
        }

        // is return array required by parent
        $parentMethodReturnType = $this->parentMethodReturnTypeResolver->resolve($scope);
        if ($parentMethodReturnType instanceof ArrayType) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    private function shouldSkipArray(Array_ $array, Scope $scope): bool
    {
        if (Strings::match($scope->getFile(), self::TEST_FILE_REGEX)) {
            return true;
        }

        // skip examples in Rector::getDefinition() method
        if (in_array($scope->getFunctionName(), ['getDefinition', MethodName::CONSTRUCTOR], true)) {
            return true;
        }

        return $this->isPartOfClassConstOrNew($array);
    }

    private function isPartOfClassConstOrNew(Node $currentNode): bool
    {
        while ($currentNode = $currentNode->getAttribute(PHPStanAttributeKey::PARENT)) {
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

    private function isArrayWithStringKey(Array_ $array): bool
    {
        foreach ($array->items as $arrayItem) {
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

            return true;
        }

        return false;
    }
}
