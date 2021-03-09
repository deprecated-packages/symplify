<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\Console\Output\StaticClassMethodNamesReporter;
use Symplify\EasyCI\Latte\LatteFilterManager;
use Symplify\EasyCI\Latte\LatteStaticCallAnalyzer;
use Symplify\EasyCI\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;

final class ExtractLatteStaticCallToFilterCommand extends AbstractSymplifyCommand
{
    /**
     * @var LatteStaticCallAnalyzer
     */
    private $latteStaticCallAnalyzer;

    /**
     * @var LatteFilterManager
     */
    private $latteFilterManager;

    /**
     * @var StaticClassMethodNamesReporter
     */
    private $staticClassMethodNamesReporter;

    public function __construct(
        LatteStaticCallAnalyzer $latteStaticCallAnalyzer,
        LatteFilterManager $latteFilterManager,
        StaticClassMethodNamesReporter $staticClassMethodNamesReporter
    ) {
        $this->latteStaticCallAnalyzer = $latteStaticCallAnalyzer;
        $this->latteFilterManager = $latteFilterManager;
        $this->staticClassMethodNamesReporter = $staticClassMethodNamesReporter;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            Option::SOURCES,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'One one or more directories or files to process'
        );
        $this->setDescription(
            'Analyzing latte templates for static calls that should be Latte Filters and extracting them'
        );

        $this->addOption(
            Option::FIX,
            null,
            InputOption::VALUE_NONE,
            'Generate *FilterProvider and replace static calls in templates with filters'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $directories = (array) $input->getArgument(Option::SOURCES);
        $latteFileInfos = $this->smartFinder->find($directories, '*.latte');

        $fileMessage = sprintf('Extracting filters from "%d" files', count($latteFileInfos));
        $this->symfonyStyle->title($fileMessage);

        $classMethodNames = $this->latteStaticCallAnalyzer->analyzeFileInfos($latteFileInfos);
        if ($classMethodNames === []) {
            $this->symfonyStyle->success('No static calls found in templates. Good job!');
            return ShellCode::SUCCESS;
        }

        $isFix = (bool) $input->getOption(Option::FIX);

        $this->staticClassMethodNamesReporter->reportClassMethodNames($classMethodNames);
        $this->latteFilterManager->manageClassMethodNames($latteFileInfos, $classMethodNames, $isFix);

        return ShellCode::ERROR;
    }
}
