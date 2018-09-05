<?php declare(strict_types=1);

namespace Symplify\PHPStan\ErrorFormatter;

use PHPStan\Command\AnalysisResult;
use PHPStan\Command\ErrorFormatter\ErrorFormatter;
use Symfony\Component\Console\Style\OutputStyle;
use Symfony\Component\Console\Terminal;
use Symplify\PHPStan\Error\ErrorGrouper;

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

    /**
     * @var ErrorGrouper
     */
    private $errorGrouper;

    public function __construct(Terminal $terminal, ErrorGrouper $errorGrouper)
    {
        $this->terminal = $terminal;
        $this->errorGrouper = $errorGrouper;
    }

    public function formatErrors(AnalysisResult $analysisResult, OutputStyle $outputStyle): int
    {
        if ($analysisResult->getTotalErrorsCount() === 0) {
            $outputStyle->success('No errors');
            // success
            return 0;
        }

        $messagesToFrequency = $this->errorGrouper->groupErrorsToMessagesToFrequency(
            $analysisResult->getFileSpecificErrors()
        );

        // pick top X items
        $topMessagesToFrequency = $this->cutTopXItems($messagesToFrequency, self::LIMIT);
        $tableData = $this->transformToTableData($topMessagesToFrequency);

        $outputStyle->table(['Message', 'Count'], $tableData);
        $outputStyle->error(sprintf('Found top %d most frequent errors', count($topMessagesToFrequency)));

        // fail
        return 1;
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

    /**
     * @param mixed[] $items
     * @return mixed[]
     */
    private function cutTopXItems(array $items, int $limit): array
    {
        return array_slice($items, 0, min(count($items), $limit), true);
    }
}
