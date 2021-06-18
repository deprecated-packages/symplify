<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Parallel\Application;

use Closure;
use Clue\React\NDJson\Decoder;
use Clue\React\NDJson\Encoder;
use React\ChildProcess\Process;
use React\EventLoop\StreamSelectLoop;
use Symfony\Component\Console\Input\InputInterface;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Schedule;
use Symplify\EasyCodingStandard\Parallel\ValueObject\StreamBuffer;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Throwable;

/**
 * @see https://github.com/phpstan/phpstan-src/commit/9124c66dcc55a222e21b1717ba5f60771f7dda92#diff-39c7a3b0cbb217bbfff96fbb454e6e5e60c74cf92fbb0f9d246b8bebbaad2bb0
 */
final class ParallelFileProcessor
{
    /**
     * @var string
     */
    private const ACTION = 'action';

    /**
     * @var string
     */
    private const ANALYSE = 'analyse';

    /**
     * @var string[]
     */
    private const OPTIONS = ['paths', 'autoload-file', 'xdebug'];

    public function __construct(
        private ParameterProvider $parameterProvider,
    ) {
    }

    /**
     * @param Closure(int):void|null $postFileCallback
     * @return array{errors: (string[]|\PHPStan\Analyser\Error[])}
     */
    public function analyse(
        Schedule $schedule,
        string $mainScript,
        bool $onlyFiles,
        ?Closure $postFileCallback,
        ?string $projectConfigFile,
        InputInterface $input
    ): array {
        $internalErrorsCountLimit = $this->parameterProvider->provideIntParameter(Option::INTERNAL_ERROR_COUNT_LIMIT);

        $jobs = array_reverse($schedule->getJobs());
        $streamSelectLoop = new StreamSelectLoop();

        $processes = [];
        $numberOfProcesses = $schedule->getNumberOfProcesses();
        $errors = [];
        $internalErrors = [];
        $hasInferrablePropertyTypesFromConstructor = false;

        $command = $this->getWorkerCommand($mainScript, $projectConfigFile, $input);

        $internalErrorsCount = 0;
        $reachedInternalErrorsCountLimit = false;

        $handleError = static function (Throwable $error) use (
            $streamSelectLoop,
            &$internalErrors,
            &$internalErrorsCount,
            &$reachedInternalErrorsCountLimit
        ): void {
            $streamSelectLoop = null;
            $internalErrors[] = 'Internal error: ' . $error->getMessage();
            ++$internalErrorsCount;
            $reachedInternalErrorsCountLimit = true;
            $streamSelectLoop->stop();
        };

        for ($i = 0; $i < $numberOfProcesses; ++$i) {
            if ($jobs === []) {
                break;
            }

            $childProcess = new Process($command);
            $childProcess->start($streamSelectLoop);

            $processStdInEncoder = new Encoder($childProcess->stdin);
            $processStdInEncoder->on('error', $handleError);

            $processStdOutDecoder = new Decoder($childProcess->stdout, true, 512, 0, 4 * 1024 * 1024);
            $processStdOutDecoder->on('data', function (array $json) use (
                $childProcess,
                &$internalErrors,
                &$errors,
                &$jobs,
                $processStdInEncoder,
                $postFileCallback,
                &$hasInferrablePropertyTypesFromConstructor,
                &$internalErrorsCount,
                &$reachedInternalErrorsCountLimit,
                $streamSelectLoop
            ): void {
                $internalErrorsCountLimit = null;
                $streamSelectLoop = null;
                foreach ($json['errors'] as $jsonError) {
                    if (is_string($jsonError)) {
                        $internalErrors[] = sprintf('Internal error: %s', $jsonError);
                        continue;
                    }

                    $errors[] = Error::decode($jsonError);
                }

                if ($postFileCallback !== null) {
                    $postFileCallback($json['filesCount']);
                }

                $hasInferrablePropertyTypesFromConstructor = $hasInferrablePropertyTypesFromConstructor || $json['hasInferrablePropertyTypesFromConstructor'];
                $internalErrorsCount += $json['internalErrorsCount'];

                if ($internalErrorsCount >= $internalErrorsCountLimit) {
                    $reachedInternalErrorsCountLimit = true;
                    $streamSelectLoop->stop();
                }

                if ($jobs === []) {
                    foreach ($childProcess->pipes as $pipe) {
                        $pipe->close();
                    }

                    $processStdInEncoder->write([
                        self::ACTION => 'quit',
                    ]);
                    return;
                }

                $job = array_pop($jobs);
                $processStdInEncoder->write([
                    self::ACTION => self::ANALYSE,
                    'files' => $job,
                ]);
            });
            $processStdOutDecoder->on('error', $handleError);

            $stdErrStreamBuffer = new StreamBuffer($childProcess->stderr);
            $childProcess->on('exit', static function ($exitCode) use (&$internalErrors, $stdErrStreamBuffer): void {
                if ($exitCode === 0) {
                    return;
                }

                $internalErrors[] = sprintf('Child process error: %s', $stdErrStreamBuffer->getBuffer());
            });

            $job = array_pop($jobs);
            $processStdInEncoder->write([
                self::ACTION => self::ANALYSE,
                'files' => $job,
            ]);
            $processes[] = $childProcess;
        }

        $streamSelectLoop->run();

        if ($reachedInternalErrorsCountLimit) {
            $internalErrors[] = sprintf(
                'Reached internal errors count limit of %d, exiting...',
                $internalErrorsCountLimit
            );
        }

        return [
            'errors' => array_merge(
                $ignoredErrorHelperResult->process($errors, $onlyFiles, $reachedInternalErrorsCountLimit),
                $internalErrors,
                $ignoredErrorHelperResult->getWarnings()
            ),
            'hasInferrablePropertyTypesFromConstructor' => $hasInferrablePropertyTypesFromConstructor,
        ];
    }

    private function getWorkerCommand(
        string $mainScript,
        ?string $projectConfigFile,
        InputInterface $input
    ): string {
        $args = array_merge([PHP_BINARY, $mainScript], array_slice($_SERVER['argv'], 1));
        $processCommandArray = [];
        foreach ($args as $arg) {
            if (in_array($arg, [self::ANALYSE, 'analyze'], true)) {
                break;
            }

            $processCommandArray[] = escapeshellarg($arg);
        }

        $processCommandArray[] = 'worker';
        if ($projectConfigFile !== null) {
            $processCommandArray[] = '--configuration';
            $processCommandArray[] = escapeshellarg($projectConfigFile);
        }
        foreach (self::OPTIONS as $optionName) {
            /** @var bool|string|null $optionValue */
            $optionValue = $input->getOption($optionName);
            if (is_bool($optionValue)) {
                if ($optionValue) {
                    $processCommandArray[] = sprintf('--%s', $optionName);
                }
                continue;
            }
            if ($optionValue === null) {
                continue;
            }

            $processCommandArray[] = sprintf('--%s', $optionName);
            $processCommandArray[] = escapeshellarg($optionValue);
        }

        /** @var string[] $paths */
        $paths = $input->getArgument('paths');
        foreach ($paths as $path) {
            $processCommandArray[] = escapeshellarg($path);
        }

        return implode(' ', $processCommandArray);
    }
}
