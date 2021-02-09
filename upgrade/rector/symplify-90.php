<?php

declare(strict_types=1);

use Rector\Renaming\Rector\Name\RenameClassRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(RenameClassRector::class)
        ->call('configure', [[
            RenameClassRector::OLD_TO_NEW_CLASSES => [
                'Symplify\PackageBuilder\Tests\AbstractKernelTestCase' => 'Symplify\PackageBuilder\Testing\AbstractKernelTestCase',
                'Symplify\PHPStanRules\Rules\ForbiddenAssignInifRule' => 'Symplify\PHPStanRules\Rules\ForbiddenAssignInIfRule',
                'Symplify\PHPStanRules\Rules\NoFunctionCallInMethodCallRule' => 'Symplify\PHPStanRules\Rules\NoFuncCallInMethodCallRule',
                'Symplify\ChangelogLinker\Regex\RegexPattern' => 'Symplify\ChangelogLinker\ValueObject\RegexPattern',
                'Symplify\ChangelogLinker\Configuration\Category' => 'Symplify\ChangelogLinker\ValueObject\Category',
                'Symplify\ChangelogLinker\Configuration\PackageName' => 'Symplify\ChangelogLinker\ValueObject\PackageName',
                'Symplify\CodingStandard\Fixer\Spacing\RemoveSpacingAroundModifierAndConstFixer' => 'SlevomatCodingStandard\Sniffs\Whitespaces\DuplicateSpacesSniff',
                'Symplify\CodingStandard\Fixer\Commenting\RemoveSuperfluousDocBlockWhitespaceFixer' => 'PhpCsFixer\Fixer\Phpdoc\PhpdocTrimConsecutiveBlankLineSeparationFixer',

                // phpstan-rules renames
                // see https://github.com/symplify/symplify/pull/2462 - not 1:1 namespace renames
                'Symplify\CodingStandard\Rules\NoDynamicMethodNameRule' => 'Symplify\PHPStanRules\Rules\NoDynamicNameRule',
                'Symplify\CodingStandard\Rules\PreferredClassConstantOverVariableConstantRule' => 'Symplify\PHPStanRules\Rules\NoDynamicNameRule',

                'Symplify\CodingStandard\Rules\NoTraitExceptItsMethodsPublicAndRequiredRule' => 'Symplify\PHPStanRules\Rules\NoTraitRule',
                'Symplify\CodingStandard\Rules\CheckRequiredMethodTobeAutowireWithClassNameRule' => 'Symplify\PHPStanRules\Rules\CheckRequiredMethodNamingRule',
                'Symplify\CodingStandard\Rules\PrefferedStaticCallOverFuncCallRule' => 'Symplify\PHPStanRules\Rules\PreferredStaticCallOverFuncCallRule',
                'Symplify\CodingStandard\Rules\NoFunctionCallInMethodCallRule' => 'Symplify\PHPStanRules\Rules\NoFuncCallInMethodCallRule',
                'Symplify\CodingStandard\Rules\NoDynamicPropertyFetchNameRule' => 'Symplify\PHPStanRules\Rules\NoDynamicNameRule',
                'Symplify\CodingStandard\Rules\PrefferedMethodCallOverFuncCallRule' => 'Symplify\PHPStanRules\Rules\PreferredMethodCallOverFuncCallRule',
                'Symplify\CodingStandard\Rules\ForbiddenMethodCallInIfRule' => 'Symplify\PHPStanRules\Rules\ForbiddenMethodOrStaticCallInIfRule',
                // an "PhpParser\Node\Empty_" must be added to the list, see
                'Symplify\CodingStandard\Rules\NoEmptyRule' => 'Symplify\PHPStanRules\Rules\ForbiddenNodeRule'
            ],
        ]]);
};
