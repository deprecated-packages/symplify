<?php

declare(strict_types=1);

namespace Symplify\EasyParallel\Tests\CommandLine;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symplify\EasyParallel\CommandLine\WorkerCommandLineFactory;
use Symplify\EasyParallel\Reflection\CommandFromReflectionFactory;
use Symplify\EasyParallel\Tests\CommandLine\Source\MainCommand;
use Symplify\EasyParallel\Tests\CommandLine\Source\TestOption;

final class WorkerCommandLineFactoryTest extends TestCase
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

    private CommandFromReflectionFactory $commandFromReflectionFactory;

    protected function setUp(): void
    {
        $this->workerCommandLineFactory = new WorkerCommandLineFactory();
        $this->commandFromReflectionFactory = new CommandFromReflectionFactory();
    }

    /**
     * @dataProvider provideData()
     *
     * @param class-string<Command> $commandClass
     * @param array<string, mixed> $inputParameters
     */
    public function test(
        string $commandClass,
        string $pathsOptionName,
        array $inputParameters,
        string $expectedCommand
    ): void {
        $inputDefinition = $this->prepareProcessCommandDefinition($commandClass);
        $arrayInput = new ArrayInput($inputParameters, $inputDefinition);

        $workerCommandLine = $this->workerCommandLineFactory->create(
            self::DUMMY_MAIN_SCRIPT,
            $commandClass,
            'worker',
            $pathsOptionName,
            null,
            $arrayInput,
            'identifier',
            2000
        );

        $this->assertSame($expectedCommand, $workerCommandLine);
    }

    /**
     * @return Iterator<array<int, array<string, string|string[]|bool>>|string[]>
     */
    public function provideData(): Iterator
    {
        $cliInputOptions = array_slice($_SERVER['argv'], 1);

        $expectedCommandLinesString = $this->createExpectedCommandLinesString($cliInputOptions);

        yield [
            MainCommand::class,
            TestOption::PATHS,
            [
                self::COMMAND => 'process',
                TestOption::PATHS => ['src'],
            ],
            $expectedCommandLinesString,
        ];

        yield [
            MainCommand::class,
            TestOption::PATHS,
            [
                self::COMMAND => 'process',
                TestOption::PATHS => ['src'],
                '--' . TestOption::OUTPUT_FORMAT => 'console',
            ],
            $expectedCommandLinesString,
        ];
    }

    /**
     * @param class-string<Command> $mainCommandClass
     */
    private function prepareProcessCommandDefinition(string $mainCommandClass): InputDefinition
    {
        $mainCommand = $this->commandFromReflectionFactory->create($mainCommandClass);

        $inputDefinition = $mainCommand->getDefinition();

        // not sure why, but the 1st argument "command" is missing; this is needed for a command name
        $arguments = $inputDefinition->getArguments();
        $commandInputArgument = new InputArgument(self::COMMAND, InputArgument::REQUIRED);
        $arguments = array_merge([$commandInputArgument], $arguments);

        $inputDefinition->setArguments($arguments);

        return $inputDefinition;
    }

    private function createExpectedCommandLinesString(array $cliInputOptions): string
    {
        $commandLineString = "'" . PHP_BINARY . "' '" . self::DUMMY_MAIN_SCRIPT . "'";

        // in some cases, the test does not have any options/args, e.g. when running it like "vendor/bin/phpunit"
        // vs." vendor/bin/phpunit <some_arg>"
        if ($cliInputOptions) {
            $cliInputOptionsAsString = implode("' '", $cliInputOptions);
            $commandLineString .= " '" . $cliInputOptionsAsString . "'";
        }

        $commandLineString .= " worker --port 2000 --identifier 'identifier' 'src' --output-format 'json' --no-ansi";
        return $commandLineString;
    }
}
