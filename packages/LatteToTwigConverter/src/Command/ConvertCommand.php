<?php

declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\Command;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\LatteToTwigConverter\Finder\LatteAndTwigFinder;
use Symplify\LatteToTwigConverter\LatteToTwigConverter;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

final class ConvertCommand extends Command
{
    /**
     * @var string
     */
    private const ARGUMENT_SOURCE = 'source';

    /**
     * @var LatteToTwigConverter
     */
    private $latteToTwigConverter;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var LatteAndTwigFinder
     */
    private $latteAndTwigFinder;

    public function __construct(
        LatteToTwigConverter $latteToTwigConverter,
        SymfonyStyle $symfonyStyle,
        LatteAndTwigFinder $latteAndTwigFinder
    ) {
        parent::__construct();
        $this->latteToTwigConverter = $latteToTwigConverter;
        $this->symfonyStyle = $symfonyStyle;
        $this->latteAndTwigFinder = $latteAndTwigFinder;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->addArgument(self::ARGUMENT_SOURCE, InputArgument::REQUIRED, 'Directory or file to convert');
        $this->setDescription('Converts Latte syntax to Twig');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = (string) $input->getArgument(self::ARGUMENT_SOURCE);
        $fileInfos = $this->latteAndTwigFinder->findTwigAndLatteFilesInSource($source);

        foreach ($fileInfos as $fileInfo) {
            $convertedContent = $this->latteToTwigConverter->convertFile($fileInfo);
            $oldFilePath = $fileInfo->getPathname();
            $newFilePath = Strings::replace($fileInfo->getPathname(), '#\.latte$#', '.twig');

            // save
            FileSystem::write($newFilePath, $convertedContent);

            // remove old path
            if ($oldFilePath !== $newFilePath) {
                FileSystem::delete($oldFilePath);
            }

            $this->symfonyStyle->note(sprintf(
                'File "%s" was converted to Twig to "%s"',
                $oldFilePath,
                $newFilePath
            ));
        }

        $this->symfonyStyle->success('Done');

        return ShellCode::SUCCESS;
    }
}
