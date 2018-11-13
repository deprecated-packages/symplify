<?php declare(strict_types=1);

namespace Symplify\PHPStanExtensions\ErrorFormatter;

use Nette\Utils\Strings;
use PHPStan\Command\AnalysisResult;
use PHPStan\Command\ErrorFormatter\ErrorFormatter;
use Symfony\Component\Console\Style\OutputStyle;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\PHPStanExtensions\Error\ErrorGrouper;
use function Safe\getcwd;
use function Safe\sprintf;

final class StatsErrorFormatter implements ErrorFormatter
{
    /**
     * Number of top errors to display
     * @var int
     */
    private const LIMIT = 5;

    /**
     * @var ErrorGrouper
     */
    private $errorGrouper;

    public function __construct(ErrorGrouper $errorGrouper)
    {
        $this->errorGrouper = $errorGrouper;
    }

    public function formatErrors(AnalysisResult $analysisResult, OutputStyle $outputStyle): int
    {
        if ($analysisResult->getTotalErrorsCount() === 0) {
            $outputStyle->success('No errors');
            return ShellCode::SUCCESS;
        }

        $messagesToFrequency = $this->errorGrouper->groupErrorsToMessagesToFrequency(
            $analysisResult->getFileSpecificErrors()
        );

        $topMessagesToFrequency = $this->cutTopXItems($messagesToFrequency, self::LIMIT);
        $outputStyle->title(sprintf('These are %d most frequent errors', count($topMessagesToFrequency)));

        foreach ($topMessagesToFrequency as $info) {
            $info['files'] = $this->relativizePaths($info['files']);
            $filesOutput = '- ' . implode(PHP_EOL . '- ', $info['files']);
            $outputStyle->table([sprintf('%d x', count($info['files'])), $info['message']], [['', $filesOutput]]);
        }

        $outputStyle->newLine();
        $outputStyle->error(sprintf('Found %d errors', $analysisResult->getTotalErrorsCount()));

        return ShellCode::ERROR;
    }

    /**
     * @param mixed[] $items
     * @return mixed[]
     */
    private function cutTopXItems(array $items, int $limit): array
    {
        return array_slice($items, 0, min(count($items), $limit), true);
    }

    /**
     * @param string[] $files
     * @return string[]
     */
    private function relativizePaths(array $files): array
    {
        foreach ($files as $i => $file) {
            $files[$i] = Strings::replace($file, '#' . preg_quote(getcwd() . '/') . '#');
        }

        return $files;
    }
}
