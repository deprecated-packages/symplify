<?php declare(strict_types=1);

namespace Symplify\Autodiscovery\Command;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use Symplify\Autodiscovery\Yaml\ExplicitToAutodiscoveryConverter;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireSinglyImplementedCompilerPass;
use Symplify\PackageBuilder\FileSystem\FinderSanitizer;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class ConvertYamlCommand extends Command
{
    /**
     * @var string
     */
    private const OPTION_REMOVE_SINGLY_IMPLEMENTED = 'remove-singly-implemented';

    /**
     * @var string
     */
    private const OPTION_NESTING_LEVEL = 'nesting-level';

    /**
     * @var string
     */
    private const ARGUMENT_DIRECTORY = 'directory';

    /**
     * @var ExplicitToAutodiscoveryConverter
     */
    private $explicitToAutodiscoveryConverter;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;

    public function __construct(
        ExplicitToAutodiscoveryConverter $explicitToAutodiscoveryConverter,
        SymfonyStyle $symfonyStyle,
        FinderSanitizer $finderSanitizer
    ) {
        parent::__construct();
        $this->explicitToAutodiscoveryConverter = $explicitToAutodiscoveryConverter;
        $this->symfonyStyle = $symfonyStyle;
        $this->finderSanitizer = $finderSanitizer;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription(
            'Convert "(services|config).(yml|yaml)" from pre-Symfony 3.3 format to modern format using autodiscovery, autowire and autoconfigure'
        );

        $this->addArgument(self::ARGUMENT_DIRECTORY, InputArgument::REQUIRED, 'Path to your application');

        $this->addOption(
            self::OPTION_REMOVE_SINGLY_IMPLEMENTED,
            'r',
            InputOption::VALUE_NONE,
            sprintf(
                'Remove aliases added only for only-implementations, useful in combination with "%s".',
                AutowireSinglyImplementedCompilerPass::class
            )
        );

        $this->addOption(
            self::OPTION_NESTING_LEVEL,
            'l',
            InputOption::VALUE_REQUIRED,
            'How many namespace levels should be separated in autodiscovery, e.g 2 → "App\SomeProject\", 3 → "App\SomeProject\InnerNamespace\"',
            2
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $directory = (string) $input->getArgument(self::ARGUMENT_DIRECTORY);

        $yamlFileInfos = $this->findServiceYamlFilesInDirectory($directory);

        foreach ($yamlFileInfos as $yamlFileInfo) {
            $this->symfonyStyle->section('Processing ' . $yamlFileInfo->getRealPath());

            $removeSinglyImplemented = (bool) $input->getOption(self::OPTION_REMOVE_SINGLY_IMPLEMENTED);
            $nestingLevel = (int) $input->getOption(self::OPTION_NESTING_LEVEL);

            $servicesYaml = Yaml::parse($yamlFileInfo->getContents());

            $convertedYaml = $this->explicitToAutodiscoveryConverter->convert(
                $servicesYaml,
                $yamlFileInfo->getRealPath(),
                $nestingLevel,
                $removeSinglyImplemented
            );

            if ($servicesYaml === $convertedYaml) {
                $this->symfonyStyle->note('No changes');
                continue;
            }

            $convertedContent = Yaml::dump($convertedYaml, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);

            // "SomeNamespace\SomeService: null" → "SomeNamespace\SomeService: ~"
            $convertedContent = Strings::replace($convertedContent, '#^( {4}([A-Z].*?): )(null)$#m', '$1~');

            // save
            FileSystem::write($yamlFileInfo->getRealPath(), $convertedContent);

            $this->symfonyStyle->note('File converted');
        }

        $this->symfonyStyle->success('Done');

        return ShellCode::SUCCESS;
    }

    /**
     * @return SmartFileInfo[]
     */
    private function findServiceYamlFilesInDirectory(string $directory): array
    {
        $finder = Finder::create()->files()
            ->name('#(config|services)\.(\w+\.)?(yml|yaml)$#')
            ->in($directory);

        return $this->finderSanitizer->sanitize($finder);
    }
}
