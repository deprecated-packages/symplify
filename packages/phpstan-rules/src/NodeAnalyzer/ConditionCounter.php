<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Expr;
use PhpParser\Node\Stmt\If_;
use Symplify\PHPStanRules\ValueObject\ScalarAndNonScalarCounter;

final class ConditionCounter
{
    public function __construct(
        private ScalarAnalyzer $scalarAnalyzer
    ) {
    }

    /**
     * @param If_[] $ifs
     */
    public function resolveScalarConditionTypes(array $ifs): ScalarAndNonScalarCounter
    {
        $scalarConditionTypes = [];
        $nonScalarConditionTypes = [];

        $conds = $this->resolveIfConditions($ifs);

        foreach ($conds as $cond) {
            if ($this->scalarAnalyzer->isScalar($cond)) {
                $scalarConditionTypes[] = get_class($cond);
            } else {
                $nonScalarConditionTypes[] = get_class($cond);
            }
        }

        $scalarConditionCount = count($scalarConditionTypes);
        $nonScalarConditionCount = count($nonScalarConditionTypes);

        return new ScalarAndNonScalarCounter($scalarConditionCount, $nonScalarConditionCount);
    }

    /**
     * @param If_[] $ifs
     * @return Expr[]
     */
    private function resolveIfConditions(array $ifs): array
    {
        $conds = [];
        foreach ($ifs as $if) {
            $conds[] = $if->cond;
        }

        foreach ($ifs as $if) {
            foreach ($if->elseifs as $elseif) {
                $conds[] = $elseif->cond;
            }
        }

        return $conds;
    }
}
