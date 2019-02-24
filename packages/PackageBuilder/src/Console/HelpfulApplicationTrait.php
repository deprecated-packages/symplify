<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Console;

use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Descriptor\TextDescriptor;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * must be part of child @see \Symfony\Component\Console\Application
 */
trait HelpfulApplicationTrait
{
    protected function doRunCommand(Command $command, InputInterface $input, OutputInterface $output): int
    {
        return $this->doRunCommandAndShowHelpOnArgumentError($command, $input, $output);
    }

    private function doRunCommandAndShowHelpOnArgumentError(
        Command $command,
        InputInterface $input,
        OutputInterface $output
    ): int {
        try {
            return parent::doRunCommand($command, $input, $output);
        } catch (RuntimeException $runtimeException) {
            if (Strings::match($runtimeException->getMessage(), '#Not enough arguments#')) {
                $this->cleanExtraCommandArgument($command);
                (new TextDescriptor())->describe($output, $command);

                return ShellCode::SUCCESS;
            }

            throw $runtimeException;
        }
    }

    /**
     * Sometimes there is "command" argument,
     * not really needed on fail of missing argument
     */
    private function cleanExtraCommandArgument(Command $command): void
    {
        $arguments = $command->getDefinition()->getArguments();
        if (! isset($arguments['command'])) {
            return;
        }

        unset($arguments['command']);
        $command->getDefinition()->setArguments($arguments);
    }
}
