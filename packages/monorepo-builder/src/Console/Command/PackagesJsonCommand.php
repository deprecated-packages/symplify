<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Console\Command;

use Nette\Utils\Json;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\MonorepoBuilder\Package\PackageProvider;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;

final class PackagesJsonCommand extends AbstractSymplifyCommand
{
    /**
     * @var string
     */
    private const NAMES = 'names';

    /**
     * @var PackageProvider
     */
    private $packageProvider;

    public function __construct(PackageProvider $packageProvider)
    {
        $this->packageProvider = $packageProvider;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Provides packages in json format. Useful for GitHub Actions Workflow');
        $this->addOption(self::NAMES, null, InputOption::VALUE_NONE, 'Return package names');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $data = (bool) $input->getOption(self::NAMES) ? $this->createPackageNames() : $this->createPackagePaths();

        $json = Json::encode($data);
        $this->symfonyStyle->writeln($json);

        return ShellCode::SUCCESS;
    }

    /**
     * @return array<string, string[]>
     */
    private function createPackagePaths(): array
    {
        $packageRelativePaths = [];
        foreach ($this->packageProvider->provide() as $package) {
            $packageRelativePaths[] = $package->getRelativePath();
        }

        return [
            'package_path' => $packageRelativePaths,
        ];
    }

    /**
     * @return array<string, string[]>
     */
    private function createPackageNames(): array
    {
        $packageShortNames = [];
        foreach ($this->packageProvider->provide() as $package) {
            $packageShortNames[] = $package->getShortName();
        }

        return [
            'package_name' => $packageShortNames,
        ];
    }
}
