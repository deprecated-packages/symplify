<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDependencyJugglingRule\Fixture;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

final class SkipNodeVisitor
{
    /**
     * @var NodeVisitorAbstract
     */
    private $nodeVisitorAbstract;

    public function __construct(NodeVisitorAbstract $nodeVisitorAbstract)
    {
        $this->nodeVisitorAbstract = $nodeVisitorAbstract;
    }

    public function another(NodeTraverser $nodeTraverser)
    {
        $nodeTraverser->addVisitor($this->nodeVisitorAbstract);
    }
}
