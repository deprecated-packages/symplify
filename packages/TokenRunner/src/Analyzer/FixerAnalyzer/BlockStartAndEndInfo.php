<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Analyzer\FixerAnalyzer;

final class BlockStartAndEndInfo
{
    /**
     * @var int
     */
    private $start;

    /**
     * @var int
     */
    private $end;

    public function __construct(int $start, int $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function getStart(): int
    {
        return $this->start;
    }

    public function geEnd(): int
    {
        return $this->end;
    }
}
