<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Clue\React\NDJson\Decoder;
use Clue\React\NDJson\Encoder;
use React\EventLoop\StreamSelectLoop;
use React\Socket\ConnectionInterface;
use React\Socket\TcpConnector;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Application\SingleFileProcessor;
use Symplify\EasyCodingStandard\Parallel\Enum\Action;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge;
use Symplify\EasyCodingStandard\Parallel\ValueObject\ReactCommand;
use Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\SystemError;
use Symplify\PackageBuilder\Yaml\ParametersMerger;
use Symplify\SmartFileSystem\SmartFileInfo;
use Throwable;

/**
 * Inspired at: https://github.com/phpstan/phpstan-src/commit/9124c66dcc55a222e21b1717ba5f60771f7dda92
 * https://github.com/phpstan/phpstan-src/blob/c471c7b050e0929daf432288770de673b394a983/src/Command/WorkerCommand.php
 *
 * ↓↓↓
 * https://github.com/phpstan/phpstan-src/commit/b84acd2e3eadf66189a64fdbc6dd18ff76323f67#diff-7f625777f1ce5384046df08abffd6c911cfbb1cfc8fcb2bdeaf78f337689e3e2
 */
final class WorkerCommand extends AbstractCheckCommand
{
    /**
     * @var string
     */
    private const RESULT = 'result';

    public function __construct(
        private SingleFileProcessor $singleFileProcessor,
        private ParametersMerger $parametersMerger
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();
        $this->setDescription('(Internal) Support for parallel process');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configuration = $this->configurationFactory->createFromInput($input);

        $streamSelectLoop = new StreamSelectLoop();
        $parallelIdentifier = $configuration->getParallelIdentifier();

        $tcpConnector = new TcpConnector($streamSelectLoop);

        $tcpConnector->connect(sprintf('127.0.0.1:%d', $configuration->getParallelPort()))
            ->then(function (ConnectionInterface $connection) use ($output, $parallelIdentifier, $configuration): void {
                $inDecoder = new Decoder($connection, true, 512, JSON_INVALID_UTF8_IGNORE);
                $outEncoder = new Encoder($connection, JSON_INVALID_UTF8_IGNORE);

                // handshake?
                $outEncoder->write([
                    ReactCommand::ACTION => Action::HELLO,
                    ReactCommand::IDENTIFIER => $parallelIdentifier,
                ]);

                $this->runWorker($outEncoder, $inDecoder, $configuration);
            });

        $streamSelectLoop->run();

        return self::SUCCESS;
    }

    private function runWorker(Encoder $encoder, Decoder $decoder, Configuration $configuration)
    {
        // 1. handle system error
        $handleErrorCallback = static function (Throwable $throwable) use ($encoder): void {
            $systemErrors = new SystemError($throwable->getLine(), $throwable->getMessage(), $throwable->getFile());

            $encoder->write([
                ReactCommand::ACTION => self::RESULT,
                self::RESULT => [
                    Bridge::SYSTEM_ERRORS => [$systemErrors],
                    Bridge::FILES_COUNT => 0,
                    Bridge::SYSTEM_ERRORS_COUNT => 1,
                ],
            ]);
            $encoder->end();
        };

        $encoder->on(ReactEvent::ERROR, $handleErrorCallback);

        // 2. collect diffs + errors from file processor
        $decoder->on(ReactEvent::DATA, function (array $json) use ($encoder, $configuration): void {
            $action = $json[ReactCommand::ACTION];
            if ($action !== Action::CHECK) {
                return;
            }

            $systemErrorsCount = 0;

            /** @var string[] $filePaths */
            $filePaths = $json[Bridge::FILES] ?? [];

            $errorAndFileDiffs = [];
            $systemErrors = [];

            foreach ($filePaths as $filePath) {
                try {
                    $smartFileInfo = new SmartFileInfo($filePath);
                    $currentErrorsAndFileDiffs = $this->singleFileProcessor->processFileInfo(
                        $smartFileInfo,
                        $configuration
                    );

                    $errorAndFileDiffs = $this->parametersMerger->merge(
                        $errorAndFileDiffs,
                        $currentErrorsAndFileDiffs
                    );
                } catch (Throwable $throwable) {
                    ++$systemErrorsCount;

                    $errorMessage = sprintf('System error: %s', $throwable->getMessage());
                    $errorMessage .= 'Run ECS with "--debug" option and post the report here: https://github.com/symplify/symplify/issues/new';
                    $systemErrors[] = new SystemError($throwable->getLine(), $errorMessage, $filePath);
                }
            }

            /**
             * this invokes all listeners listening $decoder->on(...) @see ReactEvent::DATA
             */
            $encoder->write([
                ReactCommand::ACTION => self::RESULT,
                self::RESULT => [
                    Bridge::CODING_STANDARD_ERRORS => $errorAndFileDiffs[Bridge::CODING_STANDARD_ERRORS] ?? [],
                    Bridge::FILE_DIFFS => $errorAndFileDiffs[Bridge::FILE_DIFFS] ?? [],
                    Bridge::FILES_COUNT => count($filePaths),
                    Bridge::SYSTEM_ERRORS => $systemErrors,
                    Bridge::SYSTEM_ERRORS_COUNT => $systemErrorsCount,
                ],
            ]);
        });

        $decoder->on(ReactEvent::ERROR, $handleErrorCallback);
    }
}
