<?php

declare(strict_types=1);

namespace Symplify\NeonToYamlConverter\Command;

use Nette\Utils\Strings;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\NeonToYamlConverter\ArrayParameterCollector;
use Symplify\NeonToYamlConverter\Finder\NeonAndYamlFinder;
use Symplify\NeonToYamlConverter\NeonToYamlConverter;
use Symplify\NeonToYamlConverter\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;

final class ConvertCommand extends AbstractSymplifyCommand
{
    /**
     * @see https://regex101.com/r/lrPvtZ/1
     * @var string
     */
    private const NEON_SUFFIX_REGEX = '#\.neon$#';

    public function __construct(
        private NeonToYamlConverter $neonToYamlConverter,
        private NeonAndYamlFinder $neonAndYamlFinder,
        private ArrayParameterCollector $arrayParameterCollector
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(Option::SOURCES, InputArgument::REQUIRED, 'Directory or file to convert');
        $this->setDescription('Converts Neon syntax to Yaml');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = (string) $input->getArgument(Option::SOURCES);
        $fileInfos = $this->neonAndYamlFinder->findYamlAndNeonFilesInSource($source);

        $this->arrayParameterCollector->collectFromFiles($fileInfos);

        foreach ($fileInfos as $fileInfo) {
            $convertedContent = $this->neonToYamlConverter->convertFileInfo($fileInfo);
            $oldFilePath = $fileInfo->getPathname();
            $newFilePath = Strings::replace($oldFilePath, self::NEON_SUFFIX_REGEX, '.yaml');

            // save
            $this->smartFileSystem->dumpFile($newFilePath, $convertedContent);

            // remove old path
            if ($oldFilePath !== $newFilePath) {
                $this->smartFileSystem->remove($oldFilePath);
            }
            $message = sprintf('File "%s" was converted to YAML to "%s" path', $oldFilePath, $newFilePath);

            $this->symfonyStyle->note($message);
        }

        $this->symfonyStyle->success('Done');

        return ShellCode::SUCCESS;
    }
}
