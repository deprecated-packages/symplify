<?php declare(strict_types=1);

namespace Symplify\Statie\Console;

use Jean85\PrettyVersions;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symplify\PackageBuilder\Console\HelpfulApplicationTrait;

final class Application extends SymfonyApplication
{
    use HelpfulApplicationTrait;

    public function __construct()
    {
        parent::__construct('Statie', $this->getPrettyVersion());
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
