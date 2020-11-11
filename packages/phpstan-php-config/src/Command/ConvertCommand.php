<?php

declare(strict_types=1);

namespace Symplify\PHPStanPHPConfig\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\PHPStanPHPConfig\Neon\NeonFilePrinter;
use Symplify\PHPStanPHPConfig\PHPStanPHPToNeonConverter;
use Symplify\PHPStanPHPConfig\ValueObject\Option;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ConvertCommand extends AbstractSymplifyCommand
{
    /**
     * @var FileSystemGuard
     */
    private $fileSystemGuard;

    /**
     * @var PHPStanPHPToNeonConverter
     */
    private $phpStanPHPToNeonConverter;

    /**
     * @var NeonFilePrinter
     */
    private $neonFilePrinter;

    public function __construct(
        FileSystemGuard $fileSystemGuard,
        PHPStanPHPToNeonConverter $phpStanPHPToNeonConverter,
        NeonFilePrinter $neonFilePrinter
    ) {
        $this->fileSystemGuard = $fileSystemGuard;
        $this->phpStanPHPToNeonConverter = $phpStanPHPToNeonConverter;

        parent::__construct();

        $this->neonFilePrinter = $neonFilePrinter;
    }

    protected function configure(): void
    {
        $this->setDescription('Converts phpstan.php to phpstan.neon');
        $this->addArgument(Option::PATH, InputArgument::REQUIRED, 'Path to phpstan.php');
        $this->addOption(
            Option::OUTPUT_FILE,
            null,
            InputOption::VALUE_REQUIRED,
            'Path to dump converted phpstan.neon to',
            getcwd() . DIRECTORY_SEPARATOR . 'phpstan.neon'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $phpstanPhpFilePath = (string) $input->getArgument(Option::PATH);
        $this->fileSystemGuard->ensureFileExists($phpstanPhpFilePath, __METHOD__);

        $phpConfigFileInfo = new SmartFileInfo($phpstanPhpFilePath);
        $neonFileContent = $this->phpStanPHPToNeonConverter->convert($phpConfigFileInfo);

        $outputFilePath = (string) $input->getOption(Option::OUTPUT_FILE);
        $this->neonFilePrinter->printContentToOutputFile($neonFileContent, $outputFilePath);

        return ShellCode::SUCCESS;
    }
}
