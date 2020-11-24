<?php

declare(strict_types=1);

namespace Symplify\SymfonyRouteUsage\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
<<<<<<< HEAD
<<<<<<< HEAD
=======
use Symplify\SymfonyRouteUsage\EntityRepository\RouteVisitRepository;
>>>>>>> 91a7cf6c2... fixup! misc
=======
use Symplify\SymfonyRouteUsage\EntityRepository\RouteVisitRepository;
<<<<<<< HEAD
use Symplify\symplifyKernel\Command\AbstractsymplifyCommand;
>>>>>>> 434bcd4b3... rename Migrify to Symplify
=======
use Symplify\SymplifyKernel\Command\AbstractSymplifyCommand;
>>>>>>> 1a08239af... misc

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
