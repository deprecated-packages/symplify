<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Console\Style;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Tester\CommandTester;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Tests\AbstractContainerAwareTestCase;

final class EasyCodingStandardStyleTest extends AbstractContainerAwareTestCase
{
    /**
     * @var EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;

    /**
     * @var ConsoleOutput
     */
    private $consoleOutput;

    /**
     * @var CommandTester
     */
    private $commandTester;

    protected function setUp(): void
    {
        $command = new Command('ecsStyle');
        $this->commandTester = new CommandTester($command);

        $this->easyCodingStandardStyle = $this->container->get(EasyCodingStandardStyle::class);
        $this->consoleOutput = $this->container->get(ConsoleOutput::class);
        $this->consoleOutput->setVerbosity(0);
    }

    public function testPrintErrors(): void
    {
        $this->easyCodingStandardStyle->printErrors([]);
    }
}