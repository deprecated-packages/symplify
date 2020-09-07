<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ObjectCalisthenics\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeTraverser;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\ObjectCalisthenics\Marker\IndentationMarker;
use Symplify\CodingStandard\ObjectCalisthenics\NodeVisitor\IndentationNodeVisitor;

/**
 * @see https://williamdurand.fr/2013/06/03/object-calisthenics/#1-only-one-level-of-indentation-per-method
 *
 * @see \Symplify\CodingStandard\ObjectCalisthenics\Tests\Rules\SingleIndentationInMethodRule\SingleIndentationInMethodRuleTest
 */
final class SingleIndentationInMethodRule implements Rule
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
     * @var NodeTraverser
     */
    private $indentationNodeTraverser;

    /**
     * @var int
     */
    private $maxNestingLevel;

    public function __construct(int $maxNestingLevel = 1)
    {
        $this->indentationMarker = new IndentationMarker();
        $indentationNodeVisitor = new IndentationNodeVisitor($this->indentationMarker);

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($indentationNodeVisitor);

        $this->indentationNodeTraverser = $nodeTraverser;

        $this->maxNestingLevel = $maxNestingLevel;
    }

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $this->indentationMarker->reset();
        $this->indentationNodeTraverser->traverse([$node]);

        $limitIndentation = $this->maxNestingLevel + self::DEFAULT_DEPTH;

        if ($this->indentationMarker->getIndentation() < $limitIndentation) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $this->maxNestingLevel);
        return [$errorMessage];
    }
}
