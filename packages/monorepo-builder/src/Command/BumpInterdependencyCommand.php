<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\MonorepoBuilder\DependencyUpdater;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Validator\SourcesPresenceValidator;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

final class BumpInterdependencyCommand extends AbstractSymplifyCommand
{
    /**
     * @var string
     */
    private const VERSION_ARGUMENT = 'version';

    /**
     * @var DependencyUpdater
     */
    private $dependencyUpdater;

    /**
     * @var ComposerJsonProvider
     */
    private $composerJsonProvider;

    /**
     * @var SourcesPresenceValidator
     */
    private $sourcesPresenceValidator;

    public function __construct(
        DependencyUpdater $dependencyUpdater,
        ComposerJsonProvider $composerJsonProvider,
        SourcesPresenceValidator $sourcesPresenceValidator
    ) {
        parent::__construct();

        $this->dependencyUpdater = $dependencyUpdater;
        $this->composerJsonProvider = $composerJsonProvider;
        $this->sourcesPresenceValidator = $sourcesPresenceValidator;
    }

    protected function configure(): void
    {
        $this->setDescription('Bump dependency of split packages on each other');
        $this->addArgument(
            self::VERSION_ARGUMENT,
            InputArgument::REQUIRED,
            'New version of inter-dependencies, e.g. "^4.4.2"'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->sourcesPresenceValidator->validateRootComposerJsonName();

        /** @var string $version */
        $version = $input->getArgument(self::VERSION_ARGUMENT);

        $rootComposerJson = $this->composerJsonProvider->getRootComposerJson();

        // @todo resolve better for only found packages
        // see https://github.com/symplify/symplify/pull/1037/files
        $vendorName = $rootComposerJson->getVendorName();
        if ($vendorName === null) {
            throw new ShouldNotHappenException();
        }

        $this->dependencyUpdater->updateFileInfosWithVendorAndVersion(
            $this->composerJsonProvider->getPackagesComposerFileInfos(),
            $vendorName,
            $version
        );

        $successMessage = sprintf('Inter-dependencies of packages were updated to "%s".', $version);
        $this->symfonyStyle->success($successMessage);

        return ShellCode::SUCCESS;
    }
}
