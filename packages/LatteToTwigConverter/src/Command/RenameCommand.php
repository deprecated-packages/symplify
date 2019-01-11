<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\Command;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symplify\LatteToTwigConverter\LatteToTwigConverter;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\FileSystem\FinderSanitizer;
use function Safe\sprintf;

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
     * @var FinderSanitizer
     */
    private $finderSanitizer;

    public function __construct(
        LatteToTwigConverter $latteToTwigConverter,
        SymfonyStyle $symfonyStyle,
        FinderSanitizer $finderSanitizer
    ) {
        parent::__construct();
        $this->latteToTwigConverter = $latteToTwigConverter;
        $this->symfonyStyle = $symfonyStyle;
        $this->finderSanitizer = $finderSanitizer;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->addArgument(
            self::ARGUMENT_SOURCE,
            InputArgument::REQUIRED,
            'Directory to convert *.twig files to Latte syntax in.'
        );
        $this->setDescription('Converts Latte syntax to Twig in all *.twig files');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $twigFileFinder = Finder::create()
            ->files()
            ->in($input->getArgument(self::ARGUMENT_SOURCE))
            ->name('*.twig');

        $smartFileInfos = $this->finderSanitizer->sanitize($twigFileFinder);

        foreach ($smartFileInfos as $twigFileInfo) {
            $convertedContent = $this->latteToTwigConverter->convertFile($twigFileInfo->getRealPath());

            if ($twigFileInfo->getContents() !== $convertedContent) {
                FileSystem::write($twigFileInfo->getPathname(), $convertedContent);

                $this->symfonyStyle->note(sprintf('File "%s" was converted to Twig', $twigFileInfo->getPathname()));
            } else {
                $this->symfonyStyle->note(
                    sprintf('File "%s" was skipped for no match of latte syntax', $twigFileInfo->getPathname())
                );
            }
        }

        $this->symfonyStyle->success('Convert process finished');
    }
}
