<?php

declare(strict_types=1);

namespace Symplify\SymfonyRouteUsage\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Route;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SymfonyRouteUsage\Routing\DeadRoutesProvider;

final class ShowDeadRoutesCommand extends AbstractSymplifyCommand
{
    /**
     * @var string[]
     */
    private const TABLE_HEADLINE = ['Route Name', 'Controller'];

    /**
     * @var DeadRoutesProvider
     */
    private $deadRoutesProvider;

    public function __construct(DeadRoutesProvider $deadRoutesProvider)
    {
        $this->deadRoutesProvider = $deadRoutesProvider;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Display dead routes');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tableData = [];
        $this->symfonyStyle->title('Dead Routes');

        /** @var Route $route */
        foreach ($this->deadRoutesProvider->provide() as $routeName => $route) {
            $tableData[] = [
                'route_name' => $routeName,
                'controller' => $route->getDefault('_controller'),
            ];
        }
        $this->symfonyStyle->table(self::TABLE_HEADLINE, $tableData);

        $otherCommandMessage = sprintf(
            'Do you want to see what routes are used? Run "bin/console %s"',
            CommandNaming::classToName(ShowRouteUsageCommand::class)
        );
        $this->symfonyStyle->note($otherCommandMessage);

        return ShellCode::SUCCESS;
    }
}
