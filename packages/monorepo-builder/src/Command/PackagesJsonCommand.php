<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Command;

use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\MonorepoBuilder\Json\PackageJsonProvider;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;

final class PackagesJsonCommand extends AbstractSymplifyCommand
{
    /**
     * @var string
     */
    private const NAMES = 'names';

    /**
     * @var string
     */
    private const BASE_DATA = 'base-data';

    /**
     * @var PackageJsonProvider
     */
    private $packageJsonProvider;

    public function __construct(PackageJsonProvider $packageJsonProvider)
    {
        $this->packageJsonProvider = $packageJsonProvider;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Provides packages in json format. Useful for GitHub Actions Workflow');
        $this->addOption(self::NAMES, null, InputOption::VALUE_NONE, 'Return package names');
        $this->addOption(self::BASE_DATA, null, InputOption::VALUE_REQUIRED, 'Base json data to append to. Example: {"php-version":["7.4","8.0"]}');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $names = (bool) $input->getOption(self::NAMES);
        $baseData = (string) $input->getOption(self::BASE_DATA);
        try {
            $base = Json::decode($baseData ?: "[]", Json::FORCE_ARRAY);
        } catch (JsonException $jsonException) {
            $this->symfonyStyle->error('Could not decode base json data. Please make you provided valid json.');

            return ShellCode::ERROR;
        }

        $data = $names ? $this->packageJsonProvider->createPackageNames() : $this->packageJsonProvider->createPackagePaths();

        $json = Json::encode($base + $data);
        $this->symfonyStyle->writeln($json);

        return ShellCode::SUCCESS;
    }
}
