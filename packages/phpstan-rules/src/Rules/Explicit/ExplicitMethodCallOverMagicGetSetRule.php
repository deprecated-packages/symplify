<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Type\TypeWithClassName;
use Symplify\PHPStanRules\Enum\AttributeKey;
use Symplify\PHPStanRules\Reflection\PublicClassReflectionAnalyzer;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Explicit\ExplicitMethodCallOverMagicGetSetRule\ExplicitMethodCallOverMagicGetSetRuleTest
 */
final class ExplicitMethodCallOverMagicGetSetRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of magic property "%s" access use direct explicit "%s->%s()" method call';

    /**
     * @var string
     */
    private const GET_METHOD_NAME = '__get';

    /**
     * @var string
     */
    private const SET_METHOD_NAME = '__set';

    public function __construct(
        private PublicClassReflectionAnalyzer $publicClassReflectionAnalyzer
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Nette\SmartObject;

final class MagicObject
{
    // adds magic __get() and __set() methods
    use SmartObject;

    private $name;

    public function getName()
    {
        return $this->name;
    }
}

$magicObject = new MagicObject();
// magic re-directed to method
$magicObject->name;
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Nette\SmartObject;

final class MagicObject
{
    // adds magic __get() and __set() methods
    use SmartObject;

    private $name;

    public function getName()
    {
        return $this->name;
    }
}

$magicObject = new MagicObject();
// explicit
$magicObject->getName();
CODE_SAMPLE
            ),

        ]);
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return PropertyFetch::class;
    }

    /**
     * @param PropertyFetch $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // skip local "$this" calls
        if ($this->isVariableThis($node->var)) {
            return [];
        }

        $callerClassReflection = $this->resolveClassReflection($scope, $node->var);
        if (! $callerClassReflection instanceof ClassReflection) {
            return [];
        }

        if (! $node->name instanceof Identifier) {
            return [];
        }

        $propertyName = $node->name->toString();

        // has public native property?
        if ($this->publicClassReflectionAnalyzer->hasPublicNativeProperty($callerClassReflection, $propertyName)) {
            return [];
        }

        if ($scope->isInExpressionAssign($node)) {
            return $this->processSetterMethodCall($node, $callerClassReflection, $propertyName);
        }

        return $this->processGetterMethodCall($node, $callerClassReflection, $propertyName);
    }

    private function resolveClassReflection(Scope $scope, Expr $expr): ClassReflection|null
    {
        $callerType = $scope->getType($expr);
        if (! $callerType instanceof TypeWithClassName) {
            return null;
        }

        return $callerType->getClassReflection();
    }

    /**
     * @return string[]
     */
    private function processSetterMethodCall(
        PropertyFetch $propertyFetch,
        ClassReflection $classReflection,
        string $propertyName
    ): array {
        if (! $classReflection->hasNativeMethod(self::SET_METHOD_NAME)) {
            return [];
        }

        $setterMethodName = 'set' . \ucfirst($propertyName);
        if (! $this->publicClassReflectionAnalyzer->hasPublicNativeMethod($classReflection, $setterMethodName)) {
            return [];
        }

        $errorMessage = $this->createErrorMessage($propertyFetch, $propertyName, $setterMethodName);
        return [$errorMessage];
    }

    /**
     * @return string[]
     */
    private function processGetterMethodCall(
        PropertyFetch $propertyFetch,
        ClassReflection $classReflection,
        string $propertyName
    ): array {
        if (! $classReflection->hasNativeMethod(self::GET_METHOD_NAME)) {
            return [];
        }

        $getterMethodName = 'get' . \ucfirst($propertyName);
        $isserMethodName = 'is' . \ucfirst($propertyName);

        $possibleMethodNames = [$getterMethodName, $isserMethodName];

        foreach ($possibleMethodNames as $possibleMethodName) {
            if (! $this->publicClassReflectionAnalyzer->hasPublicNativeMethod($classReflection, $possibleMethodName)) {
                continue;
            }

            $errorMessage = $this->createErrorMessage($propertyFetch, $propertyName, $possibleMethodName);
            return [$errorMessage];
        }

        return [];
    }

    private function createErrorMessage(PropertyFetch $propertyFetch, string $propertyName, string $methodName): string
    {
        $printedPropertyFetch = (string) $propertyFetch->var->getAttribute(AttributeKey::PHPSTAN_CACHE_PRINTER);
        return \sprintf(self::ERROR_MESSAGE, $propertyName, $printedPropertyFetch, $methodName);
    }

    private function isVariableThis(Expr $expr): bool
    {
        if (! $expr instanceof Variable) {
            return false;
        }

        if (! is_string($expr->name)) {
            return false;
        }

        return $expr->name === 'this';
    }
}
