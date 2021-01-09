<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PackageBuilder\Matcher\ArrayStringAndFnMatcher;
use Symplify\PHPStanRules\ValueObject\MethodName;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ExclusiveDependencyRule\ExclusiveDependencyRuleTest
 */
final class ExclusiveDependencyRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = '"%s" dependency is allowed only in "%s" types';

    /**
     * @var array<string, string[]>
     */
    private $allowedExclusiveDependencyInTypes = [];

    /**
     * @var ArrayStringAndFnMatcher
     */
    private $arrayStringAndFnMatcher;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @param array<string, string[]> $allowedExclusiveDependencyInTypes
     */
    public function __construct(
        SimpleNameResolver $simpleNameResolver,
        ArrayStringAndFnMatcher $arrayStringAndFnMatcher,
        array $allowedExclusiveDependencyInTypes = []
    ) {
        $this->arrayStringAndFnMatcher = $arrayStringAndFnMatcher;
        $this->allowedExclusiveDependencyInTypes = $allowedExclusiveDependencyInTypes;
        $this->simpleNameResolver = $simpleNameResolver;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $this->simpleNameResolver->isName($node->name, MethodName::CONSTRUCTOR)) {
            return [];
        }

        $className = $this->simpleNameResolver->getClassNameFromScope($scope);
        if ($className === null) {
            return [];
        }

        $paramTypes = $this->resolveParamTypes($node);

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
class CheckboxController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class CheckboxRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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

            $paramType = $this->simpleNameResolver->getName($param->type);
            if ($paramType === null) {
                continue;
            }

            $paramTypes[] = $paramType;
        }

        return $paramTypes;
    }
}
