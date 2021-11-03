<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\Console\Output\FileErrorsReporter;
use Symplify\EasyCI\Latte\LatteTemplateProcessor;
use Symplify\EasyCI\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\Command\CommandNaming;

final class CheckLatteTemplateCommand extends AbstractSymplifyCommand
{
    public function __construct(
        private LatteTemplateProcessor $latteTemplateProcessor,
        private FileErrorsReporter $fileErrorsReporter
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));

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

        $fileErrors = $this->latteTemplateProcessor->analyzeFileInfos($latteFileInfos);

        return $this->fileErrorsReporter->report($fileErrors);
    }
}
