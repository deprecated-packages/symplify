<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Init\Command;

use Composer\Composer;
use Nette\Utils\FileSystem as NetteFileSystem;
use PharIo\Version\Version;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

final class InitCommand extends Command
{
    /**
     * @var string
     */
    private const OUTPUT = 'output';

    /**
     * @var Composer
     */
    private $composer;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(Filesystem $filesystem, SymfonyStyle $symfonyStyle, Composer $composer)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
        $this->symfonyStyle = $symfonyStyle;
        $this->composer = $composer;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Creates empty monorepo directory and composer.json structure.');
        $this->addArgument(self::OUTPUT, InputArgument::OPTIONAL, 'Directory to generate monorepo into.', getcwd());
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $output */
        $output = $input->getArgument(self::OUTPUT);

        $this->filesystem->mirror(__DIR__ . '/../../templates/monorepo', $output);

        // Replace MonorepoBuilder version in monorepo-builder.yml
        $filename = sprintf('%s/monorepo-builder.yml', $output);
        $content = str_replace('<version>', $this->getMonorepoBuilderVersion(), NetteFileSystem::read($filename));

        $this->filesystem->dumpFile($filename, $content);

        $this->symfonyStyle->success('Congrats! Your first monorepo is here.');
        $this->symfonyStyle->note(sprintf(
            'Try the next step - merge "composer.json" files from packages to the root one:%s "vendor/bin/monorepo-builder merge"',
            PHP_EOL
        ));

        return ShellCode::SUCCESS;
    }

    /**
     * Returns current version of MonorepoBuilder, contains only major and minor.
     */
    private function getMonorepoBuilderVersion(): string
    {
        $version = new Version(str_replace('x-dev', '0', $this->composer->getPackage()->getPrettyVersion()));

        return sprintf('^%d.%d', $version->getMajor()->getValue(), $version->getMinor()->getValue());
    }
}
