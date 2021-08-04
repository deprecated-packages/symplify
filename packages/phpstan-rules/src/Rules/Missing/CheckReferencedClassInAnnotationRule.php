<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Missing;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDocParser\Ast\PhpDoc\GenericTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use Symplify\Astral\ValueObject\AttributeKey;
use Symplify\PHPStanRules\PhpDoc\PhpDocNodeTraverser\ClassReferencePhpDocNodeTraverser;
use Symplify\PHPStanRules\Reflection\ClassReflectionResolver;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\PHPStanRules\ValueObject\ClassConstantReference;
use Symplify\PHPStanRules\ValueObject\MethodCallReference;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Symplify\SimplePhpDocParser\SimplePhpDocParser;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Missing\CheckReferencedClassInAnnotationRule\CheckReferencedClassInAnnotationRuleTest
 */
final class CheckReferencedClassInAnnotationRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class "%s" used in annotation is missing';

    /**
     * @var string
     */
    public const CONSTANT_ERROR_MESSAGE = 'Constant "%s" not found on "%s" class';

    /**
     * @var string
     */
    public const METHOD_ERROR_MESSAGE = 'Method "%s" not found on "%s" class';

    public function __construct(
        private SimplePhpDocParser $simplePhpDocParser,
        private ReflectionProvider $reflectionProvider,
        private ClassReflectionResolver $classReflectionResolver,
        private ClassReferencePhpDocNodeTraverser $classReferencePhpDocNodeTraverser
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Property::class, ClassMethod::class, Class_::class];
    }

    /**
     * @param Property|ClassMethod|Class_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $simplePhpDocNode = $this->simplePhpDocParser->parseNode($node);
        if (! $simplePhpDocNode instanceof PhpDocNode) {
            return [];
        }

        $classReflection = $this->classReflectionResolver->resolve($scope, $node);
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        $this->classReferencePhpDocNodeTraverser->decoratePhpDocNode($simplePhpDocNode, $classReflection);

        foreach ($simplePhpDocNode->getTags() as $phpDocTagNode) {
            if (! $phpDocTagNode->value instanceof GenericTagValueNode) {
                continue;
            }

            $genericTagValueNode = $phpDocTagNode->value;

            $classErrorMessages = $this->collectClassErrorMessages($genericTagValueNode);
            $classConstantsErrorMessages = $this->collectClassConstantsErrorMessages($genericTagValueNode);
            $methodCallsErrorMessages = $this->collectMethodCallsErrorMessages($genericTagValueNode);

            return array_merge($classErrorMessages, $classConstantsErrorMessages, $methodCallsErrorMessages);
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
/**
 * @SomeAnnotation(value=MissingClass::class)
 */
class SomeClass
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
/**
 * @SomeAnnotation(value=ExistingClass::class)
 */
class SomeClass
{
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return string[]
     */
    private function collectClassConstantsErrorMessages(GenericTagValueNode $genericTagValueNode): array
    {
        $errorMessages = [];

        /** @var ClassConstantReference[] $referencedClassConstants */
        $referencedClassConstants = $genericTagValueNode->getAttribute(AttributeKey::REFERENCED_CLASS_CONSTANTS);
        foreach ($referencedClassConstants as $referencedClassConstant) {
            $class = $referencedClassConstant->getClass();
            if (! $this->reflectionProvider->hasClass($class)) {
                $errorMessages[] = sprintf(self::ERROR_MESSAGE, $class);
                continue;
            }

            $classReflection = $this->reflectionProvider->getClass($class);
            $constant = $referencedClassConstant->getConstant();
            if ($classReflection->hasConstant($constant)) {
                continue;
            }

            $errorMessages[] = sprintf(self::CONSTANT_ERROR_MESSAGE, $constant, $class);
        }

        return $errorMessages;
    }

    /**
     * @return string[]
     */
    private function collectMethodCallsErrorMessages(GenericTagValueNode $genericTagValueNode): array
    {
        $errorMessages = [];

        /** @var MethodCallReference[] $referencedMethodCalls */
        $referencedMethodCalls = $genericTagValueNode->getAttribute(AttributeKey::REFERENCED_METHOD_CALLS);
        foreach ($referencedMethodCalls as $referencedMethodCall) {
            $class = $referencedMethodCall->getClass();
            if (! $this->reflectionProvider->hasClass($class)) {
                $errorMessages[] = sprintf(self::ERROR_MESSAGE, $class);
                continue;
            }

            $classReflection = $this->reflectionProvider->getClass($class);
            $method = $referencedMethodCall->getMethodName();
            if ($classReflection->hasMethod($method)) {
                continue;
            }

            $errorMessages[] = sprintf(self::METHOD_ERROR_MESSAGE, $method, $class);
        }
        return $errorMessages;
    }

    /**
     * @return string[]
     */
    private function collectClassErrorMessages(GenericTagValueNode $genericTagValueNode): array
    {
        $errorMessages = [];

        /** @var string[] $referencedClasses */
        $referencedClasses = $genericTagValueNode->getAttribute(AttributeKey::REFERENCED_CLASSES);

        foreach ($referencedClasses as $referencedClass) {
            if ($this->reflectionProvider->hasClass($referencedClass)) {
                continue;
            }

            /** @var string $referencedClass */
            $errorMessages[] = sprintf(self::ERROR_MESSAGE, $referencedClass);
        }

        return $errorMessages;
    }
}
