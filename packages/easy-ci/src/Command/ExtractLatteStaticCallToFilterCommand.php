<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\Console\Output\StaticClassMethodNamesReporter;
use Symplify\EasyCI\Latte\Analyzer\StaticCallLatteAnalyzer;
use Symplify\EasyCI\Latte\LatteFilter\LatteFilterManager;
use Symplify\EasyCI\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;

final class ExtractLatteStaticCallToFilterCommand extends AbstractSymplifyCommand
{
    public function __construct(
        private StaticCallLatteAnalyzer $staticCallLatteAnalyzer,
        private LatteFilterManager $latteFilterManager,
        private StaticClassMethodNamesReporter $staticClassMethodNamesReporter
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            Option::SOURCES,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'One one or more directories or files to process'
        );

        // @todo add descripton to latte analyzers!
        $this->setDescription(
            'Analyzing latte templates for static calls that should be Latte Filters and extracting them'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $directories = (array) $input->getArgument(Option::SOURCES);
        $latteFileInfos = $this->smartFinder->find($directories, '*.latte');

        $classMethodNames = $this->staticCallLatteAnalyzer->analyze($latteFileInfos);
        if ($classMethodNames === []) {
            $this->symfonyStyle->success('No static calls found in templates. Good job!');
            return ShellCode::SUCCESS;
        }

        // @todo this is very smart!
        $this->symfonyStyle->writeln('Template call located at: ' . $classMethodName->getLatteFilePath());

        if (! $classMethodName->isOnVariableStaticCall()) {
            $this->symfonyStyle->writeln('Method located at: ' . $classMethodName->getFileLine());
        }

        $this->symfonyStyle->error(
            'We found some static calls in your templates. Do you want to extract them to latte filter provider? Just re-run command with `--fix` option'
        );

        return ShellCode::ERROR;
    }
}
