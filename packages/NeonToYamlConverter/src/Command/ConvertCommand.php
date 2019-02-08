<?php declare(strict_types=1);

namespace Symplify\NeonToYamlConverter\Command;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\NeonToYamlConverter\Finder\NeonAndYamlFinder;
use Symplify\NeonToYamlConverter\NeonToYamlConverter;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

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
     * @var NeonToYamlConverter
     */
    private $neonToYamlConverter;

    /**
     * @var NeonAndYamlFinder
     */
    private $neonAndYamlFinder;

    public function __construct(
        NeonToYamlConverter $neonToYamlConverter,
        SymfonyStyle $symfonyStyle,
        NeonAndYamlFinder $neonAndYamlFinder
    ) {
        parent::__construct();
        $this->symfonyStyle = $symfonyStyle;
        $this->neonToYamlConverter = $neonToYamlConverter;
        $this->neonAndYamlFinder = $neonAndYamlFinder;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->addArgument(self::ARGUMENT_SOURCE, InputArgument::REQUIRED, 'Directory or file to convert');
        $this->setDescription('Converts Neon syntax to Yaml');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = (string) $input->getArgument(self::ARGUMENT_SOURCE);
        $yamlFileInfos = $this->neonAndYamlFinder->findYamlFilesInfSource($source);

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
}
