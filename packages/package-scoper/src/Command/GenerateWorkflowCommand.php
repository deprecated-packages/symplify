<?php

declare(strict_types=1);

namespace Symplify\PackageScoper\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class GenerateWorkflowCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var string
     */
    private $workflowFilePath;

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    public function __construct(SymfonyStyle $symfonyStyle, SmartFileSystem $smartFileSystem)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->workflowFilePath = getcwd() . '/.github/workflows/build_scoped_packages.yaml';
        $this->smartFileSystem = $smartFileSystem;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Generate Github Action for scoping packages');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (file_exists($this->workflowFilePath)) {
            $workflowFileInfo = new SmartFileInfo($this->workflowFilePath);
            $question = sprintf(
                'The "%s" file already exists. Should we override it?',
                $workflowFileInfo->getRelativeFilePathFromCwd()
            );
            if (! $this->symfonyStyle->confirm($question)) {
                $this->symfonyStyle->note('Nothing changed');
                return ShellCode::SUCCESS;
            }
        }

        $this->smartFileSystem->copy(
            __DIR__ . '/../../templates/github/workflows/build_scoped_packages.yaml',
            $this->workflowFilePath
        );

        $workflowFileInfo = new SmartFileInfo($this->workflowFilePath);
        $message = sprintf('File "%s" was crated', $workflowFileInfo->getRelativeFilePathFromCwd());
        $this->symfonyStyle->success($message);

        $this->symfonyStyle->warning(
            'We still need some manual work from you to make sure the Workflow is tailed to your needs:'
        );
        $this->symfonyStyle->listing([
            'complete email to commit with',
            'complete package names you want to scope and split',
            'add GitHub token to your repository Secrets (e.g. https://github.com/symplify/symplify/settings/secrets)',
            'change PHP version if you fancy another than default one',
        ]);

        return ShellCode::SUCCESS;
    }
}
