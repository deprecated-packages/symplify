<?php declare(strict_types=1);

namespace Symplify\Statie\GithubContributorsThanker\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\Statie\FileSystem\GeneratedFilesDumper;
use Symplify\Statie\GithubContributorsThanker\Api\GithubApi;

final class DumpContributorsCommand extends Command
{
    /**
     * @var GithubApi
     */
    private $githubApi;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var GeneratedFilesDumper
     */
    private $generatedFilesDumper;

    public function __construct(
        GithubApi $githubApi,
        SymfonyStyle $symfonyStyle,
        GeneratedFilesDumper $generatedFilesDumper
    ) {
        parent::__construct();
        $this->githubApi = $githubApi;
        $this->symfonyStyle = $symfonyStyle;
        $this->generatedFilesDumper = $generatedFilesDumper;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Dump contributors.yaml file with your Github repository contributors');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $contributors = $this->githubApi->getContributors();
        if (count($contributors) === 0) {
            $this->symfonyStyle->note('Found 0 contributions - stick with the current dump');

            return ShellCode::SUCCESS;
        }

        $this->generatedFilesDumper->dump('contributors', $contributors);

        $this->symfonyStyle->success(sprintf('Dump %d contributors', count($contributors)));

        return ShellCode::SUCCESS;
    }
}
