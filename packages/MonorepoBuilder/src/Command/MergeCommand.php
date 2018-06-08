<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Command;

use Composer\Json\JsonManipulator;
use Iterator;
use Nette\Utils\Json;
use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\MonorepoBuilder\Contract\ComposerJsonDecoratorInterface;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;

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

    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;

        parent::__construct();
    }

    public function addComposerJsonDecorator(ComposerJsonDecoratorInterface $composerJsonDecorator): void
    {
        $this->composerJsonDecorators[] = $composerJsonDecorator;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $composerPackageFiles = $this->getPackageComposerFiles();
        if (! count($composerPackageFiles)) {
            $this->symfonyStyle->error('No "composer.json" were found in packagse.');
            return 1;
        }



        // ...
        $sectionsToMerge = ['require', 'require-dev'];

        $collected = [];

        $extraItemsPerSection['require-dev'] = [
            'phpstan/phpstan' => '^0.9',
            'tracy/tracy' => '^2.4',
            'slam/php-cs-fixer-extensions' => '^1.15',
        ];

        $removeItemsPerSection['require'] = ['phpunit/phpunit', 'tracy/tracy'];

        foreach ($composerPackageFiles as $packageFile) {
            $packageComposerJson = Json::decode($packageFile->getContents(), Json::FORCE_ARRAY);

            foreach ($sectionsToMerge as $sectionToMerge) {
                if (! isset($packageComposerJson[$sectionToMerge])) {
                    continue;
                }

                $collected[$sectionToMerge] = array_merge(
                    $collected[$sectionToMerge] ?? [],
                    $packageComposerJson[$sectionToMerge]
                );
            }
        }

        $rootComposerJsonContent = file_get_contents(getcwd() . '/composer.json');
        $rootComposerJson = Json::decode($rootComposerJsonContent, Json::FORCE_ARRAY);

        foreach ($this->composerJsonDecorators as $composerJsonDecorator) {
            dump('EEE');
        }


        foreach ($sectionsToMerge as $sectionToMerge) {
            // nothing collected to merge
            if (! isset($collected[$sectionToMerge])) {
                continue;
            }

            // section in root composer.json is empty, just set and go
            if (! isset($rootComposerJson[$sectionToMerge])) {
                $rootComposerJson[$sectionToMerge] = $collected[$sectionToMerge];
                break;
            }

            $collected[$sectionToMerge] = $this->filterOut($collected[$sectionToMerge]);

            if (isset($extraItemsPerSection[$sectionToMerge])) {
                $collected[$sectionToMerge] += $extraItemsPerSection[$sectionToMerge];
            }

            if (isset($removeItemsPerSection[$sectionToMerge])) {
                foreach ($removeItemsPerSection[$sectionToMerge] as $itemToRemove) {
                    foreach ($collected[$sectionToMerge] as $item => $value) {
                        if ($item === $itemToRemove) {
                            unset($collected[$sectionToMerge][$item]);
                        }
                    }
                }
            }

            if (isset($rootComposerJson['config']['sort-packages']) && in_array(
                $sectionToMerge,
                ['require', 'require-dev'],
                true
            )) {
                $collected[$sectionToMerge] = $this->sortPackages($collected[$sectionToMerge]);
            }

            $rootComposerJson[$sectionToMerge] = $collected[$sectionToMerge];
        }

        file_put_contents('composer.json', Json::encode($rootComposerJson, Json::PRETTY) . PHP_EOL);

        $this->symfonyStyle->success('Main "composer.json" was updated.');

        // success
        return 0;
    }

    /**
     * @param string[] $packages
     * @return string[]
     */
    private function sortPackages(array $packages): array
    {
        return (new PrivatesCaller())->callPrivateMethodWithReference(
            JsonManipulator::class,
            'sortPackages',
            $packages
        );
    }

    /**
     * @param mixed[] $packages
     * @return mixed[]
     */
    private function filterOut(array $packages): array
    {
        foreach ($packages as $name => $version) {
            if (Strings::startsWith($name, 'symplify')) {
                unset($packages[$name]);
            }
        }

        return $packages;
    }

    /**
     * @return SplFileInfo[]
     */
    protected function getPackageComposerFiles(): array
    {
        $iterator = Finder::create()
            ->files()
            ->in(getcwd() . '/packages')
            ->name('composer.json')
            ->getIterator();

        return iterator_to_array($iterator);
    }
}
