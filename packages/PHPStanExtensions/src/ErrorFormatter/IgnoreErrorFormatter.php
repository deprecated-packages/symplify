<?php declare(strict_types=1);

namespace Symplify\PHPStanExtensions\ErrorFormatter;

use PHPStan\Command\AnalysisResult;
use PHPStan\Command\ErrorFormatter\ErrorFormatter;
use Symfony\Component\Console\Style\OutputStyle;
use Symplify\PHPStanExtensions\Error\ErrorGrouper;

final class IgnoreErrorFormatter implements ErrorFormatter
{
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
            // success
            return 0;
        }

        $messagesToFrequency = $this->errorGrouper->groupErrorsToMessagesToFrequency(
            $analysisResult->getFileSpecificErrors()
        );

        if (! $messagesToFrequency) {
            $outputStyle->error(sprintf('Found %d non-ignorable errors', $analysisResult->getTotalErrorsCount()));

            // fail
            return 1;
        }

        $ignoredMessages = [];
        foreach ($messagesToFrequency as $message => $frequency) {
            $ignoredMessages[] = sprintf('\'#%s#\'# found %dx', preg_quote(rtrim($message, '.'), '#'), $frequency);
        }

        $outputStyle->title('Add to "parameters > ignoreErrors" section in "phpstan.neon"');
        $outputStyle->writeln('# phpstan.neon');
        $outputStyle->writeln('parameters:');
        $outputStyle->writeln('    ignoreErrors:');

        foreach ($ignoredMessages as $ignoredMessage) {
            $outputStyle->writeln('        - ' . $ignoredMessage);
        }
        $outputStyle->newLine(1);

        $outputStyle->error(sprintf('Found %d errors', $analysisResult->getTotalErrorsCount()));

        // fail
        return 1;
    }
}
