<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\ChangelogLinker\ChangelogLinker;
use Symplify\ChangelogLinker\Configuration\Option;
use Symplify\ChangelogLinker\FileSystem\ChangelogFileSystem;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

final class LinkifyCommand extends Command
{
    /**
     * @var ChangelogLinker
     */
    private $changelogLinker;

    /**
     * @var ChangelogFileSystem
     */
    private $changelogFileSystem;

    /**
     * @var ParameterProvider
     */
    private $parameterProvider;

    public function __construct(
        ChangelogLinker $changelogLinker,
        ChangelogFileSystem $changelogFileSystem,
        ParameterProvider $parameterProvider
    ) {
        parent::__construct();
        $this->changelogLinker = $changelogLinker;
        $this->changelogFileSystem = $changelogFileSystem;
        $this->parameterProvider = $parameterProvider;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->parameterProvider->changeParameter(Option::FILE, $input->getArgument(Option::FILE));

        $changelogContent = $this->changelogFileSystem->readChangelog();

        $processedChangelogContent = $this->changelogLinker->processContent($changelogContent);

        $this->changelogFileSystem->storeChangelog($processedChangelogContent);

        // success
        return 0;
    }
}
