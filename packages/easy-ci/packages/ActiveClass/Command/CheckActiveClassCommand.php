<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ActiveClass\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCI\ActiveClass\ClassNameResolver;
use Symplify\EasyCI\ActiveClass\UsedNeonServicesResolver;
use Symplify\EasyCI\ActiveClass\UseImportsResolver;
use Symplify\EasyCI\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\SmartFileSystem\Finder\SmartFinder;
use Symplify\SmartFileSystem\SmartFileInfo;

final class CheckActiveClassCommand extends Command
{
    /**
     * @var string[]
     */
    private const EXCLUDED_TYPES = [ConfigurableRuleInterface::class];

    public function __construct(
        private SmartFinder $smartFinder,
        private SymfonyStyle $symfonyStyle,
        private ClassNameResolver $classNameResolver,
        private UseImportsResolver $useImportsResolver,
        private UsedNeonServicesResolver $usedNeonServicesResolver,
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

        $phpFileInfos = $this->smartFinder->find($sources, '*.php', ['Fixture', 'Source', 'tests', 'stubs']);
        $uniqueUseImports = $this->useImportsResolver->resolveFromFileInfos($phpFileInfos);

        $neonFileInfos = $this->smartFinder->find($sources, '*.neon', ['Fixture', 'Source', 'tests']);
        $uniqueUsedNeonServices = $this->usedNeonServicesResolver->resolveFormFileInfos($neonFileInfos);

        $allClassUses = array_merge($uniqueUseImports, $uniqueUsedNeonServices);

        $checkClassNames = $this->resolveClassNamesToCheck($phpFileInfos);

        $possiblyUnusedClasses = $this->resolvePossiblyUnusedClasses($checkClassNames, $allClassUses);

        if ($possiblyUnusedClasses === []) {
            $errorMessage = sprintf(
                'All the %d services from %d files are used. Great job!',
                count($checkClassNames),
                count($phpFileInfos)
            );
            $this->symfonyStyle->success($errorMessage);
            return self::SUCCESS;
        }

        $this->symfonyStyle->listing($possiblyUnusedClasses);

        $errorMessage = sprintf(
            'Found %d unused classes. Check them, remove them or correct the command.',
            count($possiblyUnusedClasses)
        );

        $this->symfonyStyle->error($errorMessage);

        return self::FAILURE;
    }

    /**
     * @param SmartFileInfo[] $phpFileInfos
     * @return string[]
     */
    private function resolveClassNamesToCheck(array $phpFileInfos): array
    {
        $checkClassNames = [];

        foreach ($phpFileInfos as $phpFileInfo) {
            $className = $this->classNameResolver->resolveFromFromFileInfo($phpFileInfo);
            if ($className === null) {
                continue;
            }

            $checkClassNames[] = $className;
        }

        return $checkClassNames;
    }

    /**
     * @param string[] $checkClassNames
     * @param string[] $allClassUses
     * @return string[]
     */
    private function resolvePossiblyUnusedClasses(array $checkClassNames, array $allClassUses): array
    {
        $possiblyUnusedClasses = [];

        foreach ($checkClassNames as $checkClassName) {
            if (in_array($checkClassName, $allClassUses, true)) {
                continue;
            }

            // is excluded interfaces?
            foreach (self::EXCLUDED_TYPES as $excludedType) {
                if (is_a($checkClassName, $excludedType, true)) {
                    continue 2;
                }
            }

            $possiblyUnusedClasses[] = $checkClassName;
        }

        return $possiblyUnusedClasses;
    }
}
