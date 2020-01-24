<?php

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer\Wrong;

class SomeClass
{
    public function someMethod()
    {
        if ($split == null || $errorRate < $split['trainingErrorRate']) {
            $split = ['value' => $threshold, 'operator' => $operator,
                'prob' => $prob, 'column' => $col,
                'trainingErrorRate' => $errorRate, ];
        }
    }

    public function someMethod2()
    {
        $this->labels = [1 => $this->labels[0], -1 => $this->labels[1]];
    }
}

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer\Wrong;

class SomeClass
{
    public function someMethod()
    {
        if ($split == null || $errorRate < $split['trainingErrorRate']) {
            $split = [
                'value' => $threshold,
                'operator' => $operator,
                'prob' => $prob,
                'column' => $col,
                'trainingErrorRate' => $errorRate,
            ];
        }
    }

    public function someMethod2()
    {
        $this->labels = [
            1 => $this->labels[0],
            -1 => $this->labels[1]
        ];
    }
}

?>
