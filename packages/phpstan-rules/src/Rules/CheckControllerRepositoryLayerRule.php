<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
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
    public const ERROR_MESSAGE = '%s used in %s, must be used %s in "%s" type';

    /**
     * @var string
     * @see https://regex101.com/r/62rngZ/1
     */
    private const NOT_ENTITYMANAGER_REGEX = '#(EntityManager)[^\1]*#';

    /**
     * @var string
     * @see https://regex101.com/r/ZEkPwa/2
     */
    private const CONTROLLER_OR_REPOSITORY_REGEX = '#(Controller$|Repository$)#';

    /**
     * @var string
     */
    private const LAYER_NOT_MATCH = [
        // Controller allow any other, eg: Form, except EntityManager
        'Controller' => 'EntityManager',

        // Repository allow only EntityManager
        'Repository' => self::NOT_ENTITYMANAGER_REGEX,
    ];

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
        $shortClassName = $node->name;
        if ($shortClassName === null) {
            return [];
        }

        $shortClassName = (string) $shortClassName;
        $extends = $node->extends;

        if (
            ($extends === null && ! Strings::match($shortClassName, self::CONTROLLER_OR_REPOSITORY_REGEX))
                ||
            ($extends !== null && ! Strings::match($extends->getLast(), self::CONTROLLER_OR_REPOSITORY_REGEX))
        ) {
            return [];
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
}
