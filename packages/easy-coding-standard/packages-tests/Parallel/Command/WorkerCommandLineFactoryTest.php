<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Parallel\Command;

use Iterator;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symplify\EasyCodingStandard\Console\Command\CheckCommand;
use Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\Parallel\Command\WorkerCommandLineFactory;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class WorkerCommandLineFactoryTest extends AbstractKernelTestCase
{
    /**
     * @var string
     */
    private const COMMAND = 'command';

    /**
     * @var string
     */
    private const DUMMY_MAIN_SCRIPT = 'main_script';

    private WorkerCommandLineFactory $workerCommandLineFactory;

    private CheckCommand $checkCommand;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCodingStandardKernel::class);

        $this->workerCommandLineFactory = $this->getService(WorkerCommandLineFactory::class);
        $this->checkCommand = $this->getService(CheckCommand::class);
    }

    /**
     * @dataProvider provideData()
     * @param array<string, mixed> $inputParameters
     */
    public function test(array $inputParameters, string $expectedCommand): void
    {
        $inputDefinition = $this->prepareCheckCommandDefinition();
        $arrayInput = new ArrayInput($inputParameters, $inputDefinition);

        $workerCommandLine = $this->workerCommandLineFactory->create(self::DUMMY_MAIN_SCRIPT, null, $arrayInput);

        $this->assertSame($expectedCommand, $workerCommandLine);
    }

    /**
     * @return Iterator<array<int, array<string, string|string[]|bool>>|string[]>
     */
    public function provideData(): Iterator
    {
        $cliInputOptions = array_slice($_SERVER['argv'], 1);
        $cliInputOptionsAsString = implode("' '", $cliInputOptions);

        yield [
            [
                self::COMMAND => 'check',
                Option::PATHS => ['src'],
                '--' . Option::FIX => true,
            ],
            "'" . PHP_BINARY . "' '" . self::DUMMY_MAIN_SCRIPT . "' '" . $cliInputOptionsAsString . "' worker --fix 'src' --output-format 'json' --no-ansi",
        ];

        yield [
            [
                self::COMMAND => 'check',
                Option::PATHS => ['src'],
                '--' . Option::FIX => true,
                '--' . Option::OUTPUT_FORMAT => ConsoleOutputFormatter::NAME,
            ],
            "'" . PHP_BINARY . "' '" . self::DUMMY_MAIN_SCRIPT . "' '" . $cliInputOptionsAsString . "' worker --fix 'src' --output-format 'json' --no-ansi",
        ];
    }

    private function prepareCheckCommandDefinition(): InputDefinition
    {
        $inputDefinition = $this->checkCommand->getDefinition();

        // not sure why, but the 1st argument "command" is missing; this is needed for a command name
        $arguments = $inputDefinition->getArguments();
        $commandInputArgument = new InputArgument(self::COMMAND, InputArgument::REQUIRED);
        $arguments = array_merge([$commandInputArgument], $arguments);

        $inputDefinition->setArguments($arguments);

        return $inputDefinition;
    }
}
