<?php declare(strict_types=1);

namespace Symplify\PHPStan\ErrorFormatter;

use PHPStan\Command\AnalysisResult;
use PHPStan\Command\ErrorFormatter\ErrorFormatter;
use Symfony\Component\Console\Style\OutputStyle;
use Symfony\Component\Console\Terminal;

final class StatsErrorFormatter implements ErrorFormatter
{
    /**
     * Number of top errors to display
     * @var int
     */
    private const LIMIT = 10;

    /**
     * @var Terminal
     */
    private $terminal;

    public function __construct(Terminal $terminal)
    {
        $this->terminal = $terminal;
    }

    public function formatErrors(AnalysisResult $analysisResult, OutputStyle $outputStyle): int
    {
        if ($analysisResult->getTotalErrorsCount() === 0) {
            // success
            return 0;
        }

        $errorMessages = [];
        foreach ($analysisResult->getFileSpecificErrors() as $error) {
            $errorMessages[] = $error->getMessage();
        }

        $messagesToFrequency = $this->groupAndSortByMostFrequent($errorMessages);
        if (! $messagesToFrequency) {
            // success
            return 0;
        }

        $tableData = $this->transformToTableData($messagesToFrequency);

        $outputStyle->table(['Message', 'Count'], $tableData);
        $outputStyle->error(sprintf('Found top %d most frequent errors', count($messagesToFrequency)));

        // fail
        return 1;
    }

    /**
     * @param string[] $errorMessages
     * @return int[]
     */
    private function groupAndSortByMostFrequent(array $errorMessages): array
    {
        $errorMessagesCounts = array_count_values($errorMessages);

        // sort with most frequent items first
        arsort($errorMessagesCounts);

        // pick top X items
        return array_slice($errorMessagesCounts, 0, min(count($errorMessagesCounts), self::LIMIT), true);
    }

    /**
     * @param int[] $messagesToFrequency
     * @return int[][]|string[][]
     */
    private function transformToTableData(array $messagesToFrequency): array
    {
        $errorTable = [];
        foreach ($messagesToFrequency as $message => $frequency) {
            $message = $this->wrapMessageSoItFitsTheColumnWidth($message);
            $errorTable[] = [$message, (string) $frequency . 'x'];
        }

        return $errorTable;
    }

    private function wrapMessageSoItFitsTheColumnWidth(string $message): string
    {
        return wordwrap($message, $this->terminal->getWidth() - 12, PHP_EOL);
    }
}
