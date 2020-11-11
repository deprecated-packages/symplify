<?php

declare(strict_types=1);

namespace Symplify\PackageScoper\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\ComposerJsonManipulator\ComposerJsonFactory;
use Symplify\ComposerJsonManipulator\Printer\ComposerJsonPrinter;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\PackageScoper\ComposerJson\ScopedComposerJsonFactory;
use Symplify\PackageScoper\ValueObject\Option;
use Symplify\SmartFileSystem\FileSystemGuard;

final class ScopeComposerJsonCommand extends AbstractSymplifyCommand
{
    /**
     * @var FileSystemGuard
     */
    private $fileSystemGuard;

    /**
     * @var ComposerJsonFactory
     */
    private $composerJsonFactory;

    /**
     * @var ScopedComposerJsonFactory
     */
    private $scopedComposerJsonFactory;

    /**
     * @var ComposerJsonPrinter
     */
    private $composerJsonPrinter;

    public function __construct(
        FileSystemGuard $fileSystemGuard,
        ComposerJsonFactory $composerJsonFactory,
        ScopedComposerJsonFactory $scopedComposerJsonFactory,
        ComposerJsonPrinter $composerJsonPrinter
    ) {
        $this->fileSystemGuard = $fileSystemGuard;
        $this->composerJsonFactory = $composerJsonFactory;
        $this->scopedComposerJsonFactory = $scopedComposerJsonFactory;
        $this->composerJsonPrinter = $composerJsonPrinter;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Simplify "composer.json" of scoped package to bare minimum');
        $this->addArgument(Option::PATH, InputArgument::REQUIRED, 'Path to composer.json to simplify');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = (string) $input->getArgument(Option::PATH);
        $this->fileSystemGuard->ensureFileExists($path, __METHOD__);

        $packageComposerJson = $this->composerJsonFactory->createFromFilePath($path);
        $scopedPackageComposerJson = $this->scopedComposerJsonFactory->createFromPackageComposerJson(
            $packageComposerJson
        );

        $scopedPackageComposerJsonFileContent = $this->composerJsonPrinter->print($scopedPackageComposerJson, $path);

        $this->symfonyStyle->title('Changing to scoped composer.json');
        $this->symfonyStyle->writeln('----------------------------------------------------');
        $this->symfonyStyle->newLine();
        $this->symfonyStyle->writeln($scopedPackageComposerJsonFileContent);
        $this->symfonyStyle->writeln('----------------------------------------------------');

        return ShellCode::SUCCESS;
    }
}
