<?php declare(strict_types=1);

namespace Symplify\NeonToYamlConverter\Command;

use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symplify\NeonToYamlConverter\Finder\NeonAndYamlFinder;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

final class RenameCommand extends Command
{
    /**
     * @var string
     */
    private const ARGUMENT_SOURCE = 'source';

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var NeonAndYamlFinder
     */
    private $neonAndYamlFinder;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        NeonAndYamlFinder $neonAndYamlFinder,
        Filesystem $filesystem
    ) {
        parent::__construct();
        $this->symfonyStyle = $symfonyStyle;
        $this->neonAndYamlFinder = $neonAndYamlFinder;
        $this->filesystem = $filesystem;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->addArgument(self::ARGUMENT_SOURCE, InputArgument::REQUIRED, 'Directory or file to rename');
        $this->setDescription(
            sprintf('Renames *.neon files to *.yaml. Run before "%s" command', CommandNaming::classToName(
                ConvertCommand::class
            ))
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = (string) $input->getArgument(self::ARGUMENT_SOURCE);
        $neonFileInfos = $this->neonAndYamlFinder->findNeonFilesInSource($source);

        foreach ($neonFileInfos as $neonFileInfo) {
            $newFilePath = Strings::replace($neonFileInfo->getPathname(), '#\.neon#', '.yaml');

            $this->filesystem->rename($neonFileInfo->getPathname(), $newFilePath);

            $this->symfonyStyle->note(sprintf('File "%s" renamed', $neonFileInfo->getPathname()));
        }

        $this->symfonyStyle->success('Rename process finished');

        return ShellCode::SUCCESS;
    }
}
