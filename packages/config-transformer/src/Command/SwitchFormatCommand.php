<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\ConfigTransformer\Configuration\Configuration;
use Symplify\ConfigTransformer\Converter\ConvertedContentFactory;
use Symplify\ConfigTransformer\FileSystem\ConfigFileDumper;
use Symplify\ConfigTransformer\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;

final class SwitchFormatCommand extends AbstractSymplifyCommand
{
    public function __construct(
        private Configuration $configuration,
        private ConfigFileDumper $configFileDumper,
        private ConvertedContentFactory $convertedContentFactory
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Converts XML/YAML configs to PHP format');

        $this->addArgument(
            Option::SOURCES,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Path to directory with configs'
        );

        $this->addOption(
            Option::TARGET_SYMFONY_VERSION,
            's',
            InputOption::VALUE_REQUIRED,
            'Symfony version to migrate config to',
            '3.2'
        );

        $this->addOption(Option::DRY_RUN, null, InputOption::VALUE_NONE, 'Dry run - no removal or config change');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->configuration->populateFromInput($input);

        $suffixes = $this->configuration->getInputSuffixes();
        $suffixesRegex = '#\.' . implode('|', $suffixes) . '$#';
        $fileInfos = $this->smartFinder->find($this->configuration->getSource(), $suffixesRegex);

        $convertedContents = $this->convertedContentFactory->createFromFileInfos($fileInfos);

        foreach ($convertedContents as $convertedContent) {
            $this->configFileDumper->dumpFile($convertedContent);
        }

        if (! $this->configuration->isDryRun()) {
            $this->smartFileSystem->remove($fileInfos);
        }

        $successMessage = sprintf('Processed %d file(s) to "PHP" format', count($fileInfos));
        $this->symfonyStyle->success($successMessage);

        return ShellCode::SUCCESS;
    }
}
