<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Property;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use Symplify\PackageBuilder\Matcher\ArrayStringAndFnMatcher;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckDependencyMatrixRule\CheckDependencyMatrixRuleTest
 */
final class CheckDependencyMatrixRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_FORBIDDEN_MESSAGE = '%s type as dependency is not allowed';

    /**
     * @var string
     */
    public const ERROR_ALLOW_ONLY_MESSAGE = 'Only %s type as dependency is allowed';

    /**
     * @var string
     * @see https://regex101.com/r/EPYQEH/1
     */
    private const DEPENDENCY_VAR_REGEX = '#@var\s+(.*)#';

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    /**
     * @var ArrayStringAndFnMatcher
     */
    private $arrayStringAndFnMatcher;

    /**
     * @var array<string, array<string, string>>
     */
    private $forbiddenMatrix = [];

    /**
     * @var array<string, string>
     */
    private $allowOnlyMatrix = [];

    /**
     * @param array<string, array<string, string>> $forbiddenMatrix
     * @param array<string, string> $allowOnlyMatrix
     */
    public function __construct(
        NodeFinder $nodeFinder,
        ArrayStringAndFnMatcher $arrayStringAndFnMatcher,
        array $forbiddenMatrix = [],
        array $allowOnlyMatrix = []
    ) {
        $this->nodeFinder = $nodeFinder;
        $this->arrayStringAndFnMatcher = $arrayStringAndFnMatcher;
        $this->forbiddenMatrix = $forbiddenMatrix;
        $this->allowOnlyMatrix = $allowOnlyMatrix;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param Class_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! property_exists($node, 'namespacedName')) {
            return [];
        }

        /** @var Identifier|null $name */
        $name = $node->namespacedName;
        if ($name === null) {
            return [];
        }

        $className = (string) $name;
        $extends = $node->extends;

        if ($extends !== null && ! $extends instanceof FullyQualified) {
            return [];
        }

        $forbiddenDependencies = $this->getForbiddenDependencies($className, $extends);
        $allowOnlyDependency = $this->getAllowOnly($className, $extends);

        /** @var Property[] $properties */
        $properties = $this->nodeFinder->findInstanceOf($node, Property::class);
        return $this->checkLayer($properties, $forbiddenDependencies, $allowOnlyDependency);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Type dependency disallowed or allowed only', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class CheckboxController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
}

class CheckboxRepository
{
    /**
     * @var Command
     */
    private $command;
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class CheckboxController extends AbstractController
{
    /**
     * @var CheckboxRepositoryInterface
     */
    private $repository;
}

class CheckboxRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
}
CODE_SAMPLE
            ,
                [
                    'forbiddenMatrix' => [
                        '*Controller*' => ['EntityManager*'],
                    ],
                    'allowOnlyMatrix' => [
                        '*Repository*' => 'EntityManager*',
                    ],
                ]
            ),
        ]);
    }

    /**
     * @param Property[] $properties
     * @return string[]
     */
    private function checkLayer(
        array $properties,
        array $forbiddenDependencies,
        ?string $allowOnlyDependency = null
    ): array {
        if ($properties === []) {
            return [];
        }

        foreach ($properties as $property) {
            $dependency = $this->getDependency($property);

            if ($dependency === null) {
                continue;
            }

            if ($this->isForbidden($forbiddenDependencies, $allowOnlyDependency, $dependency)) {
                return [sprintf(self::ERROR_FORBIDDEN_MESSAGE, $dependency)];
            }

            if ($this->isNotAllowOnly($forbiddenDependencies, $allowOnlyDependency, $dependency)) {
                return [sprintf(self::ERROR_ALLOW_ONLY_MESSAGE, $allowOnlyDependency)];
            }
        }

        return [];
    }

    private function isForbidden(
        array $forbiddenDependencies,
        ?string $allowOnlyDependency = null,
        string $dependency
    ): bool {
        return $allowOnlyDependency === null
            && $forbiddenDependencies !== []
            && $this->arrayStringAndFnMatcher->isMatch($dependency, $forbiddenDependencies);
    }

    private function isNotAllowOnly(
        array $forbiddenDependencies,
        ?string $allowOnlyDependency = null,
        string $dependency
    ): bool {
        return $forbiddenDependencies === []
            && $allowOnlyDependency !== null
            && ! $this->arrayStringAndFnMatcher->isMatch($dependency, [$allowOnlyDependency]);
    }

    private function getDependency(Property $property): ?string
    {
        if ($property->type instanceof FullyQualified) {
            return $property->type->getLast();
        }

        $docComment = $property->getDocComment();
        if ($docComment === null) {
            return $property->props[0]->name->toString();
        }

        $text = $docComment->getText();
        $match = Strings::match($text, self::DEPENDENCY_VAR_REGEX);
        if ($match) {
            return $this->resolveShortName($match[1]);
        }

        return null;
    }

    /**
     * @return string[]
     */
    private function getForbiddenDependencies(string $className, ?FullyQualified $extends): array
    {
        $locate = $className;
        if ($extends !== null) {
            $locate = $extends->toString();
        }

        foreach ($this->forbiddenMatrix as $type => $forbiddenDependencies) {
            if ($this->arrayStringAndFnMatcher->isMatch($locate, [$type])) {
                return $forbiddenDependencies;
            }
        }

        return [];
    }

    private function getAllowOnly(string $className, ?FullyQualified $extends): ?string
    {
        $locate = $className;
        if ($extends !== null) {
            $locate = $extends->toString();
        }

        foreach ($this->allowOnlyMatrix as $type => $allowOnlyDependency) {
            if ($this->arrayStringAndFnMatcher->isMatch($locate, [$type])) {
                return $allowOnlyDependency;
            }
        }

        return null;
    }
}
