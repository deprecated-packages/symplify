<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Property;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckControllerRepositoryLayerRule\CheckControllerRepositoryLayerRuleTest
 */
final class CheckControllerRepositoryLayerRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = '%s type not allowed to use %s type as dependency, use %s instead';

    /**
     * @var string
     * @see https://regex101.com/r/x1GflV/1
     */
    private const ENTITYMANAGER_REGEX = '#EntityManager#i';

    /**
     * @var string
     * @see https://regex101.com/r/62rngZ/2
     */
    private const NOT_ENTITYMANAGER_REGEX = '#(EntityManager)[^\1]*#i';

    /**
     * @var string
     * @see https://regex101.com/r/Azledf/2
     */
    private const CONTROLLER_REGEX = '#(Controller$)|\b(Controller)\b#';

    /**
     * @var string
     * @see https://regex101.com/r/AQG06A/2
     */
    private const REPOSITORY_REGEX = '#(Repository$)|\b(Repository)\b#';

    /**
     * @var string
     */
    private const LAYER_NOT_MATCH = [
        // Controller allow any other, eg: Form, except EntityManager
        'Controller' => self::ENTITYMANAGER_REGEX,

        // Repository allow only EntityManager
        'Repository' => self::NOT_ENTITYMANAGER_REGEX,
    ];

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    public function __construct(NodeFinder $nodeFinder)
    {
        $this->nodeFinder = $nodeFinder;
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

        $name = $node->namespacedName;
        if ($name === null) {
            return [];
        }

        $className = (string) $name;
        $extends = $node->extends;

        if ($extends !== null && ! $extends instanceof FullyQualified) {
            return [];
        }

        $isController = $this->isController($className, $extends);
        $isRepository = $this->isRepository($className, $extends);

        if (! $isController && ! $isRepository) {
            return [];
        }

        $properties = $this->nodeFinder->findInstanceOf($node, Property::class);
        if ($properties === []) {
            return [];
        }

        foreach ($properties as $property) {
            $propertyName = $property->props[0]->name->toString();

            if ($isController && Strings::match($propertyName, self::LAYER_NOT_MATCH['Controller'])) {
                return [sprintf(self::ERROR_MESSAGE, 'Controller', 'EntityManager', 'Repository')];
            }

            if ($isRepository && ! Strings::match($propertyName, self::LAYER_NOT_MATCH['Repository'])) {
                return [sprintf(self::ERROR_MESSAGE, 'Repository', $propertyName, 'EntityManager')];
            }
        }

        return [];
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
        ]);
    }

    private function isController(string $className, ?FullyQualified $extends): bool
    {
        if ($extends === null) {
            return (bool) Strings::match($className, self::CONTROLLER_REGEX);
        }

        return (bool) Strings::match($extends->toString(), self::CONTROLLER_REGEX);
    }

    private function isRepository(string $className, ?FullyQualified $extends): bool
    {
        if ($extends === null) {
            return (bool) Strings::match($className, self::REPOSITORY_REGEX);
        }

        return (bool) Strings::match($extends->toString(), self::REPOSITORY_REGEX);
    }
}
