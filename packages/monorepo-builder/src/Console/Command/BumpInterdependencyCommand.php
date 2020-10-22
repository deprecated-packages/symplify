<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\DependencyUpdater;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

final class BumpInterdependencyCommand extends Command
{
    /**
     * @var string
     */
    private const VERSION_ARGUMENT = 'version';

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var DependencyUpdater
     */
    private $dependencyUpdater;

    /**
     * @var ComposerJsonProvider
     */
    private $composerJsonProvider;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        DependencyUpdater $dependencyUpdater,
        ComposerJsonProvider $composerJsonProvider
    ) {
        parent::__construct();

        $this->symfonyStyle = $symfonyStyle;
        $this->dependencyUpdater = $dependencyUpdater;
        $this->composerJsonProvider = $composerJsonProvider;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Bump dependency of split packages on each other');
        $this->addArgument(
            self::VERSION_ARGUMENT,
            InputArgument::REQUIRED,
            'New version of inter-dependencies, e.g. "^4.4.2"'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $version */
        $version = $input->getArgument(self::VERSION_ARGUMENT);

        $mainComposerJson = $this->composerJsonProvider->getRootJson();

        // @todo resolve better for only found packages
        // see https://github.com/symplify/symplify/pull/1037/files
        [$vendor] = explode('/', $mainComposerJson['name']);

        $this->dependencyUpdater->updateFileInfosWithVendorAndVersion(
            $this->composerJsonProvider->getPackagesComposerFileInfos(),
            $vendor,
            $version
        );

        $successMessage = sprintf('Inter-dependencies of packages were updated to "%s".', $version);
        $this->symfonyStyle->success($successMessage);

        return ShellCode::SUCCESS;
    }
}
