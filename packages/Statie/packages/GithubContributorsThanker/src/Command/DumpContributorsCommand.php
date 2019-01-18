<?php declare(strict_types=1);

namespace Symplify\Statie\GithubContributorsThanker\Command;

use Nette\Utils\DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\Statie\Configuration\StatieConfiguration;
use Symplify\Statie\GithubContributorsThanker\Api\GithubApi;
use function Safe\sprintf;

final class DumpContributorsCommand extends Command
{
    /**
     * @var GithubApi
     */
    private $githubApi;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var StatieConfiguration
     */
    private $statieConfiguration;

    public function __construct(
        GithubApi $githubApi,
        Filesystem $filesystem,
        SymfonyStyle $symfonyStyle,
        StatieConfiguration $statieConfiguration
    ) {
        parent::__construct();
        $this->githubApi = $githubApi;
        $this->filesystem = $filesystem;
        $this->symfonyStyle = $symfonyStyle;
        $this->statieConfiguration = $statieConfiguration;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Generate list of Github repository contributors');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $contributors = $this->githubApi->getContributors();

        $data['parameters']['contributors'] = $contributors;

        $yamlDump = Yaml::dump($data, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);

        $timestampComment = sprintf(
            '# this file was generated on %s, do not edit it manually' . PHP_EOL,
            (new DateTime())->format('Y-m-d H:i:s')
        );

        $dumpFilePath = $this->statieConfiguration->getSourceDirectory() . '/_data/contributors.yml';

        if (count($contributors) === 0) {
            $this->symfonyStyle->note('Found 0 contributions - stick with the current dump');

            return ShellCode::SUCCESS;
        }

        $this->filesystem->dumpFile($dumpFilePath, $timestampComment . $yamlDump);

        $this->symfonyStyle->success(sprintf('Dump %d contributors', count($contributors)));

        return ShellCode::SUCCESS;
    }
}
