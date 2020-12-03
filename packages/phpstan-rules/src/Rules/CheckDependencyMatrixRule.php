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
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
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
     * @see https://regex101.com/r/x1GflV/1
     */
    private const MATCH_DEPENDENCY_REGEX = '#%s#i';

    /**
     * @var string
     * @see https://regex101.com/r/62rngZ/2
     */
    private const NOT_MATCH_DEPENDENCY_REGEX = '#(%s)[^\1]*#i';

    /**
     * @var string
     * @see https://regex101.com/r/EPYQEH/1
     */
    private const DEPENDENCY_VAR_REGEX = '#@var\s+(.*)#';

    /**
     * @var string
     */
    private const LAYER_NOT_MATCH = [
        'forbidden' => self::MATCH_DEPENDENCY_REGEX,
        'allowOnly' => self::NOT_MATCH_DEPENDENCY_REGEX,
    ];

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

        $isForbidden = $this->isForbidden($className, $extends);
        $isAllowOnly = $this->isAllowOnly($className, $extends);

        if (! $isForbidden && ! $isAllowOnly) {
            return [];
        }

        /** @var Property[] $properties */
        $properties = $this->nodeFinder->findInstanceOf($node, Property::class);
        return $this->checkLayer($properties, $isForbidden, $isAllowOnly);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
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
            ),
            [
                'forbiddenMatrix' => [
                    '*Controller' => ['*EntityManager'],
                ],
                'allowOnlyMatrix' => [
                    '*Repository' => '*EntityManager',
                ],
            ],
        ]);
    }

    /**
     * @param Property[] $properties
     * @return string[]
     */
    private function checkLayer(array $properties, bool $isForbidden, bool $isAllowOnly): array
    {
        if ($properties === []) {
            return [];
        }

        foreach ($properties as $property) {
            $dependency = $this->getDependency($property);

            if ($dependency === null) {
                continue;
            }

            if ($isForbidden && Strings::match($dependency, sprintf(self::LAYER_NOT_MATCH['forbidden'], $dependency))) {
                return [sprintf(self::ERROR_FORBIDDEN_MESSAGE, $dependency)];
            }
        }

        return [];
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

    private function isForbidden(string $className, ?FullyQualified $extends): bool
    {
        if ($extends === null) {
            return $this->arrayStringAndFnMatcher->isMatch($className, array_keys($this->forbiddenMatrix));
        }

        return $this->arrayStringAndFnMatcher->isMatch($extends->toString(), array_keys($this->forbiddenMatrix));
    }

    private function isAllowOnly(string $className, ?FullyQualified $extends): bool
    {
        if ($extends === null) {
            return $this->arrayStringAndFnMatcher->isMatch($className, array_keys($this->allowOnlyMatrix));
        }

        return $this->arrayStringAndFnMatcher->isMatch($extends->toString(), array_keys($this->allowOnlyMatrix));
    }
}
