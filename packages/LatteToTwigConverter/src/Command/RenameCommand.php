<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\Command;

use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\PackageBuilder\FileSystem\FinderSanitizer;
use function Safe\sprintf;

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
     * @var FinderSanitizer
     */
    private $finderSanitizer;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        FinderSanitizer $finderSanitizer,
        Filesystem $filesystem
    ) {
        parent::__construct();
        $this->symfonyStyle = $symfonyStyle;
        $this->finderSanitizer = $finderSanitizer;
        $this->filesystem = $filesystem;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->addArgument(self::ARGUMENT_SOURCE, InputArgument::REQUIRED, 'Directory with *.latte files');
        $this->setDescription(
            sprintf('Renames *.latte files to *.twig files. Run before "%s" command', CommandNaming::classToName(
                ConvertCommand::class
            ))
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sourceDirectory = $input->getArgument(self::ARGUMENT_SOURCE);

        $latteFileFinder = Finder::create()
            ->files()
            ->in($sourceDirectory)
            ->name('#\.latte$#');

        $smartFileInfos = $this->finderSanitizer->sanitize($latteFileFinder);

        foreach ($smartFileInfos as $smartFileInfo) {
            $newFilePath = Strings::replace($smartFileInfo->getPathname(), '#\.latte$#', '.twig');

            $this->filesystem->rename($smartFileInfo->getPathname(), $newFilePath);

            $this->symfonyStyle->note(sprintf('File "%s" renamed', $smartFileInfo->getPathname()));
        }

        $this->symfonyStyle->success('Rename process finished');

        return ShellCode::SUCCESS;
    }
}
