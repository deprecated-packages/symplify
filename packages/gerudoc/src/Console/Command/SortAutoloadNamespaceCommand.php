<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ComposerJsonManipulator\ComposerJsonFactory;
use Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SortAutoloadNamespaceCommand extends Command
{
    /**
     * @var ComposerJsonFactory
     */
    private $composerJsonFactory;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var JsonFileManager
     */
    private $jsonFileManager;

    public function __construct(
        ComposerJsonFactory $composerJsonFactory,
        SymfonyStyle $symfonyStyle,
        JsonFileManager $jsonFileManager
    ) {
        $this->composerJsonFactory = $composerJsonFactory;
        $this->symfonyStyle = $symfonyStyle;
        $this->jsonFileManager = $jsonFileManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Sort autoload/autoload-dev namespaces');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $composerJsonPath = getcwd() . '/composer.json';
        $composerJsonFileInfo = new SmartFileInfo($composerJsonPath);

        $composerJson = $this->composerJsonFactory->createFromFileInfo($composerJsonFileInfo);

        // should be default?
        $this->sortAutoloadSections($composerJson);

        $this->jsonFileManager->saveComposerJsonWithFileInfo($composerJson, $composerJsonFileInfo);

        $this->symfonyStyle->success('"autoload"/"autoload-dev" sections were successfully sorted');

        return ShellCode::SUCCESS;
    }

    private function sortAutoloadSections(ComposerJson $composerJson): void
    {
        $sortedAutoload = $this->sortAutoload($composerJson->getAutoload());
        $composerJson->setAutoload($sortedAutoload);

        $sortedAutoloadDev = $this->sortAutoload($composerJson->getAutoloadDev());
        $composerJson->setAutoloadDev($sortedAutoloadDev);
    }

    /**
     * @return mixed[]
     */
    private function sortAutoload(array $autoload): array
    {
        // 2. sort by namespaces
        if (isset($autoload['psr-4'])) {
            ksort($autoload['psr-4']);
        }

        return $autoload;
    }
}
