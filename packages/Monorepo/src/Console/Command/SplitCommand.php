<?php declare(strict_types=1);

namespace Symplify\Monorepo\Console\Command;

use GitWrapper\GitWrapper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\Monorepo\Configuration\ConfigurationGuard;
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

    public function __construct(
        ParameterProvider $parameterProvider,
        ConfigurationGuard $configurationGuard,
        GitWrapper $gitWrapper
    ) {
        $this->parameterProvider = $parameterProvider;
        $this->configurationGuard = $configurationGuard;
        $this->gitWrapper = $gitWrapper;

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
        //        $tags = $gitWorkingCopy->tag('-l', '--sort=committerdate');
        //         LAST_TAG=$(git tag -l  --sort=committerdate | tail -n1);
    }
}
