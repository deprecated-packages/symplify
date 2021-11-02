<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ActiveClass\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCI\ActiveClass\Filtering\PossiblyUnusedClassesFilter;
use Symplify\EasyCI\ActiveClass\Finder\ClassNamesFinder;
use Symplify\EasyCI\ActiveClass\Reporting\UnusedClassReporter;
use Symplify\EasyCI\ActiveClass\UseImportsResolver;
use Symplify\EasyCI\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\SmartFileSystem\Finder\SmartFinder;

final class CheckActiveClassCommand extends Command
{
    public function __construct(
        private SmartFinder $smartFinder,
        private ClassNamesFinder $classNamesFinder,
        private UseImportsResolver $useImportsResolver,
        private PossiblyUnusedClassesFilter $possiblyUnusedClassesFilter,
        private UnusedClassReporter $unusedClassReporter,
        private ParameterProvider $parameterProvider,
        private SymfonyStyle $symfonyStyle
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Check classes that are not used in any config and in the code');

        $this->addArgument(
            Option::SOURCES,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'One or more paths with templates'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $excludedCheckPaths = $this->parameterProvider->provideArrayParameter(Option::EXCLUDED_CHECK_PATHS);

        $sources = (array) $input->getArgument(Option::SOURCES);
        $phpFileInfos = $this->smartFinder->find($sources, '*.php', $excludedCheckPaths);

        $phpFilesCount = count($phpFileInfos);
        $this->symfonyStyle->progressStart($phpFilesCount);

        $usedNames = $this->useImportsResolver->resolveFromFileInfos($phpFileInfos);

        $existingFilesWithClasses = $this->classNamesFinder->resolveClassNamesToCheck($phpFileInfos);
        $possiblyUnusedFilesWithClasses = $this->possiblyUnusedClassesFilter->filter(
            $existingFilesWithClasses,
            $usedNames
        );

        return $this->unusedClassReporter->reportResult($possiblyUnusedFilesWithClasses, $existingFilesWithClasses);
    }
}
