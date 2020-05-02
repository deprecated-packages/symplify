<?php

use Nette\Loaders\RobotLoader;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\Fixer\Commenting\RemoveEmptyDocBlockFixer;
use Symplify\CodingStandard\Fixer\Commenting\RemoveEndOfFunctionCommentFixer;
use Symplify\CodingStandard\Fixer\ControlStructure\PregDelimiterFixer;
use Symplify\CodingStandard\Fixer\ControlStructure\RequireFollowedByAbsolutePathFixer;
use Symplify\CodingStandard\Fixer\Naming\CatchExceptionNameMatchingTypeFixer;
use Symplify\CodingStandard\Fixer\Property\BoolPropertyDefaultValueFixer;
use Symplify\CodingStandard\Fixer\Solid\FinalInterfaceFixer;
use Symplify\CodingStandard\Sniffs\Architecture\ExplicitExceptionSniff;
use Symplify\CodingStandard\Sniffs\CleanCode\ClassCognitiveComplexitySniff;
use Symplify\CodingStandard\Sniffs\CleanCode\CognitiveComplexitySniff;
use Symplify\CodingStandard\Sniffs\CleanCode\ForbiddenParentClassSniff;
use Symplify\CodingStandard\Sniffs\CleanCode\ForbiddenStaticFunctionSniff;
use Symplify\CodingStandard\Sniffs\Commenting\AnnotationTypeExistsSniff;
use Symplify\CodingStandard\Sniffs\ControlStructure\ForbiddenDoubleAssignSniff;
use Symplify\CodingStandard\Sniffs\Naming\AbstractClassNameSniff;
use Symplify\CodingStandard\Sniffs\Naming\InterfaceNameSniff;
use Symplify\CodingStandard\Sniffs\Naming\TraitNameSniff;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;

require __DIR__ . '/../vendor/autoload.php';

$codingStandardSyncChecker = new CodingStandardSyncChecker();
$codingStandardSyncChecker->run();


final class CodingStandardSyncChecker
{
    /**
     * @var string
     */
    private const CODING_STANDARD_README_PATH = __DIR__ . '/../packages/coding-standard/README.md';

    /**
     * @see https://regex101.com/r/Unygf7/2/
     * @var string
     */
    private const CHECKER_CLASS_PATTERN = '#`(?<checker_class>Symplify\\\\CodingStandard.*?(Fixer|Sniff))`#';

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct()
    {
        $symfonyStyleFactory = new SymfonyStyleFactory();
        $this->symfonyStyle = $symfonyStyleFactory->create();
    }

    public function run(): void
    {
        $readmeCheckerClasses = $this->resolveCheckerClassesInReadme();

        $existingCheckerClasses = $this->getExistingCheckerClasses();
        $missingCheckerClasses = array_diff($existingCheckerClasses, $readmeCheckerClasses);

        if ($missingCheckerClasses === []) {
            die(ShellCode::SUCCESS);
        }

        $this->symfonyStyle->error(sprintf('Complete %d checkers to CodingStandard README.md', count($missingCheckerClasses)));
        $this->symfonyStyle->listing($missingCheckerClasses);

        die(ShellCode::ERROR);
    }

    /**
     * @return string[]
     */
    private function resolveCheckerClassesInReadme(): array
    {
        $codingStandardReadmeContent = FileSystem::read(self::CODING_STANDARD_README_PATH);

        $checkerClassMatches = Strings::matchAll($codingStandardReadmeContent, self::CHECKER_CLASS_PATTERN);

        $checkerClasses = [];
        foreach ($checkerClassMatches as $checkerClassMatch) {
            $checkerClasses[] = $checkerClassMatch['checker_class'];
        }

        $checkerClasses = array_unique($checkerClasses);
        sort($checkerClasses);
        return $checkerClasses;
    }

    /**
     * @return string[]
     */
    private function getExistingCheckerClasses(): array
    {
        $robotLoader = new RobotLoader();
        $robotLoader->setTempDirectory(sys_get_temp_dir() . '/coding_standard_readme_sync');

        $robotLoader->addDirectory(__DIR__ . '/../packages/coding-standard/src/Fixer');
        $robotLoader->addDirectory(__DIR__ . '/../packages/coding-standard/src/Sniffs');
        $robotLoader->acceptFiles = ['*Sniff.php', '*Fixer.php'];
        $robotLoader->rebuild();

        $existingCheckerRules = array_keys($robotLoader->getIndexedClasses());
        sort($existingCheckerRules);

        $classesToExclude = [
            // abstract
            AbstractSymplifyFixer::class,
            // deprecated
            AbstractClassNameSniff::class,
            InterfaceNameSniff::class,
            TraitNameSniff::class,
            RemoveEndOfFunctionCommentFixer::class,
            FinalInterfaceFixer::class,
            PregDelimiterFixer::class,
            RequireFollowedByAbsolutePathFixer::class,
            CatchExceptionNameMatchingTypeFixer::class,
            CognitiveComplexitySniff::class,
            ClassCognitiveComplexitySniff::class,
            ForbiddenStaticFunctionSniff::class,
            RemoveEmptyDocBlockFixer::class,
            ForbiddenDoubleAssignSniff::class,
            ForbiddenParentClassSniff::class,
            ExplicitExceptionSniff::class,
            BoolPropertyDefaultValueFixer::class,
            AnnotationTypeExistsSniff::class
        ];

        // filter out abstract class
        foreach ($existingCheckerRules as $key => $existingCheckerRule) {
            if (! in_array($existingCheckerRule, $classesToExclude, true)) {
                continue;
            }

            unset($existingCheckerRules[$key]);
        }

        return $existingCheckerRules;
    }
}
