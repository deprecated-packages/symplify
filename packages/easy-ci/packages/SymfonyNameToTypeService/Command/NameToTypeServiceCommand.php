<?php

declare(strict_types=1);

namespace Symplify\EasyCI\SymfonyNameToTypeService\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCI\SymfonyNameToTypeService\AmbiguousServiceFilter;
use Symplify\EasyCI\SymfonyNameToTypeService\Finder\YamlConfigFinder;
use Symplify\EasyCI\SymfonyNameToTypeService\NameToTypeServiceReplacer;
use Symplify\EasyCI\SymfonyNameToTypeService\Option;
use Symplify\EasyCI\SymfonyNameToTypeService\XmlServiceMapFactory;

final class NameToTypeServiceCommand extends Command
{
    public function __construct(
        private SymfonyStyle $symfonyStyle,
        private XmlServiceMapFactory $xmlServiceMapFactory,
        private AmbiguousServiceFilter $ambiguousServiceFilter,
        private YamlConfigFinder $yamlConfigFinder,
        private NameToTypeServiceReplacer $nameToTypeServiceReplacer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('name-to-type-service');
        $this->setDescription(
            'Replaces string names in Symfony 2.8 configs with typed-based names. This allows to get() by type from container'
        );

        $this->addArgument(
            Option::SOURCES,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Path to directory/file with configs'
        );
        $this->addOption(Option::XML_CONTAINER, null, InputOption::VALUE_REQUIRED, 'Path to dumped XML container');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // use like: "php utils/name-to-type-service/bin/NameToTypeServiceCommand.php config"
        $configDirectory = $input->getArgument(Option::SOURCES);
        $xmlContainerFilePath = $input->getArgument(Option::XML_CONTAINER);

        // 1. get service map from xml dump
        $serviceTypesByName = $this->xmlServiceMapFactory->create($xmlContainerFilePath);

        $serviceTypesByName = $this->ambiguousServiceFilter->filter($serviceTypesByName);

        // 2. find yml configs
        $yamlFileInfos = $this->yamlConfigFinder->findInDirectory($configDirectory);

        // 3. replace names in config services by types
        $changedFilesCount = $this->nameToTypeServiceReplacer->replaceInFileInfos($yamlFileInfos, $serviceTypesByName);

        $successMessage = sprintf('Updated %d config files', $changedFilesCount);
        $this->symfonyStyle->success($successMessage);

        return self::SUCCESS;
    }
}
