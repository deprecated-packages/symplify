<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Analyzer\FixerAnalyzer;

final class BlockStartAndEndInfo
{

    /**
     * @var int
     */
    private $blockStart;
    /**
     * @var int
     */
    private $blockEnd;

    public function __construct(int $blockStart, int $blockEnd)
    {
        $this->blockStart = $blockStart;
        $this->blockEnd = $blockEnd;
    }

    public function getBlockStart(): int
    {
        return $this->blockStart;
    }

    public function getBlockEnd(): int
    {
        return $this->blockEnd;
    }
}
