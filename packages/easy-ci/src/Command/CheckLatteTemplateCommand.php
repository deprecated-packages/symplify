<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\Latte\LatteTemplateProcessor;
use Symplify\EasyCI\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;

final class CheckLatteTemplateCommand extends AbstractSymplifyCommand
{
    public function __construct(
        private LatteTemplateProcessor $latteProcessor
    ) {
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sources = (array) $input->getArgument(Option::SOURCES);
        $latteFileInfos = $this->smartFinder->find($sources, '*.latte');

        $message = sprintf('Analysing %d *.latte files', count($latteFileInfos));
        $this->symfonyStyle->note($message);

        $TemplateErrors = $this->latteProcessor->analyzeFileInfos($latteFileInfos);
        if ($TemplateErrors === []) {
            $this->symfonyStyle->success('No errors found');
            return self::SUCCESS;
        }

        foreach ($TemplateErrors as $TemplateError) {
            $this->symfonyStyle->writeln($TemplateError->getRelativeFilePath());
            $this->symfonyStyle->writeln(' * ' . $TemplateError->getErrorMessage());
            $this->symfonyStyle->newLine();
        }

        $errorMassage = sprintf('%d errors found', count($TemplateErrors));
        $this->symfonyStyle->error($errorMassage);

        return self::FAILURE;
    }
}
