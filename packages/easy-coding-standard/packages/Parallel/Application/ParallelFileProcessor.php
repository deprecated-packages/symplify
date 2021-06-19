<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Parallel\Application;

use Closure;
use Clue\React\NDJson\Decoder;
use Clue\React\NDJson\Encoder;
use React\ChildProcess\Process;
use React\EventLoop\StreamSelectLoop;
use Symfony\Component\Console\Input\InputInterface;
use Symplify\EasyCodingStandard\Parallel\Command\WorkerCommandLineFactory;
use Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent;
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
    private const CHECK = 'check';

    public function __construct(
        private ParameterProvider $parameterProvider,
        private WorkerCommandLineFactory $workerCommandLineFactory
    ) {
    }

    /**
     * @param Closure(int):void|null $postFileCallback
     * @return array{FileError}
     */
    public function analyse(
        Schedule $schedule,
        string $mainScript,
        ?Closure $postFileCallback,
        ?string $projectConfigFile,
        InputInterface $input
    ): array {
        $systemErrorsCountLimit = $this->parameterProvider->provideIntParameter(Option::SYSTEM_ERROR_COUNT_LIMIT);

        $jobs = array_reverse($schedule->getJobs());
        $streamSelectLoop = new StreamSelectLoop();

        $processes = [];
        $numberOfProcesses = $schedule->getNumberOfProcesses();
        $errors = [];
        $systemErrors = [];

        $command = $this->workerCommandLineFactory->create($mainScript, $projectConfigFile, $input);

        $systemErrorsCount = 0;
        $reachedSystemErrorsCountLimit = false;

        $handleError = static function (Throwable $error) use (
            $streamSelectLoop,
            &$systemErrors,
            &$systemErrorsCount,
            &$reachedSystemErrorsCountLimit
        ): void {
            $streamSelectLoop = null;
            $systemErrors[] = 'System error: ' . $error->getMessage();
            ++$systemErrorsCount;
            $reachedSystemErrorsCountLimit = true;
            $streamSelectLoop->stop();
        };

        for ($i = 0; $i < $numberOfProcesses; ++$i) {
            if ($jobs === []) {
                break;
            }

            $childProcess = new Process($command);
            $childProcess->start($streamSelectLoop);

            $processStdInEncoder = new Encoder($childProcess->stdin);
            $processStdInEncoder->on(ReactEvent::ERROR, $handleError);

            $processStdOutDecoder = new Decoder($childProcess->stdout, true, 512, 0, 4 * 1024 * 1024);
            $processStdOutDecoder->on(ReactEvent::DATA, function (array $json) use (
                $childProcess,
                &$systemErrors,
                &$errors,
                &$jobs,
                $processStdInEncoder,
                $postFileCallback,
                &$systemErrorsCount,
                &$reachedSystemErrorsCountLimit,
                $streamSelectLoop
            ): void {
                $systemErrorsCountLimit = null;
                $streamSelectLoop = null;

                // @todo
                foreach ($json['errors'] as $jsonError) {
                    if (is_string($jsonError)) {
                        $systemErrors[] = sprintf('System error: %s', $jsonError);
                        continue;
                    }

                    $errors[] = Error::decode($jsonError);
                }

                if ($postFileCallback !== null) {
                    $postFileCallback($json['files_count']);
                }

                $systemErrorsCount += $json['system_errors_count'];
                if ($systemErrorsCount >= $systemErrorsCountLimit) {
                    $reachedSystemErrorsCountLimit = true;
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
                    self::ACTION => self::CHECK,
                    'files' => $job,
                ]);
            });
            $processStdOutDecoder->on(ReactEvent::ERROR, $handleError);

            $stdErrStreamBuffer = new StreamBuffer($childProcess->stderr);
            $childProcess->on(ReactEvent::EXIT, static function ($exitCode) use (
                &$systemErrors,
                $stdErrStreamBuffer
            ): void {
                if ($exitCode === 0) {
                    return;
                }

                $systemErrors[] = sprintf('Child process error: %s', $stdErrStreamBuffer->getBuffer());
            });

            $job = array_pop($jobs);
            $processStdInEncoder->write([
                self::ACTION => self::CHECK,
                'files' => $job,
            ]);
            $processes[] = $childProcess;
        }

        $streamSelectLoop->run();

        if ($reachedSystemErrorsCountLimit) {
            $systemErrors[] = sprintf(
                'Reached system errors count limit of %d, exiting...',
                $systemErrorsCountLimit
            );
        }

        return [
            'errors' => $errors,
            // @todo
            'file_diffs' => $fileDiffs ?? [],
            'system_errors' => $systemErrors,
        ];
    }
}
