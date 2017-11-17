<?php declare(strict_types=1);

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
}
