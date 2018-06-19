<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\ChangelogLinker\ChangelogLinker;
use Symplify\ChangelogLinker\Exception\FileNotFoundException;
use Symplify\PackageBuilder\Console\Command\CommandNaming;

final class RunCommand extends Command
{
    /**
     * @var string
     */
    private const CHANGELOG_FILE_OPTION = 'changelog-file';

    /**
     * @var ChangelogLinker
     */
    private $changelogLinker;

    public function __construct(ChangelogLinker $changelogLinker)
    {
        $this->changelogLinker = $changelogLinker;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->addArgument(self::CHANGELOG_FILE_OPTION, InputArgument::OPTIONAL, 'CHANGELOG.md file', 'CHANGELOG.md');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $changelogFile = $input->getArgument(self::CHANGELOG_FILE_OPTION);
        if (! file_exists($changelogFile)) {
            throw new FileNotFoundException(sprintf('Changelog file "%s" was not found' . PHP_EOL, $changelogFile));
        }

        $processedChangelogFile = $this->changelogLinker->processContent(file_get_contents($changelogFile));

        file_put_contents($changelogFile, $processedChangelogFile);

        // success
        return 0;
    }
}
