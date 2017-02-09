<?php declare(strict_types=1);

namespace Symplify\MultiCodingStandard\Console\Output;

use Symfony\Component\Console\Output\Output;
use Symplify\SniffRunner\Report\ErrorDataCollector;

final class InfoMessagePrinter
{
    /**
     * @var ErrorDataCollector
     */
    private $errorDataCollector;

    /**
     * @var Output
     */
    private $output;

    public function __construct(
        ErrorDataCollector $errorDataCollector,
        Output $output
    ) {
        $this->errorDataCollector = $errorDataCollector;
        $this->output = $output;
    }

    public function hasSomeErrorMessages() : bool
    {
        if ($this->errorDataCollector->getErrorCount()) {
            return true;
        }

        return false;
    }

    public function printFoundErrorsStatus(bool $isFixer)
    {
        // @todo: combine to onw printer!!!!

        // code sniffer
//        $this->phpCodeSnifferInfoMessagePrinter->printFoundErrorsStatus($isFixer);

        // php-cs-fixer
//        $diffs = $this->diffDataCollector->getDiffs();
//        $this->printDiffs($diffs);
    }
}
