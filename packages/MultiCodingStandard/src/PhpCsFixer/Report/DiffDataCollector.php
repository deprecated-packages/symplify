<?php declare(strict_types=1);

namespace Symplify\MultiCodingStandard\PhpCsFixer\Report;

final class DiffDataCollector
{
    /**
     * @var array
     */
    private $diffs;

    public function setDiffs(array $diffs)
    {
        $this->diffs = $diffs;
    }

    public function getDiffs(): array
    {
        return $this->diffs;
    }
}
