<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

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
     */
    private const LAYER_MATCH = [
        'Controller' => 'Repository',
        'Repository' => 'EntityManager',
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
CODE_SAMPLE
            ),
        ]);
    }
}
