<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Command;

use Nette\Utils\Json;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\MonorepoBuilder\Contract\ComposerJsonDecoratorInterface;
use Symplify\MonorepoBuilder\Package\PackageComposerJsonMerger;
use Symplify\PackageBuilder\Console\Command\CommandNaming;

final class MergeCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var ComposerJsonDecoratorInterface[]
     */
    private $composerJsonDecorators = [];

    /**
     * @var PackageComposerJsonMerger
     */
    private $packageComposerJsonMerger;

    /**
     * @var string[]
     */
    private $mergeSections = [];

    /**
     * @param string[] $mergeSections
     */
    public function __construct(
        array $mergeSections,
        SymfonyStyle $symfonyStyle,
        PackageComposerJsonMerger $packageComposerJsonMerger
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->packageComposerJsonMerger = $packageComposerJsonMerger;
        $this->mergeSections = $mergeSections;

        parent::__construct();
    }

    public function addComposerJsonDecorator(ComposerJsonDecoratorInterface $composerJsonDecorator): void
    {
        $this->composerJsonDecorators[] = $composerJsonDecorator;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Merge "composer.json" from all found packages to root one');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $composerPackageFiles = $this->getPackageComposerFiles();
        if (! count($composerPackageFiles)) {
            $this->symfonyStyle->error('No "composer.json" were found in packages.');
            return 1;
        }

        $rootComposerJson = Json::decode(file_get_contents(getcwd() . '/composer.json'), Json::FORCE_ARRAY);
        $merged = $this->packageComposerJsonMerger->mergeFileInfos($composerPackageFiles, $this->mergeSections);

        foreach ($this->mergeSections as $sectionToMerge) {
            // nothing collected to merge
            if (! isset($merged[$sectionToMerge]) || empty($merged[$sectionToMerge])) {
                continue;
            }

            // section in root composer.json is empty, just set and go
            if (! isset($rootComposerJson[$sectionToMerge])) {
                $rootComposerJson[$sectionToMerge] = $merged[$sectionToMerge];
                break;
            }

            $rootComposerJson[$sectionToMerge] = $merged[$sectionToMerge];
        }

        foreach ($this->composerJsonDecorators as $composerJsonDecorator) {
            $rootComposerJson = $composerJsonDecorator->decorate($rootComposerJson);
        }

        file_put_contents('composer.json', Json::encode($rootComposerJson, Json::PRETTY) . PHP_EOL);

        $this->symfonyStyle->success('Main "composer.json" was updated.');

        // success
        return 0;
    }

    /**
     * @return SplFileInfo[]
     */
    private function getPackageComposerFiles(): array
    {
        $iterator = Finder::create()
            ->files()
            ->in(getcwd() . '/packages')
            ->name('composer.json')
            ->getIterator();

        return iterator_to_array($iterator);
    }
}
