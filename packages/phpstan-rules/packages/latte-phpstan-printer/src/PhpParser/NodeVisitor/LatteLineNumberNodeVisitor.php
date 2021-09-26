<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\LattePHPStanPrinter\PhpParser\NodeVisitor;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\NodeVisitorAbstract;
use Symplify\PHPStanRules\LattePHPStanPrinter\Latte\LineCommentMatcher;

final class LatteLineNumberNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var array<int, int>
     */
    private array $phpLinesToLatteLines = [];

    private ?int $lastLatteLine = null;

    public function __construct(
        private LineCommentMatcher $lineCommentMatcher
    ) {
    }

    /**
     * @param Stmt[] $nodes
     * @return Stmt[]
     */
    public function beforeTraverse(array $nodes): array
    {
        // reset to avoid leak to another class
        $this->phpLinesToLatteLines = [];

        return $nodes;
    }

    public function enterNode(Node $node)
    {
        $docComment = $node->getDocComment();

        if (! $docComment instanceof Doc) {
            $this->phpLinesToLatteLines[$node->getStartLine()] = $this->lastLatteLine;
            return null;
        }

        $docCommentText = $docComment->getText();
        $latteLine = $this->lineCommentMatcher->matchLine($docCommentText);
        if ($latteLine === null) {
            $this->phpLinesToLatteLines[$node->getStartLine()] = $this->lastLatteLine;
            return null;
        }

        $this->phpLinesToLatteLines[$node->getStartLine()] = $latteLine;
        $this->lastLatteLine = $latteLine;

        return null;
    }

    /**
     * @return array<int, int>
     */
    public function getPhpLinesToLatteLines(): array
    {
        return $this->phpLinesToLatteLines;
    }
}
