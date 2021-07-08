<?php

declare(strict_types=1);

namespace Symplify\SimplePhpDocParser;

use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode;
use Symplify\SimplePhpDocParser\Contract\PhpDocNodeVisitorInterface;
use Symplify\SimplePhpDocParser\Exception\InvalidTraverseException;
use Symplify\SimplePhpDocParser\PhpDocNodeVisitor\CallablePhpDocNodeVisitor;
use Symplify\SimplePhpDocParser\ValueObject\PhpDocAttributeKey;

/**
 * Mimics
 * https://github.com/nikic/PHP-Parser/blob/4abdcde5f16269959a834e4e58ea0ba0938ab133/lib/PhpParser/NodeTraverser.php
 *
 * @see \Symplify\SimplePhpDocParser\Tests\SimplePhpDocNodeTraverser\PhpDocNodeTraverserTest
 */
final class PhpDocNodeTraverser
{
    /**
     * Return from enterNode() to remove node from the tree
     *
     * @var int
     */
    public const NODE_REMOVE = 1;

    /**
     * @var PhpDocNodeVisitorInterface[]
     */
    private array $phpDocNodeVisitors = [];

    public function addPhpDocNodeVisitor(PhpDocNodeVisitorInterface $phpDocNodeVisitor): void
    {
        $this->phpDocNodeVisitors[] = $phpDocNodeVisitor;
    }

    public function traverse(Node $node): void
    {
        foreach ($this->phpDocNodeVisitors as $phpDocNodeVisitor) {
            $phpDocNodeVisitor->beforeTraverse($node);
        }

        $node = $this->traverseNode($node);
        if (is_int($node)) {
            throw new InvalidTraverseException();
        }

        foreach ($this->phpDocNodeVisitors as $phpDocNodeVisitor) {
            $phpDocNodeVisitor->afterTraverse($node);
        }
    }

    public function traverseWithCallable(Node $node, string $docContent, callable $callable): Node
    {
        $callablePhpDocNodeVisitor = new CallablePhpDocNodeVisitor($callable, $docContent);
        $this->addPhpDocNodeVisitor($callablePhpDocNodeVisitor);

        $this->traverse($node);
        return $node;
    }

    /**
     * @template TNode of Node
     * @param TNode $node
     * @return TNode|int
     */
    private function traverseNode(Node $node): Node | int
    {
        $subNodeNames = array_keys(get_object_vars($node));

        foreach ($subNodeNames as $subNodeName) {
            $subNode = &$node->{$subNodeName};

            if (\is_array($subNode)) {
                $subNode = $this->traverseArray($subNode);
            } elseif ($subNode instanceof Node) {
                foreach ($this->phpDocNodeVisitors as $phpDocNodeVisitor) {
                    $return = $phpDocNodeVisitor->enterNode($subNode);
                    if ($return instanceof Node) {
                        $subNode = $return;
                    } elseif ($return === self::NODE_REMOVE) {
                        if ($subNode instanceof PhpDocTagValueNode) {
                            // we have to remove the node above
                            return self::NODE_REMOVE;
                        }
                        $subNode = null;
                        continue 2;
                    }
                }

                $subNode = $this->traverseNode($subNode);
                if (is_int($subNode)) {
                    throw new InvalidTraverseException();
                }

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
        foreach ($nodes as $key => &$node) {
            // can be string or something else
            if (! $node instanceof Node) {
                continue;
            }

            foreach ($this->phpDocNodeVisitors as $phpDocNodeVisitor) {
                $return = $phpDocNodeVisitor->enterNode($node);
                if ($return instanceof Node) {
                    $node = $return;
                } elseif ($return === self::NODE_REMOVE) {
                    // remove node
                    unset($nodes[$key]);
                    continue 2;
                }
            }

            $return = $this->traverseNode($node);
            // remove value node
            if ($return === self::NODE_REMOVE) {
                unset($nodes[$key]);
                continue;
            }

            if (is_int($return)) {
                throw new InvalidTraverseException();
            }

            $node = $return;

            foreach ($this->phpDocNodeVisitors as $phpDocNodeVisitor) {
                $phpDocNodeVisitor->leaveNode($node);
            }
        }

        return $nodes;
    }
}
