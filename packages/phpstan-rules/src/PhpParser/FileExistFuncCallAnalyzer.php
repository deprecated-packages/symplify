<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\PhpParser;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Stmt\If_;
use Symplify\Astral\ValueObject\AttributeKey;

final class FileExistFuncCallAnalyzer
{
    public function isBeingCheckedIfExists(Node $node): bool
    {
        $parent = $node->getAttribute(AttributeKey::PARENT);
        if (! $parent instanceof Arg) {
            return false;
        }

        $parentParent = $parent->getAttribute(AttributeKey::PARENT);
        if (! $parentParent instanceof Node) {
            return false;
        }

        return $this->isFileCheckingFuncCall($parentParent);
    }

    public function hasParentIfWithFileExistCheck(Concat $concat): bool
    {
        $parent = $concat->getAttribute(AttributeKey::PARENT);
        while ($parent !== null) {
            if ($parent instanceof If_ && $this->isFileCheckingFuncCall($parent->cond)) {
                return true;
            }

            $parent = $parent->getAttribute(AttributeKey::PARENT);
        }

        return false;
    }

    private function isFileCheckingFuncCall(Node $node): bool
    {
        if (! $node instanceof FuncCall) {
            return false;
        }

        if ($node->name instanceof Expr) {
            return false;
        }

        $funcCallName = (string) $node->name;
        return in_array($funcCallName, ['is_file', 'file_exists', 'is_dir'], true);
    }
}
