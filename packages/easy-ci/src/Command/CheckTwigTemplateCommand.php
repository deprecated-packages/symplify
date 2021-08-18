<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\Twig\TwigTemplateProcessor;
use Symplify\EasyCI\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;

final class CheckTwigTemplateCommand extends AbstractSymplifyCommand
{
    public function __construct(
        private TwigTemplateProcessor $twigTemplateProcessor
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

        $message = sprintf('Analysing %d *.twig files', count($latteFileInfos));
        $this->symfonyStyle->note($message);

        $templateErrors = $this->twigTemplateProcessor->analyzeFileInfos($latteFileInfos);
        if ($templateErrors === []) {
            $this->symfonyStyle->success('No errors found');
            return self::SUCCESS;
        }

        foreach ($templateErrors as $templateError) {
            $this->symfonyStyle->writeln($templateError->getRelativeFilePath());
            $this->symfonyStyle->writeln(' * ' . $templateError->getErrorMessage());
            $this->symfonyStyle->newLine();
        }

        $errorMassage = sprintf('%d errors found', count($templateErrors));
        $this->symfonyStyle->error($errorMassage);

        return self::FAILURE;
    }
}
