<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\TemplateChecker\Latte\LatteAnalyzer;
use Symplify\TemplateChecker\ValueObject\Option;

final class CheckLatteTemplateCommand extends AbstractSymplifyCommand
{
    /**
     * @var LatteAnalyzer
     */
    private $latteAnalyzer;

    public function __construct(LatteAnalyzer $latteAnalyzer)
    {
        parent::__construct();

        $this->latteAnalyzer = $latteAnalyzer;
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
        $latteFileInfos = $this->smartFinder->find($sources, '*.latte');

        $message = sprintf('Analysing %d *.latte files', count($latteFileInfos));
        $this->symfonyStyle->note($message);

        $errors = $this->latteAnalyzer->analyzeFileInfos($latteFileInfos);
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
}
