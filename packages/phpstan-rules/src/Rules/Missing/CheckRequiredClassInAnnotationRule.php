<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Missing;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\Reflection\ReflectionProvider;
use Symplify\PHPStanRules\PhpDoc\BarePhpDocParser;
use Symplify\PHPStanRules\PhpDoc\ClassAnnotationResolver;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Missing\CheckRequiredClassInAnnotationRule\CheckRequiredClassInAnnotationRuleTest
 */
final class CheckRequiredClassInAnnotationRule extends AbstractSymplifyRule
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
     * @var BarePhpDocParser
     */
    private $barePhpDocParser;

    /**
     * @var ClassAnnotationResolver
     */
    private $classAnnotationResolver;

    /**
     * @var ReflectionProvider
     */
    private $reflectionProvider;

    public function __construct(
        BarePhpDocParser $barePhpDocParser,
        ClassAnnotationResolver $classAnnotationResolver,
        ReflectionProvider $reflectionProvider
    ) {
        $this->barePhpDocParser = $barePhpDocParser;
        $this->classAnnotationResolver = $classAnnotationResolver;
        $this->reflectionProvider = $reflectionProvider;
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
        $phpDocNode = $this->barePhpDocParser->parseNode($node);
        if (! $phpDocNode instanceof PhpDocNode) {
            return [];
        }

        // foreach with configureaiton
        $classReferences = $this->classAnnotationResolver->resolveClassReferences($node, $scope);

        foreach ($classReferences as $classReference) {
            if ($this->reflectionProvider->hasClass($classReference)) {
                continue;
            }

            $errorMessage = sprintf(self::ERROR_MESSAGE, $classReference);
            return [$errorMessage];
        }

        // foreach with configureaiton
        $classConstantReferences = $this->classAnnotationResolver->resolveClassConstantReferences($node, $scope);

        foreach ($classConstantReferences as $classConstantReference) {
            $class = $classConstantReference->getClass();
            if (! $this->reflectionProvider->hasClass($class)) {
                continue;
            }

            $classReflection = $this->reflectionProvider->getClass($class);

            $constant = $classConstantReference->getConstant();
            if ($classReflection->hasConstant($constant)) {
                continue;
            }

            $errorMessage = sprintf(self::CONSTANT_ERROR_MESSAGE, $constant, $class);
            return [$errorMessage];
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
}
