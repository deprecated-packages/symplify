<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\PhpDocParser\Node;

use PhpParser\Node;
use PHPStan\PhpDocParser\Ast\Node as PhpDocAstNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use Symplify\BetterPhpDocParser\Ast\NodeTraverser;
use Symplify\BetterPhpDocParser\Attribute\Attribute;
use Symplify\BetterPhpDocParser\Contract\PhpDocParser\Ast\AttributeAwareNodeInterface;
use Symplify\BetterPhpDocParser\Exception\NotImplementedYetException;
use Symplify\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;

final class FqnNameDecorator
{
    /**
     * @var NamespaceAnalyzer
     */
    private $namespaceAnalyzer;

    /**
     * @var NodeTraverser
     */
    private $nodeTraverser;

    public function __construct(NamespaceAnalyzer $namespaceAnalyzer, NodeTraverser $nodeTraverser)
    {
        $this->namespaceAnalyzer = $namespaceAnalyzer;
        $this->nodeTraverser = $nodeTraverser;
    }

    public function decorate(PhpDocNode $phpDocNode, Node $node): void
    {
        $this->nodeTraverser->traverseWithCallable($phpDocNode, function (PhpDocAstNode $phpDocNode) use ($node) {
            $this->traverseNode($phpDocNode, $node);
        });
    }

    private function traverseNode(PhpDocAstNode $phpDocNode, Node $node): void
    {
        if ($this->shouldSkip($phpDocNode)) {
            return;
        }

        /** @var IdentifierTypeNode $phpDocNode */
        $fqnName = $this->namespaceAnalyzer->resolveTypeToFullyQualified($phpDocNode->name, $node, []);

        // no fqn name resolution
        if ($phpDocNode->name === $fqnName) {
            return;
        }

        if (! $phpDocNode instanceof AttributeAwareNodeInterface) {
            throw new NotImplementedYetException();
        }

        $phpDocNode->setAttribute(Attribute::FQN_NAME, $fqnName);
    }

    private function shouldSkip(PhpDocAstNode $phpDocNode): bool
    {
        if (! $phpDocNode instanceof IdentifierTypeNode) {
            return true;
        }

        return ! $this->isClassyType($phpDocNode->name);
    }

    private function isClassyType(string $name): bool
    {
        return ctype_upper($name[0]);
    }
}
