<?php declare(strict_types=1);

namespace Symplify\MultiCodingStandard\Application;

use Symplify\MultiCodingStandard\Application\Command\RunApplicationCommand;
use Symplify\MultiCodingStandard\PhpCsFixer\Application\Application as PhpCsFixerApplication;
use Symplify\MultiCodingStandard\PhpCsFixer\Application\Command\RunApplicationCommand
    as PhpCsFixerRunApplicationCommand;
use Symplify\SniffRunner\Application\Application as SniffRunnerApplication;
use Symplify\SniffRunner\Application\Command\RunApplicationCommand as Php7CodeSnifferRunApplicationCommand;

final class Application
{
    /**
     * @var SniffRunnerApplication
     */
    private $sniffRunnerApplication;

    /**
     * @var PhpCsFixerApplication
     */
    private $phpCsFixerApplication;

    public function __construct(
        SniffRunnerApplication $php7CodeSnifferApplication,
        PhpCsFixerApplication $phpCsFixerApplication
    ) {
        $this->sniffRunnerApplication = $php7CodeSnifferApplication;
        $this->phpCsFixerApplication = $phpCsFixerApplication;
    }

    public function runCommand(RunApplicationCommand $command)
    {
        $this->sniffRunnerApplication->runCommand(
            $this->createPhp7CodeSnifferRunApplicationCommand($command)
        );

        $this->phpCsFixerApplication->runCommand(
            $this->createPhpCsFixerRunApplicationCommand($command)
        );
    }

    private function createPhp7CodeSnifferRunApplicationCommand(
        RunApplicationCommand $command
    ): Php7CodeSnifferRunApplicationCommand {
        return new Php7CodeSnifferRunApplicationCommand(
            $command->getSource(),
            $command->getJsonConfiguration()['standards'] ?? [],
            $command->getJsonConfiguration()['sniffs'] ?? [],
            $command->getJsonConfiguration()['exclude-sniffs'] ?? [],
            $command->isFixer()
        );
    }

    private function createPhpCsFixerRunApplicationCommand(
        RunApplicationCommand $command
    ): PhpCsFixerRunApplicationCommand {
        return new PhpCsFixerRunApplicationCommand(
            $command->getSource(),
            $command->getJsonConfiguration()['rules'] ?? [],
            $command->getJsonConfiguration()['exclude-rules'] ?? [],
            $command->isFixer()
        );
    }
}
