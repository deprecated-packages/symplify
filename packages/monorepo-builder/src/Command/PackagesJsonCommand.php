<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Command;

use Nette\Utils\Json;
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
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ((bool) $input->getOption(self::NAMES)) {
            $data = $this->packageJsonProvider->createPackageNames();
        } else {
            $data = $this->packageJsonProvider->createPackagePaths();
        }

        $json = Json::encode($data);
        $this->symfonyStyle->writeln($json);

        return ShellCode::SUCCESS;
    }
}
