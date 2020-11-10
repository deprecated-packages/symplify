<?php

declare(strict_types=1);

namespace Symplify\PHPStanPHPConfig\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\PHPStanPHPConfig\PHPStanPHPToNeonConverter;
use Symplify\PHPStanPHPConfig\ValueObject\Option;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class ConvertCommand extends Command
{
    /**
     * @var FileSystemGuard
     */
    private $fileSystemGuard;

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var PHPStanPHPToNeonConverter
     */
    private $phpStanPHPToNeonConverter;

    public function __construct(
        FileSystemGuard $fileSystemGuard,
        SmartFileSystem $smartFileSystem,
        SymfonyStyle $symfonyStyle,
        PHPStanPHPToNeonConverter $phpStanPHPToNeonConverter
    ) {
        $this->fileSystemGuard = $fileSystemGuard;
        $this->smartFileSystem = $smartFileSystem;
        $this->symfonyStyle = $symfonyStyle;
        $this->phpStanPHPToNeonConverter = $phpStanPHPToNeonConverter;

        parent::__construct();
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
        $this->printNeonToOutputFile($neonFileContent, $outputFilePath);

        return ShellCode::SUCCESS;
    }

    private function printNeonToOutputFile(string $neonFileContent, string $outputFilePath): void
    {
        $this->smartFileSystem->dumpFile($outputFilePath, $neonFileContent);

        $outputFileInfo = new SmartFileInfo($outputFilePath);

        $message = sprintf('The neon file was converted to "%s"', $outputFileInfo->getRelativeFilePathFromCwd());
        $this->symfonyStyle->success($message);

        $this->symfonyStyle->writeln('===================================');
        $this->symfonyStyle->newLine(1);
        $this->symfonyStyle->writeln('<comment>' . $neonFileContent . '</comment>');
        $this->symfonyStyle->writeln('===================================');
        $this->symfonyStyle->newLine(1);
    }
}
