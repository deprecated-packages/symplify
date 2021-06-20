<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Clue\React\NDJson\Decoder;
use Clue\React\NDJson\Encoder;
use React\EventLoop\StreamSelectLoop;
use React\Stream\ReadableResourceStream;
use React\Stream\WritableResourceStream;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Application\SingleFileProcessor;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Action;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge;
use Symplify\EasyCodingStandard\Parallel\ValueObject\ReactCommand;
use Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent;
use Symplify\EasyCodingStandard\ValueObject\Error\SystemError;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\SmartFileInfo;
use Throwable;

/**
 * Inspired at https://github.com/phpstan/phpstan-src/commit/9124c66dcc55a222e21b1717ba5f60771f7dda92
 */
final class WorkerCommand extends AbstractCheckCommand
{
    public function __construct(
        private SingleFileProcessor $singleFileProcessor
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
        $stdOutEncoder = new Encoder(new WritableResourceStream(STDOUT, $streamSelectLoop));

        $handleErrorCallback = static function (Throwable $throwable) use ($stdOutEncoder): void {
            $systemErrors = new SystemError($throwable->getLine(), $throwable->getMessage(), $throwable->getFile());
            $stdOutEncoder->write([
                Bridge::SYSTEM_ERRORS => [$systemErrors],
                Bridge::FILES_COUNT => 0,
                Bridge::SYSTEM_ERRORS_COUNT => 1,
            ]);
            $stdOutEncoder->end();
        };
        $stdOutEncoder->on(ReactEvent::ERROR, $handleErrorCallback);

        // collectErrors from file processor
        $decoder = new Decoder(new ReadableResourceStream(STDIN, $streamSelectLoop), true);
        $decoder->on(ReactEvent::DATA, function (array $json) use ($stdOutEncoder, $configuration): void {
            $action = $json[ReactCommand::ACTION];

            if ($action === Action::CHECK) {
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

                        $errorAndFileDiffs = array_merge($errorAndFileDiffs, $currentErrorsAndFileDiffs);
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
                $stdOutEncoder->write([
                    Bridge::CODING_STANDARD_ERRORS => $errorAndFileDiffs[Bridge::CODING_STANDARD_ERRORS] ?? [],
                    Bridge::FILE_DIFFS => $errorAndFileDiffs[Bridge::FILE_DIFFS] ?? [],
                    Bridge::FILES_COUNT => count($filePaths),
                    Bridge::SYSTEM_ERRORS => $systemErrors,
                    Bridge::SYSTEM_ERRORS_COUNT => $systemErrorsCount,
                ]);
            } elseif ($action === Action::QUIT) {
                $stdOutEncoder->end();
            }
        });
        $decoder->on(ReactEvent::ERROR, $handleErrorCallback);

        $streamSelectLoop->run();

        return ShellCode::SUCCESS;
    }
}
