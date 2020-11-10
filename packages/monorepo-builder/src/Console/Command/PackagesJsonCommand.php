<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Console\Command;

use Nette\Utils\Json;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\MonorepoBuilder\Package\PackageProvider;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;

final class PackagesJsonCommand extends AbstractSymplifyCommand
{
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
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $packageRelativePaths = [];
        foreach ($this->packageProvider->provide() as $package) {
            $packageRelativePaths[] = $package->getRelativePath();
        }

        $json = Json::encode($packageRelativePaths);
        $this->symfonyStyle->writeln($json);

        return ShellCode::SUCCESS;
    }
}
