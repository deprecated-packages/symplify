<?php declare(strict_types=1);

namespace Symplify\Monorepo\Console\Command;

use GitWrapper\GitWorkingCopy;
use GitWrapper\GitWrapper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\Monorepo\Configuration\ConfigurationGuard;
use Symplify\Monorepo\Filesystem\Filesystem;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

final class SplitCommand extends Command
{
    /**
     * @var ParameterProvider
     */
    private $parameterProvider;

    /**
     * @var ConfigurationGuard
     */
    private $configurationGuard;

    /**
     * @var GitWrapper
     */
    private $gitWrapper;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(
        ParameterProvider $parameterProvider,
        ConfigurationGuard $configurationGuard,
        GitWrapper $gitWrapper,
        SymfonyStyle $symfonyStyle,
        Filesystem $filesystem
    ) {
        $this->parameterProvider = $parameterProvider;
        $this->configurationGuard = $configurationGuard;
        $this->gitWrapper = $gitWrapper;
        $this->symfonyStyle = $symfonyStyle;
        $this->filesystem = $filesystem;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Split monolithic repository from provided config to many repositories.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $splitConfig = $this->parameterProvider->provideParameter('split');
        $this->configurationGuard->ensureConfigSectionIsFilled($splitConfig, 'split');

        // git subsplit init .git
        $gitWorkingCopy = $this->gitWrapper->workingCopy(getcwd());

        // @todo check exception if subsplit alias not installed
        $gitWorkingCopy->run('subsplit', ['init', '.git']);
        $this->symfonyStyle->success(sprintf(
            'Directory "%s" with local clone created',
            $this->getSubsplitDirectory()
        ));

        $this->splitDirectoriesToRepositories($gitWorkingCopy, $splitConfig);

        $this->filesystem->deleteDirectory($this->getSubsplitDirectory());
        $this->symfonyStyle->success(sprintf('Directory "%s" cleaned', $this->getSubsplitDirectory()));
    }

    private function getSubsplitDirectory(): string
    {
        return getcwd() . '/.subsplit';
    }

    private function getMostRecentTag(GitWorkingCopy $gitWorkingCopy): string
    {
        $tags = $gitWorkingCopy->tag('-l');
        $tagList = explode(PHP_EOL, trim($tags));

        return (string) array_pop($tagList);
    }

    /**
     * @todo own service
     * @param mixed[] $splitConfig
     */
    private function splitDirectoriesToRepositories(GitWorkingCopy $gitWorkingCopy, array $splitConfig): void
    {
        $theMostRecentTag = $this->getMostRecentTag($gitWorkingCopy);

        foreach ($splitConfig as $localSubdirectory => $remoteRepository) {
            $this->splitLocalSubdirectoryToGitRepositoryWithTag(
                $localSubdirectory,
                $remoteRepository,
                $theMostRecentTag
            );
        }
    }

    private function splitLocalSubdirectoryToGitRepositoryWithTag(
        string $localSubdirectory,
        string $remoteGitRepository,
        ?string $theMostRecentTag = null
    ): void {
        // ...
    }
}
