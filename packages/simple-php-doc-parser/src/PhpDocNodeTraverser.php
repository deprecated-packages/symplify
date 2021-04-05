<?php

declare(strict_types=1);

namespace Symplify\SimplePhpDocParser;

use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use Symplify\SimplePhpDocParser\Contract\PhpDocNodeVisitorInterface;
use Symplify\SimplePhpDocParser\PhpDocNodeVisitor\CallablePhpDocNodeVisitor;

/**
 * Mimics
 * https://github.com/nikic/PHP-Parser/blob/4abdcde5f16269959a834e4e58ea0ba0938ab133/lib/PhpParser/NodeTraverser.php
 *
 * @see \Symplify\SimplePhpDocParser\Tests\SimplePhpDocNodeTraverser\PhpDocNodeTraverserTest
 */
final class PhpDocNodeTraverser
{
    /**
     * @var PhpDocNodeVisitorInterface[]
     */
    private $phpDocNodeVisitors = [];

    public function addPhpDocNodeVisitor(PhpDocNodeVisitorInterface $phpDocNodeVisitor): void
    {
        $this->phpDocNodeVisitors[] = $phpDocNodeVisitor;
    }

    public function traverse(PhpDocNode $phpDocNode): void
    {
        foreach ($this->phpDocNodeVisitors as $phpDocNodeVisitor) {
            $phpDocNodeVisitor->beforeTraverse($phpDocNode);
        }

        $phpDocNode = $this->traverseNode($phpDocNode);

        foreach ($this->phpDocNodeVisitors as $phpDocNodeVisitor) {
            $phpDocNodeVisitor->afterTraverse($phpDocNode);
        }
    }

    public function traverseWithCallable(PhpDocNode $phpDocNode, string $docContent, callable $callable): PhpDocNode
    {
        $callableNodeVisitor = new CallablePhpDocNodeVisitor($callable, $docContent);
        $this->addPhpDocNodeVisitor($callableNodeVisitor);

        $this->traverse($phpDocNode);
        return $phpDocNode;
    }

    /**
     * @template TNode as Node
     * @param TNode $node
     * @return TNode
     */
    private function traverseNode(Node $node): Node
    {
        $subNodeNames = array_keys(get_object_vars($node));

        foreach ($subNodeNames as $subNodeName) {
            $subNode =& $node->$subNodeName;

            if (\is_array($subNode)) {
                $subNode = $this->traverseArray($subNode);
            } elseif ($subNode instanceof Node) {
                foreach ($this->phpDocNodeVisitors as $phpDocNodeVisitor) {
                    $return = $phpDocNodeVisitor->enterNode($subNode);
                    if ($return instanceof Node) {
                        $subNode = $return;
                    }
                }

                $subNode = $this->traverseNode($subNode);

                foreach ($this->phpDocNodeVisitors as $phpDocNodeVisitor) {
                    $phpDocNodeVisitor->leaveNode($subNode);
                }
            }
        }


        return $node;
    }

    /**
     * @param array<Node|mixed> $nodes
     * @return array<Node|mixed>
     */
    private function traverseArray(array $nodes): array
    {
        foreach ($nodes as &$node) {
            // can be string or something else
            if (! $node instanceof Node) {
                continue;
            }

            foreach ($this->phpDocNodeVisitors as $phpDocNodeVisitor) {
                $return = $phpDocNodeVisitor->enterNode($node);
                if ($return instanceof Node) {
                    $node = $return;
                }
            }

            $node = $this->traverseNode($node);

            foreach ($this->phpDocNodeVisitors as $phpDocNodeVisitor) {
                $phpDocNodeVisitor->leaveNode($node);
            }
        }

        return $nodes;
    }
}
