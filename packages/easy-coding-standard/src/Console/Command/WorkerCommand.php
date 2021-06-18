<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Clue\React\NDJson\Decoder;
use Clue\React\NDJson\Encoder;
use React\EventLoop\StreamSelectLoop;
use React\Stream\ReadableResourceStream;
use React\Stream\WritableResourceStream;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Application\SingleFileProcessor;
use Symplify\EasyCodingStandard\ValueObject\Error\SystemError;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\SmartFileSystem\SmartFileInfo;
use Throwable;

/**
 * Inspired at https://github.com/phpstan/phpstan-src/commit/9124c66dcc55a222e21b1717ba5f60771f7dda92
 */
final class WorkerCommand extends AbstractSymplifyCommand
{
    public function __construct(
        private SingleFileProcessor $singleFileProcessor
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('(Internal) Support for parallel process');
        $this->addArgument(
            Option::PATHS,
            InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
            'Paths with source code to run analysis on'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $singleFileProcessor = $this->singleFileProcessor;

        $streamSelectLoop = new StreamSelectLoop();

        $stdOutEncoder = new Encoder(new WritableResourceStream(STDOUT, $streamSelectLoop));

        $handleError = static function (Throwable $error) use ($stdOutEncoder): void {
            $stdOutEncoder->write([
                'errors' => [$error->getMessage()],
                'filesCount' => 0,
                'internalErrorsCount' => 1,
            ]);
            $stdOutEncoder->end();
        };
        $stdOutEncoder->on('error', $handleError);

        // todo collectErrors (from Analyser)
        $decoder = new Decoder(new ReadableResourceStream(STDIN, $streamSelectLoop), true);
        $decoder->on('data', static function (array $json) use ($singleFileProcessor, $stdOutEncoder): void {
            $inferrablePropertyTypesFromConstructorHelper = null;
            $action = $json['action'];
            if ($action === 'analyse') {
                $internalErrorsCount = 0;

                $filePaths = $json['files'];

                $errors = [];
                foreach ($filePaths as $filePath) {
                    try {
                        $singleFileProcessor->processFileInfo(new SmartFileInfo($filePath));
                        $fileErrors = $fileAnalyser->analyseFile($filePath);
                        foreach ($fileErrors as $fileError) {
                            $errors[] = $fileError;
                        }
                    } catch (Throwable $throwable) {
                        ++$internalErrorsCount;
                        $internalErrorMessage = sprintf('Internal error: %s', $throwable->getMessage());
                        $internalErrorMessage .= 'Run ECS with --debug option';
                        $errors[] = new SystemError($throwable->getLine(), $internalErrorMessage, $filePath);
                    }
                }

                $stdOutEncoder->write([
                    'errors' => $errors,
                    'filesCount' => is_countable($filePaths) ? count($filePaths) : 0,
                    'hasInferrablePropertyTypesFromConstructor' => $inferrablePropertyTypesFromConstructorHelper->hasInferrablePropertyTypesFromConstructor(),
                    'internalErrorsCount' => $internalErrorsCount,
                ]);
            } elseif ($action === 'quit') {
                $stdOutEncoder->end();
            }
        });
        $decoder->on('error', $handleError);

        $streamSelectLoop->run();

        return 0;
    }
}
