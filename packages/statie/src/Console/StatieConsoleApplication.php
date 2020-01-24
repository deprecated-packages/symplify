<?php

declare(strict_types=1);

namespace Symplify\Statie\Console;

use Composer\XdebugHandler\XdebugHandler;
use Jean85\PrettyVersions;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
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

    public function doRun(InputInterface $input, OutputInterface $output): int
    {
        // @fixes https://github.com/rectorphp/rector/issues/2205
        $isXdebugAllowed = $input->hasParameterOption('--xdebug');
        if (! $isXdebugAllowed && ! defined('PHPUNIT_COMPOSER_INSTALL')) {
            $xdebug = new XdebugHandler('statie', '--ansi');
            $xdebug->check();
            unset($xdebug);
        }

        return parent::doRun($input, $output);
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
            'xdebug',
            null,
            InputOption::VALUE_NONE,
            'Allow running xdebug'
        ));

        $inputDefinition->addOption(new InputOption(
            'config',
            'c',
            InputOption::VALUE_REQUIRED,
            'Path to config file.',
            'statie.(yml|yaml)'
        ));
    }
}
