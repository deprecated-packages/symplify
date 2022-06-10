<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\ConfigTransformer\Configuration\ConfigurationFactory;
use Symplify\ConfigTransformer\Converter\ConvertedContentFactory;
use Symplify\ConfigTransformer\FileSystem\ConfigFileDumper;
use Symplify\ConfigTransformer\ValueObject\Configuration;
use Symplify\ConfigTransformer\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SwitchFormatCommand extends AbstractSymplifyCommand
{
    public function __construct(
        private ConfigurationFactory $configurationFactory,
        private ConfigFileDumper $configFileDumper,
        private ConvertedContentFactory $convertedContentFactory
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('switch-format');

        $this->setDescription('Converts XML/YAML configs to PHP format');

        $this->addArgument(
            Option::SOURCES,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Path to directory with configs'
        );

        $this->addOption(Option::DRY_RUN, null, InputOption::VALUE_NONE, 'Dry run - no removal or config change');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configuration = $this->configurationFactory->createFromInput($input);

        $suffixes = $configuration->getInputSuffixes();
        $suffixesRegex = '#\.' . implode('|', $suffixes) . '$#';
        $fileInfos = $this->smartFinder->find($configuration->getSources(), $suffixesRegex);

        $convertedContents = $this->convertedContentFactory->createFromFileInfos($fileInfos);

        foreach ($convertedContents as $convertedContent) {
            $this->configFileDumper->dumpFile($convertedContent, $configuration);
        }

        $this->removeFileInfos($configuration, $fileInfos);

        $successMessage = sprintf('Processed %d file(s) to "PHP" format', count($fileInfos));
        $this->symfonyStyle->success($successMessage);

        return self::SUCCESS;
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     */
    private function removeFileInfos(Configuration $configuration, array $fileInfos): void
    {
        if (! $configuration->isDryRun()) {
            $this->smartFileSystem->remove($fileInfos);

            foreach ($fileInfos as $fileInfo) {
                $message = sprintf('File "%s" was be removed', $fileInfo->getRelativeFilePath());
                $this->symfonyStyle->note($message);
            }
        } else {
            foreach ($fileInfos as $fileInfo) {
                $message = sprintf('[dry-run] File "%s" would be removed', $fileInfo->getRelativeFilePath());
                $this->symfonyStyle->note($message);
            }
        }
    }
}
