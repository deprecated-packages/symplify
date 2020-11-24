<?php

declare(strict_types=1);

namespace Symplify\SymfonyRouteUsage\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SymfonyRouteUsage\EntityRepository\RouteVisitRepository;

final class ShowRouteUsageCommand extends AbstractSymplifyCommand
{
    /**
     * @var string[]
     */
    private const TABLE_HEADLINE = ['Visits', 'Controller', 'Route', 'Method', 'Last Visit'];

    /**
     * @var RouteVisitRepository
     */
    private $routeVisitRepository;

    public function __construct(RouteVisitRepository $routeVisitRepository)
    {
        $this->routeVisitRepository = $routeVisitRepository;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Show usage of routes');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tableData = [];
        $this->symfonyStyle->title('Used Routes by Visit Count');
        foreach ($this->routeVisitRepository->fetchAll() as $routeVisit) {
            $tableData[] = [
                'visit_count' => $routeVisit->getVisitCount(),
                'route' => $routeVisit->getRoute(),
                'controller' => $routeVisit->getController(),
                'method' => $routeVisit->getMethod(),
                'last_visit' => $routeVisit->getUpdatedAt()
                    ->format('Y-m-d'),
            ];
        }
        $this->symfonyStyle->table(self::TABLE_HEADLINE, $tableData);

        $otherCommandMessage = sprintf(
            'Do you want to see what routes are dead? Run "bin/console %s"',
            CommandNaming::classToName(ShowDeadRoutesCommand::class)
        );
        $this->symfonyStyle->note($otherCommandMessage);

        return ShellCode::SUCCESS;
    }
}
