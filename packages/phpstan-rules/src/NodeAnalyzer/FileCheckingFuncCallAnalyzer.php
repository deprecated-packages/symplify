<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\If_;

final class FileCheckingFuncCallAnalyzer
{
    public function isFileExistCheck(Node $node): bool
    {
        if (! $node instanceof If_) {
            return false;
        }

        if (! $node->cond instanceof FuncCall) {
            return false;
        }

        $funcCallCond = $node->cond;
        if (! $funcCallCond->name instanceof Name) {
            return false;
        }

        $funcCallName = $funcCallCond->name->toString();

        return in_array($funcCallName, ['is_file', 'file_exists', 'is_dir'], true);
    }
}
