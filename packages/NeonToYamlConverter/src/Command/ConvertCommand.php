<?php

declare(strict_types=1);

namespace Symplify\NeonToYamlConverter\Command;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\NeonToYamlConverter\ArrayParameterCollector;
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

    /**
     * @var ArrayParameterCollector
     */
    private $arrayParameterCollector;

    public function __construct(
        NeonToYamlConverter $neonToYamlConverter,
        SymfonyStyle $symfonyStyle,
        NeonAndYamlFinder $neonAndYamlFinder,
        ArrayParameterCollector $arrayParameterCollector
    ) {
        parent::__construct();
        $this->symfonyStyle = $symfonyStyle;
        $this->neonToYamlConverter = $neonToYamlConverter;
        $this->neonAndYamlFinder = $neonAndYamlFinder;
        $this->arrayParameterCollector = $arrayParameterCollector;
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
        $fileInfos = $this->neonAndYamlFinder->findYamlAndNeonFilesInfSource($source);

        $this->arrayParameterCollector->collectFromFiles($fileInfos);

        foreach ($fileInfos as $fileInfo) {
            $convertedContent = $this->neonToYamlConverter->convertFile($fileInfo);
            $oldFilePath = $fileInfo->getPathname();
            $newFilePath = Strings::replace($oldFilePath, '#\.neon$#', '.yaml');

            // save
            FileSystem::write($newFilePath, $convertedContent);

            // remove old path
            if ($oldFilePath !== $newFilePath) {
                FileSystem::delete($oldFilePath);
            }

            $this->symfonyStyle->note(sprintf(
                'File "%s" was converted to YAML to "%s" path',
                $oldFilePath,
                $newFilePath
            ));
        }

        $this->symfonyStyle->success('Done');

        return ShellCode::SUCCESS;
    }
}
