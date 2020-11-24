<?php

declare(strict_types=1);

namespace Symplify\PHPMDDecomposer\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;
<<<<<<< HEAD
<<<<<<< HEAD
=======
use Symplify\PackageBuilder\ValueObject\Option;
use Symplify\PHPMDDecomposer\PHPMDDecomposer;
use Symplify\PHPMDDecomposer\PHPStan\PHPStanConfigPrinter;
>>>>>>> 7e1cbd8ad... fixup! fixup! misc
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\SmartFileInfo;
<<<<<<< HEAD
=======
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
<<<<<<< HEAD
use Symplify\SymplifyKernel\ValueObject\symplifyOption;
>>>>>>> 91a7cf6c2... fixup! misc
=======
>>>>>>> 7e1cbd8ad... fixup! fixup! misc
=======
use Symplify\PHPMDDecomposer\PHPMDDecomposer;
use Symplify\PHPMDDecomposer\Printer\PHPStanPrinter;
use Symplify\PHPMDDecomposer\ValueObject\DecomposedFileConfigs;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\SmartFileInfo;
<<<<<<< HEAD
use Symplify\symplifyKernel\Command\AbstractsymplifyCommand;
use Symplify\symplifyKernel\Exception\ShouldNotHappenException;
use Symplify\symplifyKernel\ValueObject\symplifyOption;
>>>>>>> 434bcd4b3... rename Migrify to Symplify
=======
use Symplify\SymplifyKernel\Command\AbstractSymplifyCommand;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
use Symplify\SymplifyKernel\ValueObject\symplifyOption;
>>>>>>> 1a08239af... misc

final class DecomposeCommand extends AbstractSymplifyCommand
{
    /**
     * @var PHPMDDecomposer
     */
    private $phpmdDecomposer;

    /**
     * @var PHPStanConfigPrinter
     */
    private $phpStanConfigPrinter;

    public function __construct(
        FileSystemGuard $fileSystemGuard,
        PHPMDDecomposer $phpmdDecomposer,
        PHPStanConfigPrinter $phpStanConfigPrinter
    ) {
        $this->fileSystemGuard = $fileSystemGuard;
        $this->phpmdDecomposer = $phpmdDecomposer;
        $this->phpStanConfigPrinter = $phpStanConfigPrinter;

        parent::__construct();
    }

    protected function configure(): void
    {
<<<<<<< HEAD
<<<<<<< HEAD
        $this->addArgument(MigrifyOption::SOURCES, InputArgument::REQUIRED, 'File path to phpmd.xml to convert');
=======
        $this->addArgument(symplifyOption::SOURCES, InputArgument::REQUIRED, 'File path to phpmd.xml to convert');
>>>>>>> 434bcd4b3... rename Migrify to Symplify
        $this->setDescription('Converts phpmd.xml to phpstan.neon, ecs.php and rector.php');
=======
        $this->addArgument(Option::SOURCES, InputArgument::REQUIRED, 'File path to phpmd.xml to convert');
        $this->setDescription('Converts phpmd.xml to ecs.php, phpstan.neon and rector.php');
>>>>>>> 7e1cbd8ad... fixup! fixup! misc
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
<<<<<<< HEAD
<<<<<<< HEAD
        $source = (string) $input->getArgument(MigrifyOption::SOURCES);
=======
        $source = (string) $input->getArgument(Option::SOURCES);
>>>>>>> 7e1cbd8ad... fixup! fixup! misc
=======
        $source = (string) $input->getArgument(symplifyOption::SOURCES);
>>>>>>> 434bcd4b3... rename Migrify to Symplify
        $this->fileSystemGuard->ensureFileExists($source, __METHOD__);

        $phpmdXmlFileInfo = new SmartFileInfo($source);
        if ($phpmdXmlFileInfo->getSuffix() !== 'xml') {
            throw new ShouldNotHappenException();
        }

        $decomposedFileConfigs = $this->phpmdDecomposer->decompose($phpmdXmlFileInfo);

        // @todo for all files
        $this->phpStanConfigPrinter->printPHPStanConfig($decomposedFileConfigs, $phpmdXmlFileInfo);

        $this->symfonyStyle->success('Done');

        return ShellCode::SUCCESS;
    }
}
