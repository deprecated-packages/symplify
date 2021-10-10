<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ActiveClass\Command;

use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\SmartFileSystem\Finder\SmartFinder;
use Symplify\SmartFileSystem\SmartFileInfo;

final class CheckActiveClassCommand extends Command
{
    public function __construct(
        private SmartFinder $smartFinder,
        private \Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Check classes that are not used in any config and in the code');

        $this->addArgument(
            Option::SOURCES,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'One or more paths with templates'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sources = (array) $input->getArgument(Option::SOURCES);
        $phpFileInfos = $this->smartFinder->find($sources, '*.php', ['Fixture', 'Source', 'tests']);

        $uniqueUseImports = $this->resolveUseImportsFromFileInfos($phpFileInfos);
        $uniqueUsedNeonServices = $this->resolveUsedServicesInNeonConfigs($sources);

        $allClassUses = array_merge($uniqueUseImports, $uniqueUsedNeonServices);

        $possiblyUnusedClasses = [];

        $checkClassNames = [];

        foreach ($phpFileInfos as $phpFileInfo) {
            $className = $this->resolveClassNameFromFileInfo($phpFileInfo);
            if ($className === null) {
                continue;
            }

            $checkClassNames[] = $className;

            if (in_array($className, $allClassUses, true)) {
                continue;
            }

            $possiblyUnusedClasses[] = $className;
        }

        if ($possiblyUnusedClasses === []) {
            $errorMessage = sprintf('All the %d services from %d files are used. Great job!', count($checkClassNames), count($phpFileInfos));
            $this->symfonyStyle->success($errorMessage);
            return self::SUCCESS;
        }

        // @todo

        $this->symfonyStyle->listing($possiblyUnusedClasses);

        $errorMessage = sprintf(
            'Found %d unused classes. Check them, remove them or correct the command.',
            count($possiblyUnusedClasses)
        );

        $this->symfonyStyle->error($errorMessage);

        return self::FAILURE;
    }

    private function resolveClassNameFromFileInfo(SmartFileInfo $phpFileInfo): ?string
    {
        // get class name
        $namespaceMatch = Strings::match($phpFileInfo->getContents(), '#^namespace (?<namespace>.*?);$#m');
        if (!isset($namespaceMatch['namespace'])) {
            return null;
        }

        $classLikeMatch = Strings::match($phpFileInfo->getContents(), '#^(final ?)(class|interface|trait) (?<class_like_name>[\w_]+)#ms');
        if (!isset($classLikeMatch['class_like_name'])) {
            return null;
        }

        return $namespaceMatch['namespace'] . '\\' . $classLikeMatch['class_like_name'];
    }

    /**
     * @param SmartFileInfo[] $phpFileInfos
     * @return string[]
     */
    private function resolveUseImportsFromFileInfos(array $phpFileInfos): array
    {
        $useImports = [];

        foreach ($phpFileInfos as $phpFileInfo) {
            $matches = Strings::matchAll($phpFileInfo->getContents(), '#^use (?<used_class>.*?);$#ms');

            foreach ($matches as $match) {
                $useImports[] = $match['used_class'];
            }
        }

        $uniqueUseImports = array_unique($useImports);
        sort($uniqueUseImports);

        return $uniqueUseImports;
    }

    /**
     * @param string[] $sources
     * @return string[]
     */
    private function resolveUsedServicesInNeonConfigs(array $sources): array
    {
        $neonFileInfos = $this->smartFinder->find($sources, '*.neon', ['Fixture', 'Source', 'tests']);

        $usedServices = [];

        foreach ($neonFileInfos as $neonFileInfo) {
            $classMatches = Strings::matchAll($neonFileInfo->getContents(), '#class: (?<class_name>.*?)$#ms');
            foreach ($classMatches as $classMatch) {
                $usedServices[] = $classMatch['class_name'];
            }

            $bulledClassMatches = Strings::matchAll($neonFileInfo->getContents(), '#- (?<class_name>.*?)$#ms');
            foreach ($bulledClassMatches as $bulletClassMatch) {
                $usedServices[] = $bulletClassMatch['class_name'];
            }
        }

        $usedServices = array_unique($usedServices);
        sort($usedServices);

        return $usedServices;
    }
}
