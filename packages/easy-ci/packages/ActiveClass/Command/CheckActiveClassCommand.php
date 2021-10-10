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
use Symplify\EasyCI\ActiveClass\UseImportsResolver;
use Symplify\EasyCI\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\SmartFileSystem\Finder\SmartFinder;

final class CheckActiveClassCommand extends Command
{
    public function __construct(
        private SmartFinder $smartFinder,
        private SymfonyStyle $symfonyStyle,
        private ClassNamesFinder $classNamesFinder,
        private UseImportsResolver $useImportsResolver,
        private PossiblyUnusedClassesFilter $possiblyUnusedClassesFilter
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
        $sources = (array) $input->getArgument(Option::SOURCES);

        $phpFileInfos = $this->smartFinder->find($sources, '*.php', ['Fixture', 'Source', 'tests', 'stubs']);
        $classUses = $this->useImportsResolver->resolveFromFileInfos($phpFileInfos);

        // @todo also find classes from the same namespace?

        $classNames = $this->classNamesFinder->resolveClassNamesToCheck($phpFileInfos);

        $possiblyUnusedClasses = $this->possiblyUnusedClassesFilter->filter($classNames, $classUses);

        if ($possiblyUnusedClasses === []) {
            $errorMessage = sprintf(
                'All the %d services from %d files are used. Great job!',
                count($classNames),
                count($phpFileInfos)
            );
            $this->symfonyStyle->success($errorMessage);
            return self::SUCCESS;
        }

        $this->symfonyStyle->listing($possiblyUnusedClasses);

        $errorMessage = sprintf(
            'Found %d unused classes. Check them, remove them or correct the command.',
            count($possiblyUnusedClasses)
        );

        $this->symfonyStyle->error($errorMessage);

        return self::FAILURE;
    }
}
