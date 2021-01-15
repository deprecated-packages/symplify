<!-- changelog-linker -->

## 9.0.36 - 2021-01-15

### Added

#### Astral

- [#2768] Add NodeValueResolverTest, add TypeChecker
- [#2769] Add NodeValueResolverTest, add TypeChecker

#### AutowireArrayParameter

- [#2786] Add array shape support

#### CI

- [#2754] add split_repository lockdown with message

#### CodingStandard

- [#2774] Add promoted property newliner

#### EasyTesting

- [#2733] Add validate fixture skip naming command

#### PHPStanRules

- [#2740] Add InvokableControllerByRouteNamingRule, Thanks to [@samsonasik]
- [#2771] Add NoEmptyClassRule
- [#2732] Add unskipped test fixture
- [#2777] Add RequireConstantInAttributeArgumentRule
- [#2792] Add DifferentMethodNameToParameterRule
- [#2729] Add RequireSkipPrefixForRuleSkippedFixtureRule
- [#2788] Add ForbiddenNestedCallInAssertMethodCallRule

#### Unknown Package

- [#2753] Added methods to manipulate json + fix license type, Thanks to [@lulco]

### Changed

#### CI

- [#2755] reword lockdown and disable lockign to allow chat

#### ChangeLogLinker

- [#2767] Failing test ChangelogFileSystem with changelog file has previous text before , Thanks to [@samsonasik]

#### ChangelogLinker

- [#2731] Failing fixture multi list data changelog should not trimmed, Thanks to [@samsonasik]

#### EasyTesting

- [#2735] Update wording error on ValidateFixtureSkipNamingCommand, Thanks to [@samsonasik]
- [#2736] Check ---- in ValidateFixtureSkipNaming Command, Thanks to [@samsonasik]

#### PHPStanRules

- [#2744] Improve PreventDuplicateClassMethodRule with mask names, Thanks to [@samsonasik]
- [#2800] Skip bracket path for NoMissingDirPathRule
- [#2795] Skip check trait method content on PreventDuplicateClassMethodRule, Thanks to [@samsonasik]
- [#2791] use ReflectionProvider where possible
- [#2779] Don't trigger an error when the method returns an empty array, Thanks to [@matthiasnoback]
- [#2763] Re-use naming service

#### PHPUnit

- [#2789] Migrate phpunit.xml configuration with --migrate-configuration, Thanks to [@samsonasik]

#### PackageBuilder

- [#2742] Warn about null call on private

#### Rector

- [#2761] Apply early return set

#### RuleDocGenerator

- [#2734] Split comma on KeywordHighlighter, Thanks to [@samsonasik]

#### Unknown Package

- [#2739] typo, Thanks to [@dakur]
- [#2727] Automated Update of Changelog on 2021-01-01, Thanks to [@github-actions][bot]
- [#2756] Apply Rector rules
- [#2778] Tests for issue [#2776], Thanks to [@annuh]
- [#2764] Re-use node name resolver
- [#2765] use node value resolver

#### automated

- [#2737] Re-Generate Docs, Thanks to [@github-actions][bot]
- [#2796] Re-Generate Docs, Thanks to [@github-actions][bot]
- [#2730] Re-Generate Docs, Thanks to [@github-actions][bot]
- [#2783] Re-Generate Docs, Thanks to [@github-actions][bot]
- [#2772] Re-Generate Docs, Thanks to [@github-actions][bot]
- [#2738] Re-Propagate Composer Dependencies to Packages, Thanks to [@github-actions][bot]
- [#2745] Re-Generate Docs, Thanks to [@github-actions][bot]
- [#2766] Re-Generate Docs, Thanks to [@github-actions][bot]

#### changelog-linker

- [#2747] Include none if no pull requests have been merged since provided last id, Thanks to [@alexbrouwer]
- [#2750] Prevent duplication in changelog when only PR no. 1 was merged, Thanks to [@alexbrouwer]

### Deprecated

#### Autodiscovery

- [#2802] Deprecated package

#### FlexLoader

- [#2801] Deprecate unused package

### Fixed

#### Unknown Package

- [#2762] various fixes
- [#2780] Fix typo: excpeted → expected, Thanks to [@alexislefebvre]

### Removed

#### Astral

- [#2770] Remove dependnecy on phpstan

#### CodingStandard

- [#2741] Remove reference on [@param] for ParamReturnAndVarTagMalformsFixer, Thanks to [@samsonasik]

#### Unknown Package

- [#2785] Remove direct dependency on slevomat/coding-standard for breaking installs and PHP 8 code

## [9.0.24] - 2021-01-03# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

PRs and issues are linked, so you can find more about it. Thanks to [ChangelogLinker](https://github.com/symplify/changelog-linker).

### Added

#### Astral
- [#2710] Add new package- [#2719] Add builders

#### ConfigTransformer
- [#2673] Add support for sniff/fixer parameters

#### PHPStanRules
- [#2666] Add PreventDuplicateClassMethodRule, Thanks to [@samsonasik]- [#2701] Add IfImplementsInterfaceThenNewTypeRule- [#2703] Add ForbiddenNullableParameterRule, replaces overly strict NoNullableParameterRule- [#2689] Add RequireThisCallOnLocalMethodRule, Thanks to [@samsonasik]- [#2705] Add null default check to forbidden null type- [#2706] Add variable mallform fix of inlined var without variable name- [#2679] Add RequireStringRegexMatchKeyRule, Thanks to [@samsonasik]- [#2723] Add NoNullableArrayPropertyRule, Thanks to [@samsonasik]- [#2653] Add RequireQuoteStringValueSprintfRule, Thanks to [@samsonasik]- [#2725] Add ForbiddenAnonymousClassRule, Thanks to [@samsonasik]

#### PackageScoper
- [#2708] Add clean-dev-files command

#### Unknown Package
- [#2711] Added "branch-alias", Thanks to [@leoloso]

#### cs
- [#2696] add back class separation rules

### Changed

#### CodingStandard
- [#2675] Skip if without end- [#2676] Skip impossible start

#### PHPStanRules
- [#2672] Refactor CheckTypehintCallerTypeRule from instanceof to PHPStan types- [#2645] Refactor PreventDoubleSetParameterRule to use SymfonyPhpConfigClosureAnalyzer- [#2670] Skip callables in dynamic naming- [#2668] Skip callables in dynamic naming- [#2680] Refactor CheckUsedNamespacedNameOnClassNodeRule to skip null compare- [#2663] ensure no superflous / or \ in check isInDirectoryNamed(), Thanks to [@samsonasik]- [#2724] Skip ClassNameRespectsParentSuffixRule on anonymous class, Thanks to [@samsonasik]

#### Unknown Package
- [#2660] Failing test case for multiple ## [9.0.24] - 2021-01-03 against , Thanks to [@samsonasik]- [#2649] Automated Update of Changelog on 2020-12-15, Thanks to [@github-actions][bot]- [#2665] Ensure namespacedName property exists when call $Class_->namespacedName, Thanks to [@samsonasik]- [#2685] Duplicate trait- [#2690] Improved documentation (to avoid error), Thanks to [@leoloso]- [#2692] [monorepo-builder "localize-composer-paths"] Extract $packageComposerFiles from out of loop, Thanks to [@leoloso]- [#2714] [Monorepo Builder] Avoid duplicating entries in composer.json's repositories, Thanks to [@leoloso]

#### automated
- [#2726] Re-Generate Docs, Thanks to [@github-actions][bot]- [#2677] Re-Generate Docs, Thanks to [@github-actions][bot]- [#2686] Re-Generate Docs, Thanks to [@github-actions][bot]- [#2687] Re-Generate Docs, Thanks to [@github-actions][bot]- [#2651] Re-Generate Docs, Thanks to [@github-actions][bot]- [#2691] Re-Generate Docs, Thanks to [@github-actions][bot]- [#2657] Re-Generate Docs, Thanks to [@github-actions][bot]- [#2709] Re-Generate Docs, Thanks to [@github-actions][bot]

#### monorepo-builder
- [#2712] Enable symlink => true in composer.json repository for local package, Thanks to [@leoloso]

### Fixed

#### PHPStanRules
- [#2720] Fix CheckClassNamespaceFollowPsr4Rule for multi-dir- [#2659] Fixes CheckClassNamespaceFollowPsr4Rule check is in named directory in Windows Operating System, Thanks to [@samsonasik]- [#2681] Fix BoolishClassMethodPrefixRule for nested scope- [#2704] Fix commented print node- [#2684] Fix CheckUsedNamespacedNameOnClassNodeRule for being assigned into- [#2671] Fix comparing node calls- [#2688] Fix duplicate methods for traits- [#2699] Fix parent interface require by new

#### Unknown Package
- [#2667] Fixed some typos in Package Scoper's README, Thanks to [@leoloso]

### Removed

#### PHPStanRules
- [#2656] Remove composer type, Thanks to [@enumag]- [#2655] Remove auto-installation with phpstan/extension-installer, Thanks to [@enumag]

## [9.0.11] - 2020-12-15

### Added

#### EasyCI
- [#2607] Add php-json to README- [#2592] Add php-json command to provide currently support PHP versions

#### PHPStanRules
- [#2601] Add OnlyOneClassMethodRule- [#2628] Skip EAB parent class + Add on release update of composer.json replace- [#2643] Add PreventDoubleSetParameterRule, Thanks to [@samsonasik]- [#2576] Fixes [#2037] Add CheckDependencyMatrixRule, Thanks to [@samsonasik]- [#2610] Add CheckConstantStringValueFormatRule- [#2569] Fixes [#2523] Add CheckClassNamespaceFollowPsr4Rule, Thanks to [@samsonasik]- [#2623] Add IfNewTypeThenImplementInterfaceRule- [#2626] Add RequireInvokableControllerRule- [#2591] Add ForbiddenMethodCallOnTypeRule- [#2630] Add PreferredAttributeOverAnnotationRule

#### Unknown Package
- [#2604] Add AuthorComposerKeyMerger, Thanks to [@ruudk]- [#2616] Add ShouldNotHappenException for EasyCI, Thanks to [@ruudk]

### Changed

#### CI
- [#2594] Handle packges-json in key-aware approach

#### CodingStandard
- [#2638] Merge split dead commenting remover Fixers to one

#### ComposerJsonManipulator
- [#2605] Export `authors`, `type` and `conflicting`, Thanks to [@ruudk]

#### MonorepoBuilder
- [#2629] Make use of ComposerJsonManipulator

#### PHPStanRules
- [#2577] Skip anonymous class in CheckClassNamespaceFollowPsr4Rule- [#2645] Refactor PreventDoubleSetParameterRule to use SymfonyPhpConfigClosureAnalyzer- [#2611] Change CheckDependencyMatrixRule to AllowedExclusiveDependencyRule- [#2632] Move set up- [#2573] Skip attribute in array string keys- [#2619] Rename NoParticularNodeRule to ForbiddenNodeRule to respect common wording- [#2620] Rename ForbiddenConstructorDependencyByTypeRule to ForbiddenDependencyByTypeRule- [#2636] Improve NoSetterOnServiceRule, skip parent setter interface- [#2590] Skip strings with spaces in CheckConstantStringValueFormatRule- [#2633] Skip intentionally comment in ForbiddenNodeRule

#### PackageBuilder
- [#2595] Use getService() object API

#### RuleDocGenerator
- [#2588] Configuratoin cannot be empty

#### Skipper
- [#2624] Complete docs

#### Unknown Package
- [#2572] Automated Update of Changelog on 2020-12-01, Thanks to [@github-actions][bot]- [#2640] typo- [#2603] run split on tag- [#2575] Bump min version to PHP 7.3

#### automated
- [#2597] Re-Generate Docs, Thanks to [@github-actions][bot]- [#2585] Re-Generate Docs, Thanks to [@github-actions][bot]- [#2646] Re-Generate Docs, Thanks to [@github-actions][bot]- [#2598] Re-Propagate Composer Dependencies to Packages, Thanks to [@github-actions][bot]- [#2602] Re-Generate Docs, Thanks to [@github-actions][bot]- [#2613] Re-Generate Docs, Thanks to [@github-actions][bot]- [#2627] Re-Generate Docs, Thanks to [@github-actions][bot]- [#2567] Re-Apply Coding Standards, Thanks to [@github-actions][bot]- [#2635] Re-Generate Docs, Thanks to [@github-actions][bot]- [#2566] Re-Generate Docs, Thanks to [@github-actions][bot]- [#2642] Re-Generate Docs, Thanks to [@github-actions][bot]- [#2582] Re-Generate Docs, Thanks to [@github-actions][bot]

### Deprecated

#### EasyCodingStandard
- [#2614] Remove deprecated Option class

### Fixed
- [#2625] Fix init command

#### PHPStanRules
- [#2564] Fixes node->getAttribute() got null on CheckTypehintCallerTypeRule, Thanks to [@samsonasik]- [#2617] Fix doc typo

#### RuleDocGenerator
- [#2583] Fixes rectorphp/rector[#4756] AbstractCodeSample parameter order, Thanks to [@samsonasik]

#### Unknown Package
- [#2580] Fix typo in Github Action, Thanks to [@ruudk]- [#2596] Fix typo, Thanks to [@staabm]- [#2615] Fix typo, Thanks to [@staabm]- [#2609] Fix typo, Thanks to [@staabm]

### Removed

#### PHPStanRules
- [#2621] Drop NoEntityManagerInControllerRule, use AllowedExclusiveDependencyRule instead; merge duplicate AllowsExclusiveDependencyRule to ExclusiveDependencyRule- [#2634] Remove NoStaticCallRule, completely useless

#### Unknown Package
- [#2571] Drop support for ref() and inline(), Thanks to [@marforon]- [#2641] drop ignore-platform-reqs

## [9.0.0-rc1] - 2020-12-05

### Added

#### CodingStandard
- [#2498] Fixes [#2493] Add RemoveUselessClassCommentFixer, Thanks to [@samsonasik]- [#2522] Fixes [#2517] Add Handle useless method comment in RemoveUselessClassCommentFixer, Thanks to [@samsonasik]

#### ComposerJsonManipulator
- [#2494] Add type

#### ConfigTransformer
- [#2539] Add new package

#### EasyCI
- [#2528] Add a new package

#### LatteToTwig
- [#2531] Add a new package

#### PHPConfigPrinter
- [#2527] Add a new package

#### PHPStanRules
- [#2550] Fixes [#2330] Add ForbiddenCallOnTypeRule, Thanks to [@samsonasik]- [#2529] Add foreach over nested- [#2557] Fixes [#2548] Add CheckOptionArgumentCommandRule, Thanks to [@samsonasik]- [#2499] Add configuration to NoChainMethodCallRule- [#2559] Fixes [#2327] Add CheckTypehintCallerTypeRule, Thanks to [@samsonasik]- [#2515] Add RequireClassTypeInClassMethodByTypeRule

#### PHPUnitUpgrader
- [#2537] Add new package

#### RuleDocGeneator
- [#2510] Add ComposerJsonAwareCodeSample

#### RuleDocGenerator
- [#2511] Add ExtraFileCodeSampler- [#2509] Add Rector print support- [#2547] Add count to the top + category

#### SimplePhpDocParser
- [#2552] Add new package

#### Skipper
- [#2553] Various paths improvements, add shouldSkipElement() method- [#2546] Add SkippedPathsResolver- [#2495] Add a fix for PathNormalizer if Path contains "..", Thanks to [@tomasnorre]

#### StaticDetector
- [#2533] Add new package

#### SymfonyPhpConfig
- [#2549] Add a new package

#### TemplateChecker
- [#2535] Add a new package

#### Unknown Package
- [#2544] README: add Symplify 9 packages to list- [#2536] [PSR-4 Switcher] Add a new package

#### VendorPatches
- [#2534] Add a new package

### Changed

#### CI
- [#2496] Move coding standards from ci-reivew to daily-prs- [#2497] Move ci-review + rector-ci to daily PRs to speedup contributions

#### ClassPresence
- [#2541] Init new packages

#### ConsolePackageBuilder
- [#2542] Init a new package

#### EasyHydrator
- [#2521] ScalarTypeCaster supports float, Thanks to [@janatjak]

#### LatteToTwigConverter
- [#2532] Make name more explicit

#### NeonToYamlConverter
- [#2538] Init a new package

#### PHPStanRules
- [#2513] Make ForbiddenMethodOrStaticCallInIfRule skip trinary logic- [#2505] Allow parsing parent classes from phar- [#2503] Skip spready in RobotLoader addDirectory- [#2500] Make ClassLikeCognitiveComplexityRule configurable by class type- [#2512] Allow match in calls- [#2568] Improve CheckTypehintCallerTypeRule : skip non private and multiple usages, Thanks to [@samsonasik]

#### RuleDocGenerator
- [#2506] Allow multiple dirs

#### Skipper
- [#2545] Decouple to collector- [#2543] decouple FileInfoMatcher, OnlySkipper

#### SnifferFixerToECSConverter
- [#2540] Init a new package

#### SymfonyPhpConfig
- [#2551] Move from functions to static class

#### Unknown Package
- [#2492] Automated Update of Changelog on 2020-11-15, Thanks to [@github-actions][bot]- [#2501] Define package as PHPStan Extension, Thanks to [@szepeviktor]- [#2558] phpstan typo in readme, Thanks to [@alexcutts]- [#2554] normalize readme

#### automated
- [#2566] Re-Generate Docs, Thanks to [@github-actions][bot]- [#2562] Re-Apply Coding Standards, Thanks to [@github-actions][bot]- [#2561] Re-Generate Docs, Thanks to [@github-actions][bot]- [#2556] Re-Generate Docs, Thanks to [@github-actions][bot]- [#2504] Re-Apply Rector Rules, Thanks to [@github-actions][bot]- [#2567] Re-Apply Coding Standards, Thanks to [@github-actions][bot]- [#2514] Re-Apply Rector Rules, Thanks to [@github-actions][bot]

#### phpstan
- [#2525] limit class complexity by type

### Fixed

#### CodingStandard
- [#2519] Fixes [#2517] improve RemoveUselessClassCommentFixer : remove useless constructor comment, Thanks to [@samsonasik]

#### EasyHydrator
- [#2520] ObjectTypeCaster fix indexed array of objects, Thanks to [@janatjak]

#### PHPStanRules
- [#2563] Fixes to ensure check next node instanceof Identifier on CheckUsedNamespacedNameOnClassNodeRule, Thanks to [@samsonasik]- [#2564] Fixes node->getAttribute() got null on CheckTypehintCallerTypeRule, Thanks to [@samsonasik]

#### SymfonyStaticDumper
- [#2555] Fixes [#2108] : Document usage for controllers with arguments, Thanks to [@samsonasik]

#### Unknown Package
- [#2502] Fix link in README to work in a monorepo too, Thanks to [@szepeviktor]- [#2524] Fix typo in code sample, Thanks to [@staabm]

## [9.0.0-BETA2] - 2020-11-15

### Added

#### CI
- [#2468] Drop coding standard doc check, will be replaced by RuleDocGenerator + add rule definitions for sniffs/fixers

#### CodingStandard
- [#2457] Fixes [#2208] Add CheckConstantExpressionDefinedInConstructOrSetupRule, Thanks to [@samsonasik]- [#2483] Fixes [#2482] Add RemovePHPStormTodoImplementMethodCommentFixer, Thanks to [@samsonasik]- [#2481] Fixes [#2480] Add RemovePHPStormTodoCommentFixer, Thanks to [@samsonasik]

#### PHPStanRules
- [#2456] Fixes [#2404] Add ForbiddenPrivateMethodByTypeRule, Thanks to [@samsonasik]- [#2466] Fixes [#2405] Add ForbiddenMethodCallByTypeInLocationRule, Thanks to [@samsonasik]- [#2476] Add ExclusiveDependencyRule, Thanks to [@samsonasik]

#### Unknown Package
- [#2475] add missing nette/neon package

### Changed

#### CI
- [#2453] What if Github Actions can handle the split of packages?- [#2448] Automated CHANGELOG generation once 2 weeks

#### CodingStandard
- [#2469] Generate rules_overview file- [#2455] Improve CheckConstantStringValueFormatRule : Allow array constant value, Thanks to [@samsonasik]

#### DX
- [#2478] Decouple private methods from commands

#### EasyCodingStandard
- [#2442] Switch prefixed ecs.phar to automated package scoping in GitHub Action

#### MarkdownDiffer
- [#2470] Init new package

#### MonorepoBuilder
- [#2477] Move from manual package list to json list- [#2454] Prepare for split command deprectaion

#### PHPStanPHPConfig
- [#2464] Init new package with phpstan.php

#### PHPStanRules
- [#2445] update ManyNodeRuleInterface location- [#2462] Decorated with Rule Doc Generator interface and move examples to the code- [#2458] Fail for CheckConstantExpressionDefinedInConstructOrSetupRule- [#2461] Improve CheckConstantExpressionDefinedInConstructOrSetupRule- [#2473] Generate the docs

#### PackageBuilder
- [#2452] Accept also file info configs

#### Unknown Package
- [#2449] Automated Update of Changelog on 2020-11-03, Thanks to [@github-actions][bot]

### Fixed

#### CodingStandard
- [#2467] Fixes [#2425] Register RemoveUselessJustForSakeInterfaceRector into rector-ci.php, Thanks to [@samsonasik]

#### DX
- [#2486] Fixes [#2485] enable PreferThisOrSelfMethodCallRector, Thanks to [@samsonasik]

#### Unknown Package
- [#2459] fix typo, Thanks to [@staabm]- [#2460] Fix typos, Thanks to [@staabm]- [#2474] Markdown format fixes

### Removed

#### MonorepoBuilder
- [#2490] Drop split command, delegate to GitHub Action with less magic

#### Unknown Package
- [#2451] drop manual setName(), let applicaton handle that

## [9.0.0-BETA1] - 2020-11-14

### Added

#### CodingStandard
- [#2362] Fixes [#2301] Add ForbiddenNewInMethodRule, Thanks to [@samsonasik]- [#2394] Fixes [#2372] Add forbidden curl functions to symplify-rules.neon, Thanks to [@samsonasik]- [#2265] Fixes [#2261] Add Check Required `abstract` Keyword for Class Name Start with Abstract, Thanks to [@samsonasik]- [#2266] Fixes [#2238] : Add Check Unneeded SymfonyStyle usage for only newline, write(ln) rule, Thanks to [@samsonasik]- [#2357] Add NoSuffixValueObjectClassRule- [#2403] Fixes [#2373] : Add CheckParentChildMethodParameterTypeCompatibleRule, Thanks to [@samsonasik]- [#2270] Add MethodChainingNewlineFixer- [#2401] Fixes [#2349] : Add ForbiddenConstructorDependencyByTypeRule, Thanks to [@samsonasik]- [#2274] Add function call skip to MethodChainingNewlineFixer- [#2275] Add skip for opened call to MethodChainingNewlineFixer- [#2395] Fixes [#2347] Add CheckRequiredAutowireAutoconfigurePublicUsedInConfigServiceRule, Thanks to [@samsonasik]- [#2276] Fixes [#2207] : Add NoParentMethodCallOnEmptyStatementInParentMethod rule, Thanks to [@samsonasik]- [#2252] Fixes [#2176] : Add No factory in constructor rule, Thanks to [@samsonasik]- [#2388] Fixes [#2381] Add ForbiddenMethodCallInIfRule, Thanks to [@samsonasik]- [#2280] Add case with curly opener- [#2292] Add ForbiddenNodeRule- [#2298] Fixes [#2226] add PreferredRawDataInTestDataProvider, Thanks to [@samsonasik]- [#2317] Add trait/class difference in CheckRequiredMethodTobeAutowireWithClassNameRule- [#2379] Fixes [#2378] Add ForbiddenMethodOrFuncCallInForeachRule, Thanks to [@samsonasik]- [#2377] Add other exprs to PreferredClassConstantOverVariableConstantRule- [#2366] Fixes [#2365] Add ForbidNewOutsideFactoryServiceRule, Thanks to [@samsonasik]- [#2355] Add failing class with anonymous- [#2356] Fixes [#2331] Add ForbiddenProtectedPropertyRule, Thanks to [@samsonasik]- [#2264] Add ArrayListItemNewlineFixer- [#2269] Fixes [#2207] : Add No Parent Method Call On No Override Process Rule, Thanks to [@samsonasik]- [#2250] Add SuffixInterfaceRule- [#2229] Fixes [#2172] : Add NoTraitExceptItsMethodsRequired Rule, Thanks to [@samsonasik]- [#2206] Fix [#2199] : add NoScalarAndArrayConstructorParameterRule to prevent config coding, Thanks to [@samsonasik]- [#2443] Fixes [#2350] Add CheckConstantStringValueFormatRule, Thanks to [@samsonasik]- [#2212] Add allowed classes to NoScalarAndArrayConstructorParameterRule- [#2436] Fixes [#2417] Add CheckRequiredInterfaceInContractNamespaceRule, Thanks to [@samsonasik]- [#2215] Improve performance for ArrayOpenerNewlineFixer + add closer support- [#2433] Fixes [#2432] : Add ForbiddenSpreadOperatorRule, Thanks to [@samsonasik]- [#2424] Fixes [#2352] : Add RequireNewArgumentConstantRule, Thanks to [@samsonasik]- [#2419] Fixes [#2204] : Add CheckTraitMethodOnlyDelegateOtherClassRule, Thanks to [@samsonasik]- [#2249] Add SuffixTraitRule- [#2423] Fixes [#2422] : Add ForbiddenMethodCallOnNewRule, Thanks to [@samsonasik]- [#2248] Add PrefixAbstractClassRule- [#2241] add markdown array open/close fixutre

#### DX
- [#2316] Fixes [#2287] Add regex links to PHPStan reported constants, Thanks to [@samsonasik]

#### EasyCI
- [#2211] Add Sonar config generator

#### EasyCodingStandard
- [#2409] Add --match-git-diff option

#### EasyHydrator
- [#2430] ParameterTypeRecognizer added support for array|null union type, Thanks to [@janatjak]- [#2431] added ArrayTypeCaster - allow retype array of scalars, Thanks to [@janatjak]

#### EasyTesting
- [#2222] Add splitFileInfoToLocalInputAndExpected()

#### MonorepoBuilder
- [#2302] Add validation for directories and repositories parameter for split- [#2437] Add scoped-only prefixed version + New package for package scoping- [#2389] Add DIRECTORIES_TO_REPOSITORIES_CONVERT_FORMAT option for pascal case directory names- [#2391] Add number test- [#2398] Add check-split-test-workflow command

#### PHPStanRules
- [#2447] Add example, fix typo, Thanks to [@staabm]

#### PackageBuilder
- [#2202] Add provideIntParameter()

#### PackageScoper
- [#2439] Add 2 more commands for config generating

#### Packages
- [#2232] Add paypal sponsor link

#### Unknown Package
- [#2239] Fixes [#2177] : add CheckRequiredMethodTobeAutowireWithClassName rule, Thanks to [@samsonasik]- [#2257] Fixes [#2243] : Add No Setter on Service rule, Thanks to [@samsonasik]- [#2320] add init command to ECS, Thanks to [@Kerrialn]- [#2324] remove .idea and add it to .gitignore, Thanks to [@samsonasik]- [#2340] Fixes [#2332] Add RequireThisOnParentMethodCallRule, Thanks to [@samsonasik]- [#2344] Fixes [#2247] : Add ForbiddenNestedForeachWithEmptyStatementRule, Thanks to [@samsonasik]- [#2348] Fixes [#2167] add RequireConstantInMethodCallPositionRule, Thanks to [@samsonasik]- [#2351] Fixes [#2343] : Add ForbiddenMultipleClassLikeInOneFileRule, Thanks to [@samsonasik]- - [#2361] Fixes [#2342] : Add NoMethodTagInClassDocblockRule, Thanks to [@samsonasik]

#### ci
- [#2410] make rector_ci run use auto commit action + add ci-review- [#2427] add composer install cache

#### cs
- [#2319] Fixes [#2214] : add PrefferedMethodCallOverFuncCallRule, Thanks to [@samsonasik]- [#2375] Fixes [#2329] Add TooDeepNewClassNestingRule, Thanks to [@samsonasik]- [#2341] Fixes [#2328] Add ForbiddenAssignInIfRule, Thanks to [@samsonasik]

### Changed

#### CodingStandard
- [#2385] Rename Forbid to Forbidden prefix in Rule name, Thanks to [@samsonasik]- [#2315] Skip array return if required by parent method of class/interface- [#2223] Skip Event and Entity for scalar in constructor- [#2267] Rename ForceMethodCallArgumentConstantRule to RequireMethodCallArgumentConstantRule- [#2384] Protected rules improvements- [#2407] Improve line length fixer + array break fixer combination- [#2237] Make symplify-rules easier to copy- [#2288] Skip variable in RequireMethodCallArgumentConstantRule- [#2286] PHPStan rules improvoments- [#2285] Get rules from container for tests if possible- [#2282] Various PHPStan rules improvements- [#2387] Improve ForbiddenMethodOrFuncCallInForeachRule : Allow empty args, Thanks to [@samsonasik]- [#2291] Skip exception in NoParentMethodCallOnEmptyStatementInParentMethodRule- [#2307] Allow uuid factory static call- [#2339] Skip array square wrap over multi chain call- [#2209] Update NoScalarAndArrayConstructorParameterRule to work with doc types- [#2203] Resolve FQN name for class in NoProtectedElementInFinalClassRule

#### EasyCodingStandard
- [#2213] Enable debug progress bar on -v- [#2313] Make system errors in separated stream with SystemError object- [#2245] Use i/o json testing

#### EasyHydrator
- [#2360] Make use rector split package- [#2255] DateTimeImmutable resolver (symplify[#2254]), Thanks to [@vyacheslav-startsev]- [#2416] Support for objects with default constructor values, Thanks to [@JanMikes]- [#2195] Support for hydrating nested objects and typed arrays, Thanks to [@JanMikes]

#### MonorepoBuilder
- [#2259] Composer repository priority, Thanks to [@NoorAdiana]- [#2392] Merge ConvertFormat to main test

#### PHPStanRules
- [#2444] Split PHPStan rules only package from CodingStandard package- [#2445] update ManyNodeRuleInterface location

#### Skipper
- [#2420] Decouple new package

#### SmartFileSystem
- [#2227] Sort names by SmartFinder- [#2231] Sort names by SmartFinder

#### SymplifyKernel
- [#2294] Init

#### Unknown Package
- [#2359] improve CheckUsedNamespacedNameOnClassNodeRule, Thanks to [@samsonasik]- [#2402] Provisional support for PHP 8.0, Thanks to [@zingimmick]- [#2408] make sure it passes- [#2358] improve ForbiddenProtectedPropertyRule, Thanks to [@samsonasik]- [#2435] Various changes- [#2311] use DI over manual instantiation- - [#2364] Update README.md, Thanks to [@ThomasLandauer]- [#2303] various static improvement- [#2368] Update README.md, Thanks to [@ThomasLandauer]- [#2369] Update README.md, Thanks to [@ThomasLandauer]- [#2289] change HelpfulApplicationTrait to AbstractSymplifyConsoleApplication- [#2370] reactivate coverage report, Thanks to [@samsonasik]- [#2386] Use DIRECTORY_SEPARATOR constant instead of `/`, Thanks to [@ComiR]- [#2374] improve ForbiddenProtectedPropertyRule : handle injection with [@required] and autowire, Thanks to [@samsonasik]- [#2304] typo- [#2210] update regex link

#### cs
- [#2335] Enable CheckUnneededSymfonyStyleUsageRule in symplify-rules.neon, Thanks to [@samsonasik]

#### travis
- [#2429] use composer v2 for phar compilers

### Deprecated

#### AutoBindParameter
- [#2235] Deprecate, use ParameterProvider instead

#### Autodiscovery
- [#2446] Deprecate ConvertYamlCommand, already part of symplify/config-feature-bumper

#### EasyCodingStandard
- [#2412] Deprecate YAML configs, use PHP instead

### Fixed

#### ChangelogLinker
- [#2333] Fixes [#2013] Fixes multiple Unreleased sections, Thanks to [@samsonasik]

#### CodingStandard
- [#2284] Fixes [#2253] : Prefer class::constant over $variable::constant, Thanks to [@samsonasik]- [#2290] Fix chain indenxt on multi with arg- [#2314] Fix intersection for RequireMethodCallArgumentConstantRule- [#2380] Protected and factory PHPStan fixes- [#2308] Various PHPStan rule fixes- [#2325] Fixes [#2318] : Bug with CheckUnneededSymfonyStyleUsageRule that requires to use MethodCall with $scope context, Thanks to [@samsonasik]- [#2383] fix NoProtectedElementInFinalClassRule for trait- [#2293] Fix previous func call in chain- [#2281] Fixes [#2277] Fixes false positive CheckUnneededSymfonyStyleUsageRule, Thanks to [@samsonasik]- [#2279] Fixes [#2278] handle False positive in case of parent interface/trait in NoProtectedElementInFinalClassRule, Thanks to [@samsonasik]- [#2371] fix new anonymous for ForbiddenNewInMethodRule- [#2216] Improve memory on array fixes

#### EasyCodingStandard
- [#2297] Fix missing exception- [#2224] Fixes [#2219] : Fixes StandardizeHereNowDocKeywordFixer indentation closing heredoc/nowdoc to not be removed, Thanks to [@samsonasik]

#### EasyHydrator
- [#2396] Fixes [#2393] AutowireArrayParameterCompilerPass, Thanks to [@janatjak]- [#2428] fix TypeCastersCollector::$typeCasters sort, Thanks to [@janatjak]- [#2397] fix missing ParameterTypeRecognizer dependencies, Thanks to [@janatjak]

#### MonorepoBuilder
- [#2337] Fix propagate- [#2246] Fixes [#2139] ensure check file exists of monorepo-builder.yaml, fallback to monorepo-builder.php, Thanks to [@samsonasik]- [#2353] Fix absolute to relative paths

#### PackageBuilder
- [#2438] fix DefinitionFinder class not found, Thanks to [@janatjak]

#### SFS
- [#2413] Fixed skipping rules fails on Windows system ([#2399]), Thanks to [@BoGnY]

#### Unknown Package
- [#2336] Fixes [#2234] use namespaceName on $class node, Thanks to [@samsonasik]- [#2441] ecs/readme: fix fdefault typo, Thanks to [@glensc]- [#2305] fixes- [#2310] Typo fix: errorMessaeg -> errorMessage, Thanks to [@samsonasik]- [#2271] Typo fix: NoAbstactMethodRule should be NoAbstractMethodRule, Thanks to [@samsonasik]- [#2299] various fixes- [#2272] Fixes [#2225] : No __construct, only setUp() in tests, Thanks to [@samsonasik]

### Removed

#### ChangelogLinker
- [#2418] Drop YAML, use PHP

#### EasyCodingStandard
- [#2411] drop YAML support

#### EasyHydrator
- [#2426] Drop unused ParameterValueGetterInterface

#### Unknown Package
- [#2300] remove ctor factory- [#2323] Init command feature - remove .idea, Thanks to [@Kerrialn]- [#2406] Drop dependency on Composer

[#4756]: https://github.com/symplify/symplify/pull/4756
[#2646]: https://github.com/symplify/symplify/pull/2646
[#2645]: https://github.com/symplify/symplify/pull/2645
[#2643]: https://github.com/symplify/symplify/pull/2643
[#2642]: https://github.com/symplify/symplify/pull/2642
[#2641]: https://github.com/symplify/symplify/pull/2641
[#2640]: https://github.com/symplify/symplify/pull/2640
[#2638]: https://github.com/symplify/symplify/pull/2638
[#2636]: https://github.com/symplify/symplify/pull/2636
[#2635]: https://github.com/symplify/symplify/pull/2635
[#2634]: https://github.com/symplify/symplify/pull/2634
[#2633]: https://github.com/symplify/symplify/pull/2633
[#2632]: https://github.com/symplify/symplify/pull/2632
[#2630]: https://github.com/symplify/symplify/pull/2630
[#2629]: https://github.com/symplify/symplify/pull/2629
[#2628]: https://github.com/symplify/symplify/pull/2628
[#2627]: https://github.com/symplify/symplify/pull/2627
[#2626]: https://github.com/symplify/symplify/pull/2626
[#2625]: https://github.com/symplify/symplify/pull/2625
[#2624]: https://github.com/symplify/symplify/pull/2624
[#2623]: https://github.com/symplify/symplify/pull/2623
[#2621]: https://github.com/symplify/symplify/pull/2621
[#2620]: https://github.com/symplify/symplify/pull/2620
[#2619]: https://github.com/symplify/symplify/pull/2619
[#2617]: https://github.com/symplify/symplify/pull/2617
[#2616]: https://github.com/symplify/symplify/pull/2616
[#2615]: https://github.com/symplify/symplify/pull/2615
[#2614]: https://github.com/symplify/symplify/pull/2614
[#2613]: https://github.com/symplify/symplify/pull/2613
[#2611]: https://github.com/symplify/symplify/pull/2611
[#2610]: https://github.com/symplify/symplify/pull/2610
[#2609]: https://github.com/symplify/symplify/pull/2609
[#2607]: https://github.com/symplify/symplify/pull/2607
[#2605]: https://github.com/symplify/symplify/pull/2605
[#2604]: https://github.com/symplify/symplify/pull/2604
[#2603]: https://github.com/symplify/symplify/pull/2603
[#2602]: https://github.com/symplify/symplify/pull/2602
[#2601]: https://github.com/symplify/symplify/pull/2601
[#2598]: https://github.com/symplify/symplify/pull/2598
[#2597]: https://github.com/symplify/symplify/pull/2597
[#2596]: https://github.com/symplify/symplify/pull/2596
[#2595]: https://github.com/symplify/symplify/pull/2595
[#2594]: https://github.com/symplify/symplify/pull/2594
[#2592]: https://github.com/symplify/symplify/pull/2592
[#2591]: https://github.com/symplify/symplify/pull/2591
[#2590]: https://github.com/symplify/symplify/pull/2590
[#2588]: https://github.com/symplify/symplify/pull/2588
[#2585]: https://github.com/symplify/symplify/pull/2585
[#2583]: https://github.com/symplify/symplify/pull/2583
[#2582]: https://github.com/symplify/symplify/pull/2582
[#2580]: https://github.com/symplify/symplify/pull/2580
[#2577]: https://github.com/symplify/symplify/pull/2577
[#2576]: https://github.com/symplify/symplify/pull/2576
[#2575]: https://github.com/symplify/symplify/pull/2575
[#2573]: https://github.com/symplify/symplify/pull/2573
[#2572]: https://github.com/symplify/symplify/pull/2572
[#2571]: https://github.com/symplify/symplify/pull/2571
[#2569]: https://github.com/symplify/symplify/pull/2569
[#2568]: https://github.com/symplify/symplify/pull/2568
[#2567]: https://github.com/symplify/symplify/pull/2567
[#2566]: https://github.com/symplify/symplify/pull/2566
[#2564]: https://github.com/symplify/symplify/pull/2564
[#2563]: https://github.com/symplify/symplify/pull/2563
[#2562]: https://github.com/symplify/symplify/pull/2562
[#2561]: https://github.com/symplify/symplify/pull/2561
[#2559]: https://github.com/symplify/symplify/pull/2559
[#2558]: https://github.com/symplify/symplify/pull/2558
[#2557]: https://github.com/symplify/symplify/pull/2557
[#2556]: https://github.com/symplify/symplify/pull/2556
[#2555]: https://github.com/symplify/symplify/pull/2555
[#2554]: https://github.com/symplify/symplify/pull/2554
[#2553]: https://github.com/symplify/symplify/pull/2553
[#2552]: https://github.com/symplify/symplify/pull/2552
[#2551]: https://github.com/symplify/symplify/pull/2551
[#2550]: https://github.com/symplify/symplify/pull/2550
[#2549]: https://github.com/symplify/symplify/pull/2549
[#2548]: https://github.com/symplify/symplify/pull/2548
[#2547]: https://github.com/symplify/symplify/pull/2547
[#2546]: https://github.com/symplify/symplify/pull/2546
[#2545]: https://github.com/symplify/symplify/pull/2545
[#2544]: https://github.com/symplify/symplify/pull/2544
[#2543]: https://github.com/symplify/symplify/pull/2543
[#2542]: https://github.com/symplify/symplify/pull/2542
[#2541]: https://github.com/symplify/symplify/pull/2541
[#2540]: https://github.com/symplify/symplify/pull/2540
[#2539]: https://github.com/symplify/symplify/pull/2539
[#2538]: https://github.com/symplify/symplify/pull/2538
[#2537]: https://github.com/symplify/symplify/pull/2537
[#2536]: https://github.com/symplify/symplify/pull/2536
[#2535]: https://github.com/symplify/symplify/pull/2535
[#2534]: https://github.com/symplify/symplify/pull/2534
[#2533]: https://github.com/symplify/symplify/pull/2533
[#2532]: https://github.com/symplify/symplify/pull/2532
[#2531]: https://github.com/symplify/symplify/pull/2531
[#2529]: https://github.com/symplify/symplify/pull/2529
[#2528]: https://github.com/symplify/symplify/pull/2528
[#2527]: https://github.com/symplify/symplify/pull/2527
[#2525]: https://github.com/symplify/symplify/pull/2525
[#2524]: https://github.com/symplify/symplify/pull/2524
[#2523]: https://github.com/symplify/symplify/pull/2523
[#2522]: https://github.com/symplify/symplify/pull/2522
[#2521]: https://github.com/symplify/symplify/pull/2521
[#2520]: https://github.com/symplify/symplify/pull/2520
[#2519]: https://github.com/symplify/symplify/pull/2519
[#2517]: https://github.com/symplify/symplify/pull/2517
[#2515]: https://github.com/symplify/symplify/pull/2515
[#2514]: https://github.com/symplify/symplify/pull/2514
[#2513]: https://github.com/symplify/symplify/pull/2513
[#2512]: https://github.com/symplify/symplify/pull/2512
[#2511]: https://github.com/symplify/symplify/pull/2511
[#2510]: https://github.com/symplify/symplify/pull/2510
[#2509]: https://github.com/symplify/symplify/pull/2509
[#2506]: https://github.com/symplify/symplify/pull/2506
[#2505]: https://github.com/symplify/symplify/pull/2505
[#2504]: https://github.com/symplify/symplify/pull/2504
[#2503]: https://github.com/symplify/symplify/pull/2503
[#2502]: https://github.com/symplify/symplify/pull/2502
[#2501]: https://github.com/symplify/symplify/pull/2501
[#2500]: https://github.com/symplify/symplify/pull/2500
[#2499]: https://github.com/symplify/symplify/pull/2499
[#2498]: https://github.com/symplify/symplify/pull/2498
[#2497]: https://github.com/symplify/symplify/pull/2497
[#2496]: https://github.com/symplify/symplify/pull/2496
[#2495]: https://github.com/symplify/symplify/pull/2495
[#2494]: https://github.com/symplify/symplify/pull/2494
[#2493]: https://github.com/symplify/symplify/pull/2493
[#2492]: https://github.com/symplify/symplify/pull/2492
[#2490]: https://github.com/symplify/symplify/pull/2490
[#2486]: https://github.com/symplify/symplify/pull/2486
[#2485]: https://github.com/symplify/symplify/pull/2485
[#2483]: https://github.com/symplify/symplify/pull/2483
[#2482]: https://github.com/symplify/symplify/pull/2482
[#2481]: https://github.com/symplify/symplify/pull/2481
[#2480]: https://github.com/symplify/symplify/pull/2480
[#2478]: https://github.com/symplify/symplify/pull/2478
[#2477]: https://github.com/symplify/symplify/pull/2477
[#2476]: https://github.com/symplify/symplify/pull/2476
[#2475]: https://github.com/symplify/symplify/pull/2475
[#2474]: https://github.com/symplify/symplify/pull/2474
[#2473]: https://github.com/symplify/symplify/pull/2473
[#2470]: https://github.com/symplify/symplify/pull/2470
[#2469]: https://github.com/symplify/symplify/pull/2469
[#2468]: https://github.com/symplify/symplify/pull/2468
[#2467]: https://github.com/symplify/symplify/pull/2467
[#2466]: https://github.com/symplify/symplify/pull/2466
[#2464]: https://github.com/symplify/symplify/pull/2464
[#2462]: https://github.com/symplify/symplify/pull/2462
[#2461]: https://github.com/symplify/symplify/pull/2461
[#2460]: https://github.com/symplify/symplify/pull/2460
[#2459]: https://github.com/symplify/symplify/pull/2459
[#2458]: https://github.com/symplify/symplify/pull/2458
[#2457]: https://github.com/symplify/symplify/pull/2457
[#2456]: https://github.com/symplify/symplify/pull/2456
[#2455]: https://github.com/symplify/symplify/pull/2455
[#2454]: https://github.com/symplify/symplify/pull/2454
[#2453]: https://github.com/symplify/symplify/pull/2453
[#2452]: https://github.com/symplify/symplify/pull/2452
[#2451]: https://github.com/symplify/symplify/pull/2451
[#2449]: https://github.com/symplify/symplify/pull/2449
[#2448]: https://github.com/symplify/symplify/pull/2448
[#2447]: https://github.com/symplify/symplify/pull/2447
[#2446]: https://github.com/symplify/symplify/pull/2446
[#2445]: https://github.com/symplify/symplify/pull/2445
[#2444]: https://github.com/symplify/symplify/pull/2444
[#2443]: https://github.com/symplify/symplify/pull/2443
[#2442]: https://github.com/symplify/symplify/pull/2442
[#2441]: https://github.com/symplify/symplify/pull/2441
[#2439]: https://github.com/symplify/symplify/pull/2439
[#2438]: https://github.com/symplify/symplify/pull/2438
[#2437]: https://github.com/symplify/symplify/pull/2437
[#2436]: https://github.com/symplify/symplify/pull/2436
[#2435]: https://github.com/symplify/symplify/pull/2435
[#2433]: https://github.com/symplify/symplify/pull/2433
[#2432]: https://github.com/symplify/symplify/pull/2432
[#2431]: https://github.com/symplify/symplify/pull/2431
[#2430]: https://github.com/symplify/symplify/pull/2430
[#2429]: https://github.com/symplify/symplify/pull/2429
[#2428]: https://github.com/symplify/symplify/pull/2428
[#2427]: https://github.com/symplify/symplify/pull/2427
[#2426]: https://github.com/symplify/symplify/pull/2426
[#2425]: https://github.com/symplify/symplify/pull/2425
[#2424]: https://github.com/symplify/symplify/pull/2424
[#2423]: https://github.com/symplify/symplify/pull/2423
[#2422]: https://github.com/symplify/symplify/pull/2422
[#2420]: https://github.com/symplify/symplify/pull/2420
[#2419]: https://github.com/symplify/symplify/pull/2419
[#2418]: https://github.com/symplify/symplify/pull/2418
[#2417]: https://github.com/symplify/symplify/pull/2417
[#2416]: https://github.com/symplify/symplify/pull/2416
[#2413]: https://github.com/symplify/symplify/pull/2413
[#2412]: https://github.com/symplify/symplify/pull/2412
[#2411]: https://github.com/symplify/symplify/pull/2411
[#2410]: https://github.com/symplify/symplify/pull/2410
[#2409]: https://github.com/symplify/symplify/pull/2409
[#2408]: https://github.com/symplify/symplify/pull/2408
[#2407]: https://github.com/symplify/symplify/pull/2407
[#2406]: https://github.com/symplify/symplify/pull/2406
[#2405]: https://github.com/symplify/symplify/pull/2405
[#2404]: https://github.com/symplify/symplify/pull/2404
[#2403]: https://github.com/symplify/symplify/pull/2403
[#2402]: https://github.com/symplify/symplify/pull/2402
[#2401]: https://github.com/symplify/symplify/pull/2401
[#2399]: https://github.com/symplify/symplify/pull/2399
[#2398]: https://github.com/symplify/symplify/pull/2398
[#2397]: https://github.com/symplify/symplify/pull/2397
[#2396]: https://github.com/symplify/symplify/pull/2396
[#2395]: https://github.com/symplify/symplify/pull/2395
[#2394]: https://github.com/symplify/symplify/pull/2394
[#2393]: https://github.com/symplify/symplify/pull/2393
[#2392]: https://github.com/symplify/symplify/pull/2392
[#2391]: https://github.com/symplify/symplify/pull/2391
[#2389]: https://github.com/symplify/symplify/pull/2389
[#2388]: https://github.com/symplify/symplify/pull/2388
[#2387]: https://github.com/symplify/symplify/pull/2387
[#2386]: https://github.com/symplify/symplify/pull/2386
[#2385]: https://github.com/symplify/symplify/pull/2385
[#2384]: https://github.com/symplify/symplify/pull/2384
[#2383]: https://github.com/symplify/symplify/pull/2383
[#2381]: https://github.com/symplify/symplify/pull/2381
[#2380]: https://github.com/symplify/symplify/pull/2380
[#2379]: https://github.com/symplify/symplify/pull/2379
[#2378]: https://github.com/symplify/symplify/pull/2378
[#2377]: https://github.com/symplify/symplify/pull/2377
[#2375]: https://github.com/symplify/symplify/pull/2375
[#2374]: https://github.com/symplify/symplify/pull/2374
[#2373]: https://github.com/symplify/symplify/pull/2373
[#2372]: https://github.com/symplify/symplify/pull/2372
[#2371]: https://github.com/symplify/symplify/pull/2371
[#2370]: https://github.com/symplify/symplify/pull/2370
[#2369]: https://github.com/symplify/symplify/pull/2369
[#2368]: https://github.com/symplify/symplify/pull/2368
[#2366]: https://github.com/symplify/symplify/pull/2366
[#2365]: https://github.com/symplify/symplify/pull/2365
[#2364]: https://github.com/symplify/symplify/pull/2364
[#2362]: https://github.com/symplify/symplify/pull/2362
[#2361]: https://github.com/symplify/symplify/pull/2361
[#2360]: https://github.com/symplify/symplify/pull/2360
[#2359]: https://github.com/symplify/symplify/pull/2359
[#2358]: https://github.com/symplify/symplify/pull/2358
[#2357]: https://github.com/symplify/symplify/pull/2357
[#2356]: https://github.com/symplify/symplify/pull/2356
[#2355]: https://github.com/symplify/symplify/pull/2355
[#2353]: https://github.com/symplify/symplify/pull/2353
[#2352]: https://github.com/symplify/symplify/pull/2352
[#2351]: https://github.com/symplify/symplify/pull/2351
[#2350]: https://github.com/symplify/symplify/pull/2350
[#2349]: https://github.com/symplify/symplify/pull/2349
[#2348]: https://github.com/symplify/symplify/pull/2348
[#2347]: https://github.com/symplify/symplify/pull/2347
[#2344]: https://github.com/symplify/symplify/pull/2344
[#2343]: https://github.com/symplify/symplify/pull/2343
[#2342]: https://github.com/symplify/symplify/pull/2342
[#2341]: https://github.com/symplify/symplify/pull/2341
[#2340]: https://github.com/symplify/symplify/pull/2340
[#2339]: https://github.com/symplify/symplify/pull/2339
[#2337]: https://github.com/symplify/symplify/pull/2337
[#2336]: https://github.com/symplify/symplify/pull/2336
[#2335]: https://github.com/symplify/symplify/pull/2335
[#2333]: https://github.com/symplify/symplify/pull/2333
[#2332]: https://github.com/symplify/symplify/pull/2332
[#2331]: https://github.com/symplify/symplify/pull/2331
[#2330]: https://github.com/symplify/symplify/pull/2330
[#2329]: https://github.com/symplify/symplify/pull/2329
[#2328]: https://github.com/symplify/symplify/pull/2328
[#2327]: https://github.com/symplify/symplify/pull/2327
[#2325]: https://github.com/symplify/symplify/pull/2325
[#2324]: https://github.com/symplify/symplify/pull/2324
[#2323]: https://github.com/symplify/symplify/pull/2323
[#2320]: https://github.com/symplify/symplify/pull/2320
[#2319]: https://github.com/symplify/symplify/pull/2319
[#2318]: https://github.com/symplify/symplify/pull/2318
[#2317]: https://github.com/symplify/symplify/pull/2317
[#2316]: https://github.com/symplify/symplify/pull/2316
[#2315]: https://github.com/symplify/symplify/pull/2315
[#2314]: https://github.com/symplify/symplify/pull/2314
[#2313]: https://github.com/symplify/symplify/pull/2313
[#2311]: https://github.com/symplify/symplify/pull/2311
[#2310]: https://github.com/symplify/symplify/pull/2310
[#2308]: https://github.com/symplify/symplify/pull/2308
[#2307]: https://github.com/symplify/symplify/pull/2307
[#2305]: https://github.com/symplify/symplify/pull/2305
[#2304]: https://github.com/symplify/symplify/pull/2304
[#2303]: https://github.com/symplify/symplify/pull/2303
[#2302]: https://github.com/symplify/symplify/pull/2302
[#2301]: https://github.com/symplify/symplify/pull/2301
[#2300]: https://github.com/symplify/symplify/pull/2300
[#2299]: https://github.com/symplify/symplify/pull/2299
[#2298]: https://github.com/symplify/symplify/pull/2298
[#2297]: https://github.com/symplify/symplify/pull/2297
[#2294]: https://github.com/symplify/symplify/pull/2294
[#2293]: https://github.com/symplify/symplify/pull/2293
[#2292]: https://github.com/symplify/symplify/pull/2292
[#2291]: https://github.com/symplify/symplify/pull/2291
[#2290]: https://github.com/symplify/symplify/pull/2290
[#2289]: https://github.com/symplify/symplify/pull/2289
[#2288]: https://github.com/symplify/symplify/pull/2288
[#2287]: https://github.com/symplify/symplify/pull/2287
[#2286]: https://github.com/symplify/symplify/pull/2286
[#2285]: https://github.com/symplify/symplify/pull/2285
[#2284]: https://github.com/symplify/symplify/pull/2284
[#2282]: https://github.com/symplify/symplify/pull/2282
[#2281]: https://github.com/symplify/symplify/pull/2281
[#2280]: https://github.com/symplify/symplify/pull/2280
[#2279]: https://github.com/symplify/symplify/pull/2279
[#2278]: https://github.com/symplify/symplify/pull/2278
[#2277]: https://github.com/symplify/symplify/pull/2277
[#2276]: https://github.com/symplify/symplify/pull/2276
[#2275]: https://github.com/symplify/symplify/pull/2275
[#2274]: https://github.com/symplify/symplify/pull/2274
[#2272]: https://github.com/symplify/symplify/pull/2272
[#2271]: https://github.com/symplify/symplify/pull/2271
[#2270]: https://github.com/symplify/symplify/pull/2270
[#2269]: https://github.com/symplify/symplify/pull/2269
[#2267]: https://github.com/symplify/symplify/pull/2267
[#2266]: https://github.com/symplify/symplify/pull/2266
[#2265]: https://github.com/symplify/symplify/pull/2265
[#2264]: https://github.com/symplify/symplify/pull/2264
[#2261]: https://github.com/symplify/symplify/pull/2261
[#2259]: https://github.com/symplify/symplify/pull/2259
[#2257]: https://github.com/symplify/symplify/pull/2257
[#2255]: https://github.com/symplify/symplify/pull/2255
[#2254]: https://github.com/symplify/symplify/pull/2254
[#2253]: https://github.com/symplify/symplify/pull/2253
[#2252]: https://github.com/symplify/symplify/pull/2252
[#2250]: https://github.com/symplify/symplify/pull/2250
[#2249]: https://github.com/symplify/symplify/pull/2249
[#2248]: https://github.com/symplify/symplify/pull/2248
[#2247]: https://github.com/symplify/symplify/pull/2247
[#2246]: https://github.com/symplify/symplify/pull/2246
[#2245]: https://github.com/symplify/symplify/pull/2245
[#2243]: https://github.com/symplify/symplify/pull/2243
[#2241]: https://github.com/symplify/symplify/pull/2241
[#2239]: https://github.com/symplify/symplify/pull/2239
[#2238]: https://github.com/symplify/symplify/pull/2238
[#2237]: https://github.com/symplify/symplify/pull/2237
[#2235]: https://github.com/symplify/symplify/pull/2235
[#2234]: https://github.com/symplify/symplify/pull/2234
[#2232]: https://github.com/symplify/symplify/pull/2232
[#2231]: https://github.com/symplify/symplify/pull/2231
[#2229]: https://github.com/symplify/symplify/pull/2229
[#2227]: https://github.com/symplify/symplify/pull/2227
[#2226]: https://github.com/symplify/symplify/pull/2226
[#2225]: https://github.com/symplify/symplify/pull/2225
[#2224]: https://github.com/symplify/symplify/pull/2224
[#2223]: https://github.com/symplify/symplify/pull/2223
[#2222]: https://github.com/symplify/symplify/pull/2222
[#2219]: https://github.com/symplify/symplify/pull/2219
[#2216]: https://github.com/symplify/symplify/pull/2216
[#2215]: https://github.com/symplify/symplify/pull/2215
[#2214]: https://github.com/symplify/symplify/pull/2214
[#2213]: https://github.com/symplify/symplify/pull/2213
[#2212]: https://github.com/symplify/symplify/pull/2212
[#2211]: https://github.com/symplify/symplify/pull/2211
[#2210]: https://github.com/symplify/symplify/pull/2210
[#2209]: https://github.com/symplify/symplify/pull/2209
[#2208]: https://github.com/symplify/symplify/pull/2208
[#2207]: https://github.com/symplify/symplify/pull/2207
[#2206]: https://github.com/symplify/symplify/pull/2206
[#2204]: https://github.com/symplify/symplify/pull/2204
[#2203]: https://github.com/symplify/symplify/pull/2203
[#2202]: https://github.com/symplify/symplify/pull/2202
[#2199]: https://github.com/symplify/symplify/pull/2199
[#2195]: https://github.com/symplify/symplify/pull/2195
[#2177]: https://github.com/symplify/symplify/pull/2177
[#2176]: https://github.com/symplify/symplify/pull/2176
[#2172]: https://github.com/symplify/symplify/pull/2172
[#2167]: https://github.com/symplify/symplify/pull/2167
[#2139]: https://github.com/symplify/symplify/pull/2139
[#2108]: https://github.com/symplify/symplify/pull/2108
[#2037]: https://github.com/symplify/symplify/pull/2037
[#2013]: https://github.com/symplify/symplify/pull/2013
[@zingimmick]: https://github.com/zingimmick
[@vyacheslav-startsev]: https://github.com/vyacheslav-startsev
[@tomasnorre]: https://github.com/tomasnorre
[@szepeviktor]: https://github.com/szepeviktor
[@staabm]: https://github.com/staabm
[@samsonasik]: https://github.com/samsonasik
[@ruudk]: https://github.com/ruudk
[@required]: https://github.com/required
[@marforon]: https://github.com/marforon
[@janatjak]: https://github.com/janatjak
[@glensc]: https://github.com/glensc
[@github-actions]: https://github.com/github-actions
[@alexcutts]: https://github.com/alexcutts
[@ThomasLandauer]: https://github.com/ThomasLandauer
[@NoorAdiana]: https://github.com/NoorAdiana
[@Kerrialn]: https://github.com/Kerrialn
[@JanMikes]: https://github.com/JanMikes
[@ComiR]: https://github.com/ComiR
[@BoGnY]: https://github.com/BoGnY
[9.0.0-rc1]: https://github.com/symplify/symplify/compare/9.0.0-BETA2...9.0.0-rc1
[9.0.0-BETA2]: https://github.com/symplify/symplify/compare/9.0.0-BETA1...9.0.0-BETA2
[#2726]: https://github.com/symplify/symplify/pull/2726
[#2725]: https://github.com/symplify/symplify/pull/2725
[#2724]: https://github.com/symplify/symplify/pull/2724
[#2723]: https://github.com/symplify/symplify/pull/2723
[#2720]: https://github.com/symplify/symplify/pull/2720
[#2719]: https://github.com/symplify/symplify/pull/2719
[#2714]: https://github.com/symplify/symplify/pull/2714
[#2712]: https://github.com/symplify/symplify/pull/2712
[#2711]: https://github.com/symplify/symplify/pull/2711
[#2710]: https://github.com/symplify/symplify/pull/2710
[#2709]: https://github.com/symplify/symplify/pull/2709
[#2708]: https://github.com/symplify/symplify/pull/2708
[#2706]: https://github.com/symplify/symplify/pull/2706
[#2705]: https://github.com/symplify/symplify/pull/2705
[#2704]: https://github.com/symplify/symplify/pull/2704
[#2703]: https://github.com/symplify/symplify/pull/2703
[#2701]: https://github.com/symplify/symplify/pull/2701
[#2699]: https://github.com/symplify/symplify/pull/2699
[#2696]: https://github.com/symplify/symplify/pull/2696
[#2692]: https://github.com/symplify/symplify/pull/2692
[#2691]: https://github.com/symplify/symplify/pull/2691
[#2690]: https://github.com/symplify/symplify/pull/2690
[#2689]: https://github.com/symplify/symplify/pull/2689
[#2688]: https://github.com/symplify/symplify/pull/2688
[#2687]: https://github.com/symplify/symplify/pull/2687
[#2686]: https://github.com/symplify/symplify/pull/2686
[#2685]: https://github.com/symplify/symplify/pull/2685
[#2684]: https://github.com/symplify/symplify/pull/2684
[#2681]: https://github.com/symplify/symplify/pull/2681
[#2680]: https://github.com/symplify/symplify/pull/2680
[#2679]: https://github.com/symplify/symplify/pull/2679
[#2677]: https://github.com/symplify/symplify/pull/2677
[#2676]: https://github.com/symplify/symplify/pull/2676
[#2675]: https://github.com/symplify/symplify/pull/2675
[#2673]: https://github.com/symplify/symplify/pull/2673
[#2672]: https://github.com/symplify/symplify/pull/2672
[#2671]: https://github.com/symplify/symplify/pull/2671
[#2670]: https://github.com/symplify/symplify/pull/2670
[#2668]: https://github.com/symplify/symplify/pull/2668
[#2667]: https://github.com/symplify/symplify/pull/2667
[#2666]: https://github.com/symplify/symplify/pull/2666
[#2665]: https://github.com/symplify/symplify/pull/2665
[#2663]: https://github.com/symplify/symplify/pull/2663
[#2660]: https://github.com/symplify/symplify/pull/2660
[#2659]: https://github.com/symplify/symplify/pull/2659
[#2657]: https://github.com/symplify/symplify/pull/2657
[#2656]: https://github.com/symplify/symplify/pull/2656
[#2655]: https://github.com/symplify/symplify/pull/2655
[#2653]: https://github.com/symplify/symplify/pull/2653
[#2651]: https://github.com/symplify/symplify/pull/2651
[#2649]: https://github.com/symplify/symplify/pull/2649
[@leoloso]: https://github.com/leoloso
[@enumag]: https://github.com/enumag
[9.0.11]: https://github.com/symplify/symplify/compare/9.0.0-rc1...9.0.11
[#2802]: https://github.com/symplify/symplify/pull/2802
[#2801]: https://github.com/symplify/symplify/pull/2801
[#2800]: https://github.com/symplify/symplify/pull/2800
[#2796]: https://github.com/symplify/symplify/pull/2796
[#2795]: https://github.com/symplify/symplify/pull/2795
[#2792]: https://github.com/symplify/symplify/pull/2792
[#2791]: https://github.com/symplify/symplify/pull/2791
[#2789]: https://github.com/symplify/symplify/pull/2789
[#2788]: https://github.com/symplify/symplify/pull/2788
[#2786]: https://github.com/symplify/symplify/pull/2786
[#2785]: https://github.com/symplify/symplify/pull/2785
[#2783]: https://github.com/symplify/symplify/pull/2783
[#2780]: https://github.com/symplify/symplify/pull/2780
[#2779]: https://github.com/symplify/symplify/pull/2779
[#2778]: https://github.com/symplify/symplify/pull/2778
[#2777]: https://github.com/symplify/symplify/pull/2777
[#2776]: https://github.com/symplify/symplify/pull/2776
[#2774]: https://github.com/symplify/symplify/pull/2774
[#2772]: https://github.com/symplify/symplify/pull/2772
[#2771]: https://github.com/symplify/symplify/pull/2771
[#2770]: https://github.com/symplify/symplify/pull/2770
[#2769]: https://github.com/symplify/symplify/pull/2769
[#2768]: https://github.com/symplify/symplify/pull/2768
[#2767]: https://github.com/symplify/symplify/pull/2767
[#2766]: https://github.com/symplify/symplify/pull/2766
[#2765]: https://github.com/symplify/symplify/pull/2765
[#2764]: https://github.com/symplify/symplify/pull/2764
[#2763]: https://github.com/symplify/symplify/pull/2763
[#2762]: https://github.com/symplify/symplify/pull/2762
[#2761]: https://github.com/symplify/symplify/pull/2761
[#2756]: https://github.com/symplify/symplify/pull/2756
[#2755]: https://github.com/symplify/symplify/pull/2755
[#2754]: https://github.com/symplify/symplify/pull/2754
[#2753]: https://github.com/symplify/symplify/pull/2753
[#2750]: https://github.com/symplify/symplify/pull/2750
[#2747]: https://github.com/symplify/symplify/pull/2747
[#2745]: https://github.com/symplify/symplify/pull/2745
[#2744]: https://github.com/symplify/symplify/pull/2744
[#2742]: https://github.com/symplify/symplify/pull/2742
[#2741]: https://github.com/symplify/symplify/pull/2741
[#2740]: https://github.com/symplify/symplify/pull/2740
[#2739]: https://github.com/symplify/symplify/pull/2739
[#2738]: https://github.com/symplify/symplify/pull/2738
[#2737]: https://github.com/symplify/symplify/pull/2737
[#2736]: https://github.com/symplify/symplify/pull/2736
[#2735]: https://github.com/symplify/symplify/pull/2735
[#2734]: https://github.com/symplify/symplify/pull/2734
[#2733]: https://github.com/symplify/symplify/pull/2733
[#2732]: https://github.com/symplify/symplify/pull/2732
[#2731]: https://github.com/symplify/symplify/pull/2731
[#2730]: https://github.com/symplify/symplify/pull/2730
[#2729]: https://github.com/symplify/symplify/pull/2729
[#2727]: https://github.com/symplify/symplify/pull/2727
[@param]: https://github.com/param
[@matthiasnoback]: https://github.com/matthiasnoback
[@lulco]: https://github.com/lulco
[@dakur]: https://github.com/dakur
[@annuh]: https://github.com/annuh
[@alexislefebvre]: https://github.com/alexislefebvre
[@alexbrouwer]: https://github.com/alexbrouwer
[9.0.24]: https://github.com/symplify/symplify/compare/9.0.11...9.0.24
