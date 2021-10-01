<?php

declare(strict_types=1);

namespace Symplify\TwigPHPStanCompiler\PhpParser\NodeVisitor;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Echo_;
use PhpParser\Node\Stmt\Nop;
use PhpParser\NodeVisitorAbstract;
use PHPStan\Type\ObjectType;
use Symplify\TemplatePHPStanCompiler\ValueObject\VariableAndType;
use Symplify\TwigPHPStanCompiler\TwigToPhpCompiler;
use Symplify\TwigPHPStanCompiler\ValueObject\VarTypeDoc;

final class ReplaceEchoWithVarDocTypeNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var VariableAndType[]
     */
    private array $collectedVariablesAndTypes = [];

    public function beforeTraverse(array $nodes)
    {
        $this->collectedVariablesAndTypes = [];

        return parent::beforeTraverse($nodes);
    }

    public function enterNode(Node $node)
    {
        if (! $node instanceof Echo_) {
            return null;
        }

        if (count($node->exprs) !== 1) {
            return null;
        }

        if (! $node->exprs[0] instanceof String_) {
            return null;
        }

        $string = $node->exprs[0];

        $match = Strings::match($string->value, TwigToPhpCompiler::TWIG_VAR_TYPE_DOCBLOCK_REGEX);
        if ($match === null) {
            return null;
        }

        $varTypeDoc = new VarTypeDoc($match['name'], $match['type']);

        // @todo assumption that type is an object - resolve in some strict/doc parser clean way
        $this->collectedVariablesAndTypes[] = new VariableAndType($varTypeDoc->getVariableName(), new ObjectType(
            $varTypeDoc->getType()
        ));

        // basically remove node
        return new Nop();
    }

    /**
     * @return VariableAndType[]
     */
    public function getCollectedVariablesAndTypes(): array
    {
        return $this->collectedVariablesAndTypes;
    }
}
