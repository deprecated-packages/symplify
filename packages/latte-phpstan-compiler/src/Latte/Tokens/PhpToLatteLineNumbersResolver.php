<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\LattePHPStanPrinter\Latte\Tokens;

use PhpParser\NodeTraverser;
use PhpParser\Parser;
use Symplify\PHPStanRules\LattePHPStanPrinter\PhpParser\NodeVisitor\LatteLineNumberNodeVisitor;

final class PhpToLatteLineNumbersResolver
{
    public function __construct(
        private LatteLineNumberNodeVisitor $latteLineNumberNodeVisitor,
        private Parser $parser
    ) {
    }

    /**
     * Here we have to use file content and parse it again, so we have updated start line positions
     *
     * @return array<int, int>
     */
    public function resolve(string $phpFileContent): array
    {
        $phpNodes = $this->parser->parse($phpFileContent);

        // nothign to resolve
        if ($phpNodes === null) {
            return [];
        }

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($this->latteLineNumberNodeVisitor);
        $nodeTraverser->traverse($phpNodes);

        return $this->latteLineNumberNodeVisitor->getPhpLinesToLatteLines();
    }
}
