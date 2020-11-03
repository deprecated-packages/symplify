<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ObjectCalisthenics\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symplify\CodingStandard\ObjectCalisthenics\Marker\IndentationMarker;
use Symplify\CodingStandard\ObjectCalisthenics\NodeTraverserFactory\IndentationNodeTraverserFactory;
use Symplify\CodingStandard\Rules\AbstractSymplifyRule;

/**
 * @see https://williamdurand.fr/2013/06/03/object-calisthenics/#1-only-one-level-of-indentation-per-method
 *
 * @see \Symplify\CodingStandard\ObjectCalisthenics\Tests\Rules\SingleIndentationInMethodRule\SingleIndentationInMethodRuleTest
 */
final class SingleIndentationInMethodRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not indent more than %dx in class methods';

    /**
     * The depth from nested values inside method, so 2 nestings are from class and method and 1 from inner method
     * @var int
     */
    private const DEFAULT_DEPTH = 5;

    /**
     * @var IndentationMarker
     */
    private $indentationMarker;

    /**
     * @var int
     */
    private $maxNestingLevel;

    /**
     * @var IndentationNodeTraverserFactory
     */
    private $indentationNodeTraverserFactory;

    public function __construct(
        IndentationMarker $indentationMarker,
        IndentationNodeTraverserFactory $indentationNodeTraverserFactory,
        int $maxNestingLevel = 1
    ) {
        $this->indentationMarker = $indentationMarker;
        $this->maxNestingLevel = $maxNestingLevel;
        $this->indentationNodeTraverserFactory = $indentationNodeTraverserFactory;
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
        $this->indentationMarker->reset();

        $nodeTraverser = $this->indentationNodeTraverserFactory->create();
        $nodeTraverser->traverse([$node]);

        $limitIndentation = $this->maxNestingLevel + self::DEFAULT_DEPTH;

        if ($this->indentationMarker->getIndentation() < $limitIndentation) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $this->maxNestingLevel);
        return [$errorMessage];
    }
}
