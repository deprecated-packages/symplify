<?php

declare(strict_types=1);

namespace Symplify\PHPMDDecomposer\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\PackageBuilder\ValueObject\Option;
use Symplify\PHPMDDecomposer\PHPMDDecomposer;
use Symplify\PHPMDDecomposer\PHPStan\PHPStanConfigPrinter;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

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
        $this->addArgument(Option::SOURCES, InputArgument::REQUIRED, 'File path to phpmd.xml to convert');
        $this->setDescription('Converts phpmd.xml to ecs.php, phpstan.neon and rector.php');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = (string) $input->getArgument(Option::SOURCES);
        $this->fileSystemGuard->ensureFileExists($source, __METHOD__);

        $phpmdXmlFileInfo = new SmartFileInfo($source);
        if ($phpmdXmlFileInfo->getSuffix() !== 'xml') {
            throw new ShouldNotHappenException();
        }

        $decomposedFileConfigs = $this->phpmdDecomposer->decompose($phpmdXmlFileInfo);

        $this->phpStanConfigPrinter->printPHPStanConfig($decomposedFileConfigs, $phpmdXmlFileInfo);

        $this->symfonyStyle->success('Done');

        return ShellCode::SUCCESS;
    }
}
