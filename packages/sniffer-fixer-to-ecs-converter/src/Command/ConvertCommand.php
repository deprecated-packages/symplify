<?php

declare(strict_types=1);

namespace Symplify\SnifferFixerToECSConverter\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\PackageBuilder\Exception\NotImplementedYetException;
use Symplify\PackageBuilder\ValueObject\Option;
use Symplify\SmartFileSystem\Exception\FileNotFoundException;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SnifferFixerToECSConverter\FixerToECSConverter;
use Symplify\SnifferFixerToECSConverter\SnifferToECSConverter;
use Symplify\SymplifyKernel\Exception\NotImplementedYetException;
use Symplify\SymplifyKernel\ValueObject\symplifyOption;

final class ConvertCommand extends AbstractSymplifyCommand
{
    /**
     * @var SnifferToECSConverter
     */
    private $snifferToECSConverter;

    /**
     * @var FixerToECSConverter
     */
    private $fixerToECSConverter;

    public function __construct(
        SnifferToECSConverter $snifferToECSConverter,
        FixerToECSConverter $fixerToECSConverter
    ) {
        $this->snifferToECSConverter = $snifferToECSConverter;
        $this->fixerToECSConverter = $fixerToECSConverter;

        parent::__construct();
    }

    protected function configure(): void
    {
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
            InputArgument::REQUIRED,
            'File to convert, usually "phpcs.xml" or ".php_cs.dist"'
        );
        $this->setDescription('Converts PHP_CodeSniffer or PHP-CS-Fixer config to ECS one - ecs.php');
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
        if (! $this->smartFileSystem->exists($source)) {
            throw new FileNotFoundException($source);
        }

        $sourceFileInfo = new SmartFileInfo($source);
        if ($sourceFileInfo->getSuffix() === 'xml') {
            $convertedECSFileContent = $this->snifferToECSConverter->convertFile($sourceFileInfo);
        } elseif (in_array($sourceFileInfo->getSuffix(), ['php_cs', 'dist'], true)) {
            $convertedECSFileContent = $this->fixerToECSConverter->convertFile($sourceFileInfo);
        } else {
            $message = sprintf('File "%s" has not matched any converted.', $source);
            throw new NotImplementedYetException($message);
        }

        $outputFileName = $sourceFileInfo->getPath() . DIRECTORY_SEPARATOR . 'converted-ecs.php';
        $this->smartFileSystem->dumpFile($outputFileName, $convertedECSFileContent);

        $message = sprintf('"%s" was converted into "%s"', $source, $outputFileName);
        $this->symfonyStyle->success($message);

        return ShellCode::SUCCESS;
    }
}
