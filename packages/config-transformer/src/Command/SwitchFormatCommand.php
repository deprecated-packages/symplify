<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Command;

use Migrify\MigrifyKernel\Command\AbstractMigrifyCommand;
use Migrify\MigrifyKernel\ValueObject\MigrifyOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\ConfigTransformer\Configuration\Configuration;
use Symplify\ConfigTransformer\Converter\ConvertedContentFactory;
use Symplify\ConfigTransformer\FileSystem\ConfigFileDumper;
use Symplify\ConfigTransformer\ValueObject\Format;
use Symplify\ConfigTransformer\ValueObject\Option;
use Symplify\PackageBuilder\Console\ShellCode;

final class SwitchFormatCommand extends AbstractMigrifyCommand
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ConfigFileDumper
     */
    private $configFileDumper;

    /**
     * @var ConvertedContentFactory
     */
    private $convertedContentFactory;

    public function __construct(
        Configuration $configuration,
        ConfigFileDumper $configFileDumper,
        ConvertedContentFactory $convertedContentFactory
    ) {
        parent::__construct();

        $this->configuration = $configuration;
        $this->configFileDumper = $configFileDumper;
        $this->convertedContentFactory = $convertedContentFactory;
    }

    protected function configure(): void
    {
        $this->setDescription('Converts XML/YAML configs to YAML/PHP format');

        $this->addArgument(
            MigrifyOption::SOURCES,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Path to directory with configs'
        );

        $this->addOption(Option::INPUT_FORMAT, 'i', InputOption::VALUE_REQUIRED, 'Config format to input');

        $this->addOption(
            Option::OUTPUT_FORMAT,
            'o',
            InputOption::VALUE_REQUIRED,
            'Config format to output',
            Format::PHP
        );

        $this->addOption(
            Option::TARGET_SYMFONY_VERSION,
            's',
            InputOption::VALUE_REQUIRED,
            'Symfony version to migrate config to',
            3.2
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

        $successMessage = sprintf(
            'Processed %d file(s) from "%s" to "%s" format',
            count($fileInfos),
            $this->configuration->getInputFormat(),
            $this->configuration->getOutputFormat()
        );

        $this->symfonyStyle->success($successMessage);

        return ShellCode::SUCCESS;
    }
}
