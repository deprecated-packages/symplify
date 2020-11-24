<?php

declare(strict_types=1);

namespace Symplify\ClassPresence\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
<<<<<<< HEAD
<<<<<<< HEAD
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\Finder\SmartFinder;
=======
use Symplify\ClassPresence\Configuration\Suffixes;
use Symplify\ClassPresence\Regex\NonExistingClassConstantExtractor;
use Symplify\ClassPresence\Regex\NonExistingClassExtractor;
use Symplify\ClassPresence\Reporter\NonExistingElementsReporter;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\PackageBuilder\ValueObject\Option;
use Symplify\SmartFileSystem\Finder\SmartFinder;
<<<<<<< HEAD
use Symplify\SymplifyKernel\ValueObject\symplifyOption;
>>>>>>> 91a7cf6c2... fixup! misc
=======
>>>>>>> 7e1cbd8ad... fixup! fixup! misc
=======
use Symplify\ClassPresence\Configuration\Suffixes;
use Symplify\ClassPresence\Regex\NonExistingClassConstantExtractor;
use Symplify\ClassPresence\Regex\NonExistingClassExtractor;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\Finder\SmartFinder;
<<<<<<< HEAD
use Symplify\symplifyKernel\Command\AbstractsymplifyCommand;
use Symplify\symplifyKernel\ValueObject\symplifyOption;
>>>>>>> 434bcd4b3... rename Migrify to Symplify
=======
use Symplify\SymplifyKernel\Command\AbstractSymplifyCommand;
use Symplify\SymplifyKernel\ValueObject\symplifyOption;
>>>>>>> 1a08239af... misc

final class CheckCommand extends AbstractSymplifyCommand
{
    /**
     * @var NonExistingClassExtractor
     */
    private $nonExistingClassExtractor;

    /**
     * @var NonExistingClassConstantExtractor
     */
    private $nonExistingClassConstantExtractor;

    /**
     * @var Suffixes
     */
    private $suffixes;

    /**
     * @var NonExistingElementsReporter
     */
    private $nonExistingElementsReporter;

    public function __construct(
        SmartFinder $smartFinder,
        NonExistingClassExtractor $nonExistingClassExtractor,
        NonExistingClassConstantExtractor $nonExistingClassConstantExtractor,
        Suffixes $suffixes,
        NonExistingElementsReporter $nonExistingElementsReporter
    ) {
        $this->smartFinder = $smartFinder;
        $this->nonExistingClassExtractor = $nonExistingClassExtractor;
        $this->nonExistingClassConstantExtractor = $nonExistingClassConstantExtractor;
        $this->suffixes = $suffixes;

        parent::__construct();

        $this->nonExistingElementsReporter = $nonExistingElementsReporter;
    }

    protected function configure(): void
    {
        $this->setDescription('Check configs and template for existing classes and class constants');
        $this->addArgument(
<<<<<<< HEAD
<<<<<<< HEAD
            MigrifyOption::SOURCES,
=======
            Option::SOURCES,
>>>>>>> 7e1cbd8ad... fixup! fixup! misc
=======
            symplifyOption::SOURCES,
>>>>>>> 434bcd4b3... rename Migrify to Symplify
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Path to directories or files to check'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string[] $sources */
<<<<<<< HEAD
<<<<<<< HEAD
        $sources = (array) $input->getArgument(MigrifyOption::SOURCES);
=======
        $sources = (array) $input->getArgument(Option::SOURCES);
>>>>>>> 7e1cbd8ad... fixup! fixup! misc
=======
        $sources = (array) $input->getArgument(symplifyOption::SOURCES);
>>>>>>> 434bcd4b3... rename Migrify to Symplify
        $fileInfos = $this->smartFinder->find($sources, $this->suffixes->provideRegex());

        $message = sprintf(
            'Checking %d files with "%s" suffixes',
            count($fileInfos),
            implode('", "', $this->suffixes->provide())
        );
        $this->symfonyStyle->note($message);

        $nonExistingClassesByFile = $this->nonExistingClassExtractor->extractFromFileInfos($fileInfos);
        $nonExistingClassConstantsByFile = $this->nonExistingClassConstantExtractor->extractFromFileInfos($fileInfos);

        if ($nonExistingClassConstantsByFile === [] && $nonExistingClassesByFile === []) {
            $this->symfonyStyle->success('All classes and class constants exists');
            return ShellCode::SUCCESS;
        }

        $this->nonExistingElementsReporter->reportNonExistingElements(
            $nonExistingClassesByFile,
            $nonExistingClassConstantsByFile
        );

        return ShellCode::ERROR;
    }
}
