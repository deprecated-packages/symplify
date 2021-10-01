<?php

declare(strict_types=1);

namespace Symplify\TwigPHPStanCompiler\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

final class RemoveUselessClassMethodsNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private const METHOD_NAMES_TO_REMOVE = ['getTemplateName', 'isTraitable', 'getDebugInfo', 'getSourceContext'];

    public function leaveNode(Node $node): null|int
    {
        if (! $node instanceof ClassMethod) {
            return null;
        }

        $classMethodName = $node->name->toString();
        if (! in_array($classMethodName, self::METHOD_NAMES_TO_REMOVE, true)) {
            return null;
        }

        return NodeTraverser::REMOVE_NODE;
    }
}
