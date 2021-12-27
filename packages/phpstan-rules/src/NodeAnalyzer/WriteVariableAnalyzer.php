<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\AssignOp\Coalesce;
use PhpParser\Node\Expr\PostDec;
use PhpParser\Node\Expr\PostInc;
use PhpParser\Node\Expr\PreDec;
use PhpParser\Node\Expr\PreInc;
use PhpParser\Node\Expr\Variable;
use Symplify\Astral\ValueObject\AttributeKey;

final class WriteVariableAnalyzer
{
    /**
     * @var array<class-string<Expr>>
     */
    private const WRITE_PARENT_TYPE = [PreInc::class, PostInc::class, PreDec::class, PostDec::class];

    public function isVariableWritten(Variable $variable): bool
    {
        $parent = $variable->getAttribute(AttributeKey::PARENT);

        while ($parent instanceof ArrayDimFetch) {
            $parent = $parent->getAttribute(AttributeKey::PARENT);
        }

        if ($parent instanceof Coalesce) {
            return true;
        }

        if ($parent instanceof Assign && $parent->var === $variable) {
            return true;
        }

        // is used in write-mode → keep it
        foreach (self::WRITE_PARENT_TYPE as $writeParentType) {
            if (is_a($parent, $writeParentType, true)) {
                return true;
            }
        }

        return false;
    }
}
