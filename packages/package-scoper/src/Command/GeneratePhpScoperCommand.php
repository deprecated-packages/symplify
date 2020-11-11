<?php

declare(strict_types=1);

namespace Symplify\PackageScoper\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\PackageScoper\ValueObject\Option;
use Symplify\SmartFileSystem\SmartFileInfo;

final class GeneratePhpScoperCommand extends AbstractSymplifyCommand
{
    protected function configure(): void
    {
        $this->setDescription('Generate php-scoper.php.inc config for scoping packages');
        $this->addArgument(
            Option::PATH,
            InputArgument::REQUIRED,
            'Path to package directory, e.g. packages/easy-coding-standard'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $packageDirectory = (string) $input->getArgument(Option::PATH);
        $phpScoperFilePath = $packageDirectory . DIRECTORY_SEPARATOR . 'scoper.inc.php';

        if (file_exists($phpScoperFilePath)) {
            $phpScoperFileInfo = new SmartFileInfo($phpScoperFilePath);
            $question = sprintf(
                'The "%s" file already exists. Should we override it?',
                $phpScoperFileInfo->getRelativeFilePathFromCwd()
            );
            if (! $this->symfonyStyle->confirm($question)) {
                $this->symfonyStyle->note('Nothing changed');
                return ShellCode::SUCCESS;
            }
        }

        $this->smartFileSystem->copy(__DIR__ . '/../../templates/scoper.inc.php', $phpScoperFilePath);

        $phpScoperFileInfo = new SmartFileInfo($phpScoperFilePath);
        $message = sprintf('File "%s" was crated', $phpScoperFileInfo->getRelativeFilePathFromCwd());
        $this->symfonyStyle->success($message);

        return ShellCode::SUCCESS;
    }
}
