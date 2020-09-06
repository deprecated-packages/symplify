<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Init\Command;

use Jean85\PrettyVersions;
use Nette\Utils\Json as NetteJson;
use OutOfBoundsException;
use PharIo\Version\InvalidVersionException;
use PharIo\Version\Version;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\SmartFileSystem;
use function dirname;

final class InitCommand extends Command
{
    /**
     * @var string
     */
    private const OUTPUT = 'output';

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    public function __construct(SymfonyStyle $symfonyStyle, SmartFileSystem $smartFileSystem)
    {
        parent::__construct();

        $this->symfonyStyle = $symfonyStyle;
        $this->smartFileSystem = $smartFileSystem;
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

        $this->smartFileSystem->mirror(__DIR__ . '/../../templates/monorepo', $output);

        // Replace MonorepoBuilder version in monorepo-builder.yml
        $filename = sprintf('%s/monorepo-builder.yaml', $output);

        $fileContent = $this->smartFileSystem->readFile($filename);
        $content = str_replace('<version>', $this->getMonorepoBuilderVersion(), $fileContent);

        $this->smartFileSystem->dumpFile($filename, $content);

        $this->symfonyStyle->success('Congrats! Your first monorepo is here.');
        $message = sprintf(
            'Try the next step - merge "composer.json" files from packages to the root one:%s "vendor/bin/monorepo-builder merge"',
            PHP_EOL
        );
        $this->symfonyStyle->note($message);

        return ShellCode::SUCCESS;
    }

    /**
     * Returns current version of MonorepoBuilder, contains only major and minor.
     */
    private function getMonorepoBuilderVersion(): string
    {
        $version = null;

        try {
            $prettyVersion = PrettyVersions::getVersion('symplify/monorepo-builder')->getPrettyVersion();
            $version = new Version(str_replace('x-dev', '0', $prettyVersion));
        } catch (OutOfBoundsException | InvalidVersionException $exceptoin) {
            // Version might not be explicitly set inside composer.json, looking for "vendor/composer/installed.json"
            $version = $this->extractMonorepoBuilderVersionFromComposer();
        }

        if ($version === null) {
            return 'Unknown';
        }

        return sprintf('^%d.%d', $version->getMajor()->getValue(), $version->getMinor()->getValue());
    }

    /**
     * Returns current version of MonorepoBuilder extracting it from "vendor/composer/installed.json".
     */
    private function extractMonorepoBuilderVersionFromComposer(): ?Version
    {
        $installedJsonFilename = sprintf('%s/composer/installed.json', dirname(__DIR__, 6));

        if (is_file($installedJsonFilename)) {
            $installedJsonFileContent = $this->smartFileSystem->readFile($installedJsonFilename);
            $installedJson = NetteJson::decode($installedJsonFileContent);

            foreach ($installedJson as $installedPackage) {
                if ($installedPackage->name === 'symplify/monorepo-builder') {
                    return new Version($installedPackage->version);
                }
            }
        }

        return null;
    }
}
