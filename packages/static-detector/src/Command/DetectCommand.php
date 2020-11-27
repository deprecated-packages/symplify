<?php

declare(strict_types=1);

namespace Symplify\StaticDetector\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\StaticDetector\Collector\StaticNodeCollector;
use Symplify\StaticDetector\Output\StaticReportReporter;
use Symplify\StaticDetector\StaticScanner;
use Symplify\StaticDetector\ValueObject\Option;

final class DetectCommand extends AbstractSymplifyCommand
{
    /**
     * @var StaticScanner
     */
    private $staticScanner;

    /**
     * @var StaticNodeCollector
     */
    private $staticNodeCollector;

    /**
     * @var StaticReportReporter
     */
    private $staticReportReporter;

    /**
     * @var ParameterProvider
     */
    private $parameterProvider;

    public function __construct(
        StaticScanner $staticScanner,
        StaticNodeCollector $staticNodeCollector,
        StaticReportReporter $staticReportReporter,
        ParameterProvider $parameterProvider
    ) {
        $this->staticScanner = $staticScanner;
        $this->staticNodeCollector = $staticNodeCollector;
        $this->staticReportReporter = $staticReportReporter;
        $this->parameterProvider = $parameterProvider;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            Option::SOURCES,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'One or more directories to detect static in'
        );
        $this->setDescription('Show what static method calls are called where');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sources = (array) $input->getArgument(Option::SOURCES);
        $fileInfos = $this->smartFinder->find($sources, '*.php');

        $filterClasses = (array) $this->parameterProvider->provideParameter(Option::FILTER_CLASSES);
        foreach ($filterClasses as $filterClass) {
            $message = sprintf('Filtering only "%s" classes', $filterClass);
            $this->symfonyStyle->note($message);
        }

        $this->staticScanner->scanFileInfos($fileInfos);

        $this->symfonyStyle->title('Static Report');
        $staticReport = $this->staticNodeCollector->generateStaticReport();

        $this->staticReportReporter->reportStaticClassMethods($staticReport);
        $this->staticReportReporter->reportTotalNumbers($staticReport);

        return ShellCode::SUCCESS;
    }
}
