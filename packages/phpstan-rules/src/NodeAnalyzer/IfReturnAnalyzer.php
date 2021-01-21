<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Stmt\If_;
use Symplify\PHPStanRules\ValueObject\ScalarAndNonScalarCounter;

final class IfReturnAnalyzer
{
    /**
     * @var ScalarAnalyzer
     */
    private $scalarAnalyzer;

    public function __construct(ScalarAnalyzer $scalarAnalyzer)
    {
        $this->scalarAnalyzer = $scalarAnalyzer;
    }

    /**
     * @param If_[] $ifs
     */
    public function resolve(array $ifs): ScalarAndNonScalarCounter
    {
        $scalarReturnCount = 0;
        $nonScalarReturnCount = 0;

        foreach ($ifs as $if) {
            if ($if->stmts === [] || count($if->stmts) > 1) {
                ++$nonScalarReturnCount;
                continue;
            }

            $onlyStmt = $if->stmts[0];
            if ($this->scalarAnalyzer->isScalarReturn($onlyStmt)) {
                ++$scalarReturnCount;
            } else {
                ++$nonScalarReturnCount;
            }
        }

        return new ScalarAndNonScalarCounter($scalarReturnCount, $nonScalarReturnCount);
    }
}
