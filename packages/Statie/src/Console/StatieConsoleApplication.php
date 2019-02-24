<?php declare(strict_types=1);

namespace Symplify\Statie\Console;

use Jean85\PrettyVersions;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symplify\PackageBuilder\Console\HelpfulApplicationTrait;

final class StatieConsoleApplication extends Application
{
    use HelpfulApplicationTrait;

    /**
     * @param Command[] $commands
     */
    public function __construct(array $commands)
    {
        parent::__construct('Statie', $this->getPrettyVersion());
        $this->addCommands($commands);
    }

    protected function getDefaultInputDefinition(): InputDefinition
    {
        $inputDefinition = parent::getDefaultInputDefinition();
        $this->addExtraOptions($inputDefinition);

        return $inputDefinition;
    }

    private function getPrettyVersion(): string
    {
        $version = PrettyVersions::getVersion('symplify/statie');

        return $version->getPrettyVersion();
    }

    private function addExtraOptions(InputDefinition $inputDefinition): void
    {
        $inputDefinition->addOption(new InputOption(
            'config',
            'c',
            InputOption::VALUE_REQUIRED,
            'Path to config file.',
            'statie.(yml|yaml)'
        ));
    }
}
