<?php

declare(strict_types=1);

namespace Symplify\TwigPHPStanCompiler\PhpParser\NodeVisitor;

use Nette\Utils\Strings;
use PhpParser\Comment;
use PhpParser\Node;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Echo_;
use PhpParser\Node\Stmt\Nop;
use PhpParser\NodeVisitorAbstract;
use Symplify\Astral\ValueObject\AttributeKey;
use Symplify\TwigPHPStanCompiler\TwigToPhpCompiler;
use Symplify\TwigPHPStanCompiler\ValueObject\VarTypeDoc;

final class ReplaceEchoWithVarDocTypeNodeVisitor extends NodeVisitorAbstract
{
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

        $varDoc = sprintf('/** @var %s $%s */', $varTypeDoc->getType(), $varTypeDoc->getVariableName());

        $comments = $node->getComments();
        $comments[] = new Comment($varDoc);

        $nop = new Nop();
        $nop->setAttribute(AttributeKey::COMMENTS, $comments);

        return $nop;
    }
}
