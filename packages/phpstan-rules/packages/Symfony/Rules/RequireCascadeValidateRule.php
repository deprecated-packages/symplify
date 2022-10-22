<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ArrayType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use Symplify\PHPStanRules\Exception\ShouldNotHappenException;
use Symplify\PHPStanRules\Symfony\Finder\ArrayKeyFinder;
use Symplify\PHPStanRules\Symfony\PropertyMetadataResolver;
use Symplify\PHPStanRules\Symfony\ValueObject\PropertyMetadata;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @implements Rule<InClassNode>
 *
 * @see \Symplify\PHPStanRules\Tests\Symfony\Rules\RequireCascadeValidateRule\RequireCascadeValidateRuleTest
 */
final class RequireCascadeValidateRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Property "$%s" is missing @Valid annotation';

    public function __construct(
        private ReflectionProvider $reflectionProvider,
        private ArrayKeyFinder $arrayKeyFinder,
        private PropertyMetadataResolver $propertyMetadataResolver,
    ) {
    }

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     * @return RuleError[]|string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $node->getClassReflection();
        if (! $classReflection->isSubclassOf('Symfony\Component\Form\AbstractType')) {
            return [];
        }

        /** @var Class_ $classLike */
        $classLike = $node->getOriginalNode();
        $configureOptionsClassMethod = $classLike->getMethod('configureOptions');

        if ($configureOptionsClassMethod instanceof ClassMethod) {
            return $this->processConfigureOptionsClassMethod($configureOptionsClassMethod, $scope);
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

final class NullablePropertyFormType extends AbstractType
{
    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'data_class' => SomeEntity::class,
            'constraints' => new Valid(),
        ]);
    }
}

class SomeEntity
{
    /**
     * @var NestedEntity
     */
    private $nestedEntity;
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Symfony\Component\Validator\Constraints as Assert;

class SomeEntity
{
    /**
     * @Assert\Valid
     * @var NestedEntity
     */
    private $nestedEntity;
}
CODE_SAMPLE
            ), ]);
    }

    private function hasConstraintValidOption(ClassMethod $configureOptionsClassMethod, Scope $scope): bool
    {
        $expr = $this->arrayKeyFinder->findArrayItemExprByKeyName($configureOptionsClassMethod, 'constraints');
        if (! $expr instanceof New_) {
            return false;
        }

        if ($expr->class instanceof Expr) {
            $classType = $scope->getType($expr->class);
            if (! $classType instanceof ConstantStringType) {
                return false;
            }

            $className = $classType->getValue();
        } elseif ($expr->class instanceof Name) {
            $className = $expr->class->toString();
        } else {
            return false;
        }

        return $className === 'Symfony\Component\Validator\Constraints\Valid';
    }

    private function resolveDataClassReflection(ClassMethod $classMethod, Scope $scope): ?ClassReflection
    {
        $expr = $this->arrayKeyFinder->findArrayItemExprByKeyName($classMethod, 'data_class');
        if (! $expr instanceof Expr) {
            return null;
        }

        $valueType = $scope->getType($expr);
        if (! $valueType instanceof ConstantStringType) {
            return null;
        }

        $value = $valueType->getValue();
        if (! $this->reflectionProvider->hasClass($value)) {
            return null;
        }

        return $this->reflectionProvider->getClass($value);
    }

    /**
     * @return RuleError[]
     */
    private function processConfigureOptionsClassMethod(ClassMethod $configureOptionsClassMethod, Scope $scope): array
    {
        if (! $this->hasConstraintValidOption($configureOptionsClassMethod, $scope)) {
            return [];
        }

        $dataClassReflection = $this->resolveDataClassReflection($configureOptionsClassMethod, $scope);
        if (! $dataClassReflection instanceof ClassReflection) {
            return [];
        }

        $propertyMetadatas = $this->propertyMetadataResolver->resolvePropertyMetadatas($dataClassReflection, $scope);

        $ruleErrors = [];

        foreach ($propertyMetadatas as $propertyMetadata) {
            // is property with object type?
            $propertyType = $propertyMetadata->getPropertyType();

            $propertyType = $this->unwrapType($propertyType);
            if (! $propertyType instanceof ObjectType) {
                continue;
            }

            // skip PHP native classes
            $classReflection = $propertyType->getClassReflection();
            if (! $classReflection instanceof ClassReflection) {
                throw new ShouldNotHappenException();
            }

            if ($classReflection->isBuiltin()) {
                continue;
            }

            // does it have @Valid annotation?
            if ($this->hasValidAnnotation($propertyMetadata)) {
                continue;
            }

            $errorMessage = sprintf(self::ERROR_MESSAGE, $propertyMetadata->getPropertyName());

            $ruleErrors[] = RuleErrorBuilder::message($errorMessage)
                ->file($propertyMetadata->getFileName())
                ->line($propertyMetadata->getPropertyLine())
                ->build();
        }

        return $ruleErrors;
    }

    private function hasValidAnnotation(PropertyMetadata $propertyMetadata): bool
    {
        if (str_contains($propertyMetadata->getDocComment(), '@Valid')) {
            return true;
        }

        return str_contains($propertyMetadata->getDocComment(), '@Assert\Valid');
    }

    private function unwrapType(Type $type): Type
    {
        if (TypeCombinator::containsNull($type)) {
            $type = TypeCombinator::removeNull($type);
        }

        // collection types
        if ($type instanceof ArrayType) {
            return $type->getItemType();
        }

        return $type;
    }
}
