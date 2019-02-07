<?php declare(strict_types=1);

namespace Symplify\NeonToYamlConverter\Command;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symplify\NeonToYamlConverter\NeonToYamlConverter;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\PackageBuilder\FileSystem\FinderSanitizer;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class ConvertCommand extends Command
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
     * @var NeonToYamlConverter
     */
    private $neonToYamlConverter;

    public function __construct(
        NeonToYamlConverter $neonToYamlConverter,
        SymfonyStyle $symfonyStyle,
        FinderSanitizer $finderSanitizer
    ) {
        parent::__construct();
        $this->symfonyStyle = $symfonyStyle;
        $this->finderSanitizer = $finderSanitizer;
        $this->neonToYamlConverter = $neonToYamlConverter;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->addArgument(
            self::ARGUMENT_SOURCE,
            InputArgument::REQUIRED,
            'Directory to convert Neon files to Yaml syntax in.'
        );
        $this->setDescription('Converts Neon syntax to Yaml');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sourceDirectory = (string) $input->getArgument(self::ARGUMENT_SOURCE);
        $yamlFileInfos = $this->findYamlFilesInDirectory($sourceDirectory);

        foreach ($yamlFileInfos as $yamlFileInfo) {
            $convertedContent = $this->neonToYamlConverter->convertFile($yamlFileInfo->getRealPath());

            if ($yamlFileInfo->getContents() !== $convertedContent) {
                FileSystem::write($yamlFileInfo->getPathname(), $convertedContent);

                $this->symfonyStyle->note(sprintf('File "%s" was converted to Twig', $yamlFileInfo->getPathname()));
                continue;
            }

            $this->symfonyStyle->note(
                sprintf('File "%s" was skipped for no change', $yamlFileInfo->getPathname())
            );
        }

        $this->symfonyStyle->success('Convert process finished');

        return ShellCode::SUCCESS;
    }

    /**
     * @return SmartFileInfo[]
     */
    private function findYamlFilesInDirectory(string $sourceDirectory): array
    {
        $twigFileFinder = Finder::create()
            ->files()
            ->in($sourceDirectory)
            ->name('#\.(yml|yaml)#');

        return $this->finderSanitizer->sanitize($twigFileFinder);
    }
}
