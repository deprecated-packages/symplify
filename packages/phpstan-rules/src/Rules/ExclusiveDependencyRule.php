<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassMethodNode;
use PHPStan\Rules\Rule;
use PHPStan\Type\ObjectType;
use Symplify\PHPStanRules\Enum\MethodName;
use Symplify\PHPStanRules\Matcher\ArrayStringAndFnMatcher;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ExclusiveDependencyRule\ExclusiveDependencyRuleTest
 */
final class ExclusiveDependencyRule implements Rule, DocumentedRuleInterface, ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = '"%s" dependency is allowed only in "%s" types';

    /**
     * @param array<string, string[]> $allowedExclusiveDependencyInTypes
     */
    public function __construct(
        private ArrayStringAndFnMatcher $arrayStringAndFnMatcher,
        private array $allowedExclusiveDependencyInTypes
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return InClassMethodNode::class;
    }

    /**
     * @param InClassMethodNode $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classMethod = $node->getOriginalNode();
        if ($classMethod->name->toString() !== MethodName::CONSTRUCTOR) {
            return [];
        }

        $methodReflection = $node->getMethodReflection();
        $declaringClassReflection = $methodReflection->getDeclaringClass();

        $className = $declaringClassReflection->getName();
        $paramTypes = $this->resolveParamTypes($classMethod);

        foreach ($paramTypes as $paramType) {
            foreach ($this->allowedExclusiveDependencyInTypes as $dependencyType => $allowedTypes) {
                if ($this->isExclusiveMatchingDependency($paramType, $dependencyType, $className, $allowedTypes)) {
                    continue;
                }

                $errorMessage = sprintf(self::ERROR_MESSAGE, $dependencyType, implode('", "', $allowedTypes));
                return [$errorMessage];
            }
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Dependency of specific type can be used only in specific class types', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
final class CheckboxController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class CheckboxRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }
}
CODE_SAMPLE
                ,
                [
                    'allowedExclusiveDependencyInTypes' => [
                        'Doctrine\ORM\EntityManager' => ['*Repository'],
                        'Doctrine\ORM\EntityManagerInterface' => ['*Repository'],
                    ],
                ]
            ),
        ]);
    }

    /**
     * @param string[] $allowedTypes
     */
    private function isExclusiveMatchingDependency(
        string $paramType,
        string $dependencyType,
        string $className,
        array $allowedTypes
    ): bool {
        if (! $this->arrayStringAndFnMatcher->isMatch($paramType, [$dependencyType])) {
            return true;
        }

        // instancef of but with static reflection
        $classObjectType = new ObjectType($className);
        foreach ($allowedTypes as $allowedType) {
            if ($classObjectType->isInstanceOf($allowedType)->yes()) {
                return true;
            }
        }

        return $this->arrayStringAndFnMatcher->isMatch($className, $allowedTypes);
    }

    /**
     * @return string[]
     */
    private function resolveParamTypes(ClassMethod $classMethod): array
    {
        $paramTypes = [];

        foreach ($classMethod->params as $param) {
            /** @var Param $param */
            if ($param->type === null) {
                continue;
            }

            if (! $param->type instanceof Identifier && ! $param->type instanceof Name) {
                continue;
            }

            $paramTypes[] = $param->type->toString();
        }

        return $paramTypes;
    }
}
