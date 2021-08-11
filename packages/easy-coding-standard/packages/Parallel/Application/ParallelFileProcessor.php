<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Parallel\Application;

use Closure;
use Clue\React\NDJson\Decoder;
use Clue\React\NDJson\Encoder;
use React\ChildProcess\Process;
use React\EventLoop\StreamSelectLoop;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symplify\EasyCodingStandard\Parallel\Command\WorkerCommandLineFactory;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Action;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge;
use Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Schedule;
use Symplify\EasyCodingStandard\Parallel\ValueObject\StreamBuffer;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use Symplify\EasyCodingStandard\ValueObject\Error\SystemError;
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

    private int $systemErrorsCountLimit;

    public function __construct(
        ParameterProvider $parameterProvider,
        private WorkerCommandLineFactory $workerCommandLineFactory
    ) {
        $this->systemErrorsCountLimit = $parameterProvider->provideIntParameter(Option::SYSTEM_ERROR_COUNT_LIMIT);
    }

    /**
     * @param Closure(int): void|null $postFileCallback Used for progress bar jump
     * @return mixed[]
     */
    public function analyse(
        Schedule $schedule,
        string $mainScript,
        Closure $postFileCallback,
        ?string $projectConfigFile,
        InputInterface $input
    ): array {
        $jobs = array_reverse($schedule->getJobs());
        $streamSelectLoop = new StreamSelectLoop();

        // basic properties setup
        $numberOfProcesses = $schedule->getNumberOfProcesses();

        $codingStandardErrors = [];
        $fileDiffs = [];

        $systemErrors = [];
        $systemErrorsCount = 0;
        $reachedSystemErrorsCountLimit = false;

        $workerCommandLine = $this->workerCommandLineFactory->create($mainScript, $projectConfigFile, $input);

        $handleErrorCallable = static function (Throwable $throwable) use (
            $streamSelectLoop,
            &$systemErrors,
            &$systemErrorsCount,
            &$reachedSystemErrorsCountLimit
        ): void {
            $systemErrors[] = new SystemError($throwable->getLine(), $throwable->getMessage(), $throwable->getFile());
            ++$systemErrorsCount;
            $reachedSystemErrorsCountLimit = true;
            $streamSelectLoop->stop();
        };

        for ($i = 0; $i < $numberOfProcesses; ++$i) {
            // nothing else to process, stop now
            if ($jobs === []) {
                break;
            }

            $childProcess = new Process($workerCommandLine);
            $childProcess->start($streamSelectLoop);

            // handlers converting objects to json string
            // @see https://freesoft.dev/program/64329369#encoder
            $processStdInEncoder = new Encoder($childProcess->stdin);
            $processStdInEncoder->on(ReactEvent::ERROR, $handleErrorCallable);

            // handlers converting string json to array
            // @see https://freesoft.dev/program/64329369#decoder
            $processStdOutDecoder = new Decoder($childProcess->stdout, true, 512, 0, 4 * 1024 * 1024);
            $processStdOutDecoder->on(ReactEvent::DATA, function (array $json) use (
                $childProcess,
                &$systemErrors,
                &$codingStandardErrors,
                &$fileDiffs,
                &$jobs,
                $processStdInEncoder,
                $postFileCallback,
                &$systemErrorsCount,
                &$reachedSystemErrorsCountLimit,
                $streamSelectLoop
            ): void {
                // unpack coding standard errors and file diffs from subprocess to objects here
                foreach ($json[Bridge::CODING_STANDARD_ERRORS] as $codingStandardErrorJson) {
                    $codingStandardErrors[] = CodingStandardError::decode($codingStandardErrorJson);
                }

                foreach ($json[Bridge::FILE_DIFFS] as $fileDiffsJson) {
                    $fileDiffs[] = FileDiff::decode($fileDiffsJson);
                }

                // invoke after the file is processed, e.g. to increase progress bar
                $postFileCallback($json[Bridge::FILES_COUNT]);

                $systemErrorsCount += $json[Bridge::SYSTEM_ERRORS_COUNT];
                if ($systemErrorsCount >= $this->systemErrorsCountLimit) {
                    $reachedSystemErrorsCountLimit = true;
                    $streamSelectLoop->stop();
                }

                // all jobs are finished → close everything and quite
                if ($jobs === []) {
                    foreach ($childProcess->pipes as $pipe) {
                        $pipe->close();
                    }

                    $processStdInEncoder->write([
                        self::ACTION => Action::QUIT,
                    ]);
                    return;
                }

                // start a new job
                $job = array_pop($jobs);
                $processStdInEncoder->write([
                    self::ACTION => Action::CHECK,
                    Bridge::FILES => $job,
                ]);
            });
            $processStdOutDecoder->on(ReactEvent::ERROR, $handleErrorCallable);

            $stdErrStreamBuffer = new StreamBuffer($childProcess->stderr);
            $childProcess->on(ReactEvent::EXIT, static function ($exitCode) use (
                &$systemErrors,
                $stdErrStreamBuffer
            ): void {
                if ($exitCode === Command::SUCCESS) {
                    return;
                }

                $systemErrors[] = sprintf('Child process error: %s', $stdErrStreamBuffer->getBuffer());
            });

            $job = array_pop($jobs);
            $processStdInEncoder->write([
                self::ACTION => Action::CHECK,
                Bridge::FILES => $job,
                Bridge::SYSTEM_ERRORS => $systemErrors,
                Bridge::SYSTEM_ERRORS_COUNT => count($systemErrors),
            ]);
        }

        $streamSelectLoop->run();

        if ($reachedSystemErrorsCountLimit) {
            $systemErrors[] = sprintf(
                'Reached system errors count limit of %d, exiting...',
                $this->systemErrorsCountLimit
            );
        }

        return [
            Bridge::CODING_STANDARD_ERRORS => $codingStandardErrors,
            Bridge::FILE_DIFFS => $fileDiffs,
            Bridge::SYSTEM_ERRORS => $systemErrors,
            Bridge::SYSTEM_ERRORS_COUNT => count($systemErrors),
        ];
    }
}
