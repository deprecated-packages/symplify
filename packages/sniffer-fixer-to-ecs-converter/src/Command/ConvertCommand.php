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
            Option::SOURCES,
            InputArgument::REQUIRED,
            'File to convert, usually "phpcs.xml" or ".php_cs.dist"'
        );
        $this->setDescription('Converts PHP_CodeSniffer or PHP-CS-Fixer config to ECS one - ecs.php');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = (string) $input->getArgument(Option::SOURCES);
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

        $outputFileName = $sourceFileInfo->getRealPathDirectory() . DIRECTORY_SEPARATOR . 'converted-ecs.php';
        $this->smartFileSystem->dumpFile($outputFileName, $convertedECSFileContent);

        $outputFileInfo = new SmartFileInfo($outputFileName);

        $message = sprintf('"%s" was converted into "%s"', $source, $outputFileInfo->getRelativeFilePathFromCwd());
        $this->symfonyStyle->success($message);

        return ShellCode::SUCCESS;
    }
}
