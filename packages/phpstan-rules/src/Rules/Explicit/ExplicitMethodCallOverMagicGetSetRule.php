<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\TypeWithClassName;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\ValueObject\AttributeKey;
use Symplify\PHPStanRules\Reflection\PublicClassReflectionAnalyzer;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Explicit\ExplicitMethodCallOverMagicGetSetRule\ExplicitMethodCallOverMagicGetSetRuleTest
 */
final class ExplicitMethodCallOverMagicGetSetRule extends AbstractSymplifyRule
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
        private SimpleNameResolver $simpleNameResolver,
        private PublicClassReflectionAnalyzer $publicClassReflectionAnalyzer
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class MagicCallsObject
{
    // adds magic __get() and __set() methods
    use \Nette\SmartObject;

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
class MagicCallsObject
{
    // adds magic __get() and __set() methods
    use \Nette\SmartObject;

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
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [PropertyFetch::class];
    }

    /**
     * @param PropertyFetch $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        // skip local "$this" calls
        if ($this->isVariableThis($node->var)) {
            return [];
        }

        $callerClassReflection = $this->resolveClassReflection($scope, $node->var);
        if (! $callerClassReflection instanceof ClassReflection) {
            return [];
        }

        $propertyName = $this->simpleNameResolver->getName($node->name);
        if ($propertyName === null) {
            return [];
        }

        // has public native property?
        if ($this->publicClassReflectionAnalyzer->hasPublicNativeProperty($callerClassReflection, $propertyName)) {
            return [];
        }

        $parent = $node->getAttribute(AttributeKey::PARENT);
        if ($parent instanceof Assign && $parent->var === $node) {
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

        return $this->simpleNameResolver->isName($expr, 'this');
    }
}
