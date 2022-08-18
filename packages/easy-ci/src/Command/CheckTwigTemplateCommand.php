<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\Console\Output\FileErrorsReporter;
use Symplify\EasyCI\Twig\TwigTemplateProcessor;
use Symplify\EasyCI\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;

final class CheckTwigTemplateCommand extends AbstractSymplifyCommand
{
    public function __construct(
        private TwigTemplateProcessor $twigTemplateProcessor,
        private FileErrorsReporter $fileErrorsReporter
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('check-twig-template');

        $this->addArgument(
            Option::SOURCES,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'One or more paths with templates'
        );
        $this->setDescription('Analyze missing classes, constant and static calls in Twig templates');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sources = (array) $input->getArgument(Option::SOURCES);
        $twigFileInfos = $this->smartFinder->find($sources, '*.twig');

        $message = sprintf('Analysing %d *.twig files', count($twigFileInfos));
        $this->symfonyStyle->note($message);

        $fileErrors = $this->twigTemplateProcessor->analyzeFileInfos($twigFileInfos);

        return $this->fileErrorsReporter->report($fileErrors);
    }
}
