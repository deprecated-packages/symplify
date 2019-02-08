<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\Command;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\LatteToTwigConverter\Finder\LatteAndTwigFinder;
use Symplify\LatteToTwigConverter\LatteToTwigConverter;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

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
        $this->addArgument(
            self::ARGUMENT_SOURCE,
            InputArgument::REQUIRED,
            'Directory or file to convert *.twig files to Latte syntax in.'
        );
        $this->setDescription('Converts Latte syntax to Twig in all *.twig files');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $twigFileInfos = $this->resolveFileInfosFromInput($input);
        foreach ($twigFileInfos as $twigFileInfo) {
            $convertedContent = $this->latteToTwigConverter->convertFile($twigFileInfo->getRealPath());

            if ($twigFileInfo->getContents() !== $convertedContent) {
                FileSystem::write($twigFileInfo->getPathname(), $convertedContent);

                $this->symfonyStyle->note(sprintf('File "%s" was converted to Twig', $twigFileInfo->getPathname()));
                continue;
            }

            $this->symfonyStyle->note(
                sprintf('File "%s" was skipped for no match of latte syntax', $twigFileInfo->getPathname())
            );
        }

        $this->symfonyStyle->success('Convert process finished');

        return ShellCode::SUCCESS;
    }

    /**
     * @return SmartFileInfo[]
     */
    private function resolveFileInfosFromInput(InputInterface $input): array
    {
        $source = (string) $input->getArgument(self::ARGUMENT_SOURCE);
        return $this->latteAndTwigFinder->findTwigFilesInSource($source);
    }
}
