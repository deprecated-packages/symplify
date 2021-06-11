<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\Git\ConflictResolver;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\PackageBuilder\ValueObject\Option;

final class CheckConflictsCommand extends AbstractSymplifyCommand
{
    public function __construct(
        private ConflictResolver $conflictResolver
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Check files for missed git conflicts');
        $this->addArgument(Option::SOURCES, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path to project');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string[] $source */
        $source = (array) $input->getArgument(Option::SOURCES);

        $fileInfos = $this->smartFinder->find($source, '*', ['vendor']);

        $conflictsCountByFilePath = $this->conflictResolver->extractFromFileInfos($fileInfos);
        if ($conflictsCountByFilePath === []) {
            $message = sprintf('No conflicts found in %d files', count($fileInfos));
            $this->symfonyStyle->success($message);

            return ShellCode::SUCCESS;
        }

        foreach ($conflictsCountByFilePath as $file => $conflictCount) {
            $message = sprintf('File "%s" contains %d unresolved conflicts', $file, $conflictCount);
            $this->symfonyStyle->error($message);
        }

        return ShellCode::ERROR;
    }
}
