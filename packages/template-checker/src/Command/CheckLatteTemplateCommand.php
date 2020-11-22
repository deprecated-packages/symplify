<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\Command;

use Symplify\MigrifyKernel\Command\AbstractMigrifyCommand;
use Symplify\TemplateChecker\Analyzer\MissingClassConstantLatteAnalyzer;
use Symplify\TemplateChecker\Analyzer\MissingClassesLatteAnalyzer;
use Symplify\TemplateChecker\Analyzer\MissingClassStaticCallLatteAnalyzer;
use Symplify\TemplateChecker\Finder\GenericFilesFinder;
use Symplify\TemplateChecker\ValueObject\Option;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\SmartFileInfo;

final class CheckLatteTemplateCommand extends AbstractMigrifyCommand
{
    /**
     * @var GenericFilesFinder
     */
    private $genericFilesFinder;

    /**
     * @var MissingClassConstantLatteAnalyzer
     */
    private $missingClassConstantLatteAnalyzer;

    /**
     * @var MissingClassesLatteAnalyzer
     */
    private $missingClassesLatteAnalyzer;

    /**
     * @var MissingClassStaticCallLatteAnalyzer
     */
    private $missingClassStaticCallLatteAnalyzer;

    public function __construct(
        GenericFilesFinder $genericFilesFinder,
        MissingClassConstantLatteAnalyzer $missingClassConstantLatteAnalyzer,
        MissingClassesLatteAnalyzer $missingClassesLatteAnalyzer,
        MissingClassStaticCallLatteAnalyzer $missingClassStaticCallLatteAnalyzer
    ) {
        $this->genericFilesFinder = $genericFilesFinder;
        $this->missingClassConstantLatteAnalyzer = $missingClassConstantLatteAnalyzer;
        $this->missingClassesLatteAnalyzer = $missingClassesLatteAnalyzer;
        $this->missingClassStaticCallLatteAnalyzer = $missingClassStaticCallLatteAnalyzer;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            Option::SOURCES,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'One or more paths with templates'
        );
        $this->setDescription('Analyze missing classes, constant and static calls in Latte templates');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sources = (array) $input->getArgument(Option::SOURCES);
        $latteFileInfos = $this->genericFilesFinder->find($sources, '*.latte');

        $message = sprintf('Analysing %d *.latte files', count($latteFileInfos));
        $this->symfonyStyle->note($message);

        $errors = $this->analyzeFileInfosForErrors($latteFileInfos);

        if ($errors === []) {
            $this->symfonyStyle->success('No errors found');
            return ShellCode::SUCCESS;
        }

        foreach ($errors as $error) {
            $this->symfonyStyle->note($error);
        }

        $errorMassage = sprintf('%d errors found', count($errors));
        $this->symfonyStyle->error($errorMassage);

        return ShellCode::ERROR;
    }

    /**
     * @param SmartFileInfo[] $latteFileInfos
     * @return string[]
     */
    private function analyzeFileInfosForErrors(array $latteFileInfos): array
    {
        $errors = [];
        $errors += $this->missingClassesLatteAnalyzer->analyze($latteFileInfos);
        $errors += $this->missingClassConstantLatteAnalyzer->analyze($latteFileInfos);
        $errors += $this->missingClassStaticCallLatteAnalyzer->analyze($latteFileInfos);

        return $errors;
    }
}
