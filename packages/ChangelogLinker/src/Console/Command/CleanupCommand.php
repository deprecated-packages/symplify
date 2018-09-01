<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ChangelogLinker\ChangelogCleaner;
use Symplify\ChangelogLinker\Configuration\Option;
use Symplify\ChangelogLinker\FileSystem\ChangelogFileSystem;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

final class CleanupCommand extends Command
{
    /**
     * @var ChangelogFileSystem
     */
    private $changelogFileSystem;

    /**
     * @var ChangelogCleaner
     */
    private $changelogCleaner;

    /**
     * @var ParameterProvider
     */
    private $parameterProvider;
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(
        ChangelogFileSystem $changelogFileSystem,
        ChangelogCleaner $changelogCleaner,
        ParameterProvider $parameterProvider,
       SymfonyStyle $symfonyStyle
    ) {
        parent::__construct();
        $this->changelogFileSystem = $changelogFileSystem;
        $this->changelogCleaner = $changelogCleaner;
        $this->parameterProvider = $parameterProvider;
        $this->symfonyStyle = $symfonyStyle;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Removes dead links from CHANGELOG.md');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument(Option::FILE);
        $this->parameterProvider->changeParameter(Option::FILE, $file);

        $changelogContent = $this->changelogFileSystem->readChangelog();

        $processedChangelogContent = $this->changelogCleaner->processContent($changelogContent);

        $this->changelogFileSystem->storeChangelog($processedChangelogContent);

        $this->symfonyStyle->success(sprintf('File "%s" is now clean from duplicates!', $file));

        // success
        return 0;
    }
}
