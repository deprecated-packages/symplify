# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

PRs and issues are linked, so you can find more about it. Thanks to [ChangelogLinker](https://github.com/symplify/changelog-linker).

<!-- changelog-linker -->

## Unreleased

### Added

#### CodingStandard

- [#2498] Fixes [#2493] Add RemoveUselessClassCommentFixer, Thanks to [@samsonasik]
- [#2522] Fixes [#2517] Add Handle useless method comment in RemoveUselessClassCommentFixer, Thanks to [@samsonasik]

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

- [#2550] Fixes [#2330] Add ForbiddenCallOnTypeRule, Thanks to [@samsonasik]
- [#2529] Add foreach over nested
- [#2557] Fixes [#2548] Add CheckOptionArgumentCommandRule, Thanks to [@samsonasik]
- [#2499] Add configuration to NoChainMethodCallRule
- [#2559] Fixes [#2327] Add CheckTypehintCallerTypeRule, Thanks to [@samsonasik]
- [#2515] Add RequireClassTypeInClassMethodByTypeRule

#### PHPUnitUpgrader

- [#2537] Add new package

#### RuleDocGeneator

- [#2510] Add ComposerJsonAwareCodeSample

#### RuleDocGenerator

- [#2511] Add ExtraFileCodeSampler
- [#2509] Add Rector print support
- [#2547] Add count to the top + category

#### SimplePhpDocParser

- [#2552] Add new package

#### Skipper

- [#2553] Various paths improvements, add shouldSkipElement() method
- [#2546] Add SkippedPathsResolver
- [#2495] Add a fix for PathNormalizer if Path contains "..", Thanks to [@tomasnorre]

#### StaticDetector

- [#2533] Add new package

#### SymfonyPhpConfig

- [#2549] Add a new package

#### TemplateChecker

- [#2535] Add a new package

#### Unknown Package

- [#2544] README: add Symplify 9 packages to list
- [#2536] [PSR-4 Switcher] Add a new package

#### VendorPatches

- [#2534] Add a new package

### Changed

#### CI

- [#2496] Move coding standards from ci-reivew to daily-prs
- [#2497] Move ci-review + rector-ci to daily PRs to speedup contributions

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

- [#2513] Make ForbiddenMethodOrStaticCallInIfRule skip trinary logic
- [#2505] Allow parsing parent classes from phar
- [#2503] Skip spready in RobotLoader addDirectory
- [#2500] Make ClassLikeCognitiveComplexityRule configurable by class type
- [#2512] Allow match in calls
- [#2568] Improve CheckTypehintCallerTypeRule : skip non private and multiple usages, Thanks to [@samsonasik]

#### RuleDocGenerator

- [#2506] Allow multiple dirs

#### Skipper

- [#2545] Decouple to collector
- [#2543] decouple FileInfoMatcher, OnlySkipper

#### SnifferFixerToECSConverter

- [#2540] Init a new package

#### SymfonyPhpConfig

- [#2551] Move from functions to static class

#### Unknown Package

- [#2492] Automated Update of Changelog on 2020-11-15, Thanks to [@github-actions][bot]
- [#2501] Define package as PHPStan Extension, Thanks to [@szepeviktor]
- [#2558] phpstan typo in readme, Thanks to [@alexcutts]
- [#2554] normalize readme

#### automated

- [#2566] Re-Generate Docs, Thanks to [@github-actions][bot]
- [#2562] Re-Apply Coding Standards, Thanks to [@github-actions][bot]
- [#2561] Re-Generate Docs, Thanks to [@github-actions][bot]
- [#2556] Re-Generate Docs, Thanks to [@github-actions][bot]
- [#2504] Re-Apply Rector Rules, Thanks to [@github-actions][bot]
- [#2567] Re-Apply Coding Standards, Thanks to [@github-actions][bot]
- [#2514] Re-Apply Rector Rules, Thanks to [@github-actions][bot]

#### phpstan

- [#2525] limit class complexity by type

### Fixed

#### CodingStandard

- [#2519] Fixes [#2517] improve RemoveUselessClassCommentFixer : remove useless constructor comment, Thanks to [@samsonasik]

#### EasyHydrator

- [#2520] ObjectTypeCaster fix indexed array of objects, Thanks to [@janatjak]

#### PHPStanRules

- [#2563] Fixes to ensure check next node instanceof Identifier on CheckUsedNamespacedNameOnClassNodeRule, Thanks to [@samsonasik]
- [#2564] Fixes node->getAttribute() got null on CheckTypehintCallerTypeRule, Thanks to [@samsonasik]

#### SymfonyStaticDumper

- [#2555] Fixes [#2108] : Document usage for controllers with arguments, Thanks to [@samsonasik]

#### Unknown Package

- [#2502] Fix link in README to work in a monorepo too, Thanks to [@szepeviktor]
- [#2524] Fix typo in code sample, Thanks to [@staabm]

## [9.0.0-BETA2] - 2020-11-15

### Added

#### CI

- [#2468] Drop coding standard doc check, will be replaced by RuleDocGenerator + add rule definitions for sniffs/fixers

#### CodingStandard

- [#2457] Fixes [#2208] Add CheckConstantExpressionDefinedInConstructOrSetupRule, Thanks to [@samsonasik]
- [#2483] Fixes [#2482] Add RemovePHPStormTodoImplementMethodCommentFixer, Thanks to [@samsonasik]
- [#2481] Fixes [#2480] Add RemovePHPStormTodoCommentFixer, Thanks to [@samsonasik]

#### PHPStanRules

- [#2456] Fixes [#2404] Add ForbiddenPrivateMethodByTypeRule, Thanks to [@samsonasik]
- [#2466] Fixes [#2405] Add ForbiddenMethodCallByTypeInLocationRule, Thanks to [@samsonasik]
- [#2476] Add ExclusiveDependencyRule, Thanks to [@samsonasik]

#### Unknown Package

- [#2475] add missing nette/neon package

### Changed

#### CI

- [#2453] What if Github Actions can handle the split of packages?
- [#2448] Automated CHANGELOG generation once 2 weeks

#### CodingStandard

- [#2469] Generate rules_overview file
- [#2455] Improve CheckConstantStringValueFormatRule : Allow array constant value, Thanks to [@samsonasik]

#### DX

- [#2478] Decouple private methods from commands

#### EasyCodingStandard

- [#2442] Switch prefixed ecs.phar to automated package scoping in GitHub Action

#### MarkdownDiffer

- [#2470] Init new package

#### MonorepoBuilder

- [#2477] Move from manual package list to json list
- [#2454] Prepare for split command deprectaion

#### PHPStanPHPConfig

- [#2464] Init new package with phpstan.php

#### PHPStanRules

- [#2445] update ManyNodeRuleInterface location
- [#2462] Decorated with Rule Doc Generator interface and move examples to the code
- [#2458] Fail for CheckConstantExpressionDefinedInConstructOrSetupRule
- [#2461] Improve CheckConstantExpressionDefinedInConstructOrSetupRule
- [#2473] Generate the docs

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

- [#2459] fix typo, Thanks to [@staabm]
- [#2460] Fix typos, Thanks to [@staabm]
- [#2474] Markdown format fixes

### Removed

#### MonorepoBuilder

- [#2490] Drop split command, delegate to GitHub Action with less magic

#### Unknown Package

- [#2451] drop manual setName(), let applicaton handle that

## [9.0.0-BETA1] - 2020-11-14

### Added

#### CodingStandard

- [#2362] Fixes [#2301] Add ForbiddenNewInMethodRule, Thanks to [@samsonasik]
- [#2394] Fixes [#2372] Add forbidden curl functions to symplify-rules.neon, Thanks to [@samsonasik]
- [#2265] Fixes [#2261] Add Check Required `abstract` Keyword for Class Name Start with Abstract, Thanks to [@samsonasik]
- [#2266] Fixes [#2238] : Add Check Unneeded SymfonyStyle usage for only newline, write(ln) rule, Thanks to [@samsonasik]
- [#2357] Add NoSuffixValueObjectClassRule
- [#2403] Fixes [#2373] : Add CheckParentChildMethodParameterTypeCompatibleRule, Thanks to [@samsonasik]
- [#2270] Add MethodChainingNewlineFixer
- [#2401] Fixes [#2349] : Add ForbiddenConstructorDependencyByTypeRule, Thanks to [@samsonasik]
- [#2274] Add function call skip to MethodChainingNewlineFixer
- [#2275] Add skip for opened call to MethodChainingNewlineFixer
- [#2395] Fixes [#2347] Add CheckRequiredAutowireAutoconfigurePublicUsedInConfigServiceRule, Thanks to [@samsonasik]
- [#2276] Fixes [#2207] : Add NoParentMethodCallOnEmptyStatementInParentMethod rule, Thanks to [@samsonasik]
- [#2252] Fixes [#2176] : Add No factory in constructor rule, Thanks to [@samsonasik]
- [#2388] Fixes [#2381] Add ForbiddenMethodCallInIfRule, Thanks to [@samsonasik]
- [#2280] Add case with curly opener
- [#2292] Add NoParticularNodeRule
- [#2298] Fixes [#2226] add PreferredRawDataInTestDataProvider, Thanks to [@samsonasik]
- [#2317] Add trait/class difference in CheckRequiredMethodTobeAutowireWithClassNameRule
- [#2379] Fixes [#2378] Add ForbiddenMethodOrFuncCallInForeachRule, Thanks to [@samsonasik]
- [#2377] Add other exprs to PreferredClassConstantOverVariableConstantRule
- [#2366] Fixes [#2365] Add ForbidNewOutsideFactoryServiceRule, Thanks to [@samsonasik]
- [#2355] Add failing class with anonymous
- [#2356] Fixes [#2331] Add ForbiddenProtectedPropertyRule, Thanks to [@samsonasik]
- [#2264] Add ArrayListItemNewlineFixer
- [#2269] Fixes [#2207] : Add No Parent Method Call On No Override Process Rule, Thanks to [@samsonasik]
- [#2250] Add SuffixInterfaceRule
- [#2229] Fixes [#2172] : Add NoTraitExceptItsMethodsRequired Rule, Thanks to [@samsonasik]
- [#2206] Fix [#2199] : add NoScalarAndArrayConstructorParameterRule to prevent config coding, Thanks to [@samsonasik]
- [#2443] Fixes [#2350] Add CheckConstantStringValueFormatRule, Thanks to [@samsonasik]
- [#2212] Add allowed classes to NoScalarAndArrayConstructorParameterRule
- [#2436] Fixes [#2417] Add CheckRequiredInterfaceInContractNamespaceRule, Thanks to [@samsonasik]
- [#2215] Improve performance for ArrayOpenerNewlineFixer + add closer support
- [#2433] Fixes [#2432] : Add ForbiddenSpreadOperatorRule, Thanks to [@samsonasik]
- [#2424] Fixes [#2352] : Add RequireNewArgumentConstantRule, Thanks to [@samsonasik]
- [#2419] Fixes [#2204] : Add CheckTraitMethodOnlyDelegateOtherClassRule, Thanks to [@samsonasik]
- [#2249] Add SuffixTraitRule
- [#2423] Fixes [#2422] : Add ForbiddenMethodCallOnNewRule, Thanks to [@samsonasik]
- [#2248] Add PrefixAbstractClassRule
- [#2241] add markdown array open/close fixutre

#### DX

- [#2316] Fixes [#2287] Add regex links to PHPStan reported constants, Thanks to [@samsonasik]

#### EasyCI

- [#2211] Add Sonar config generator

#### EasyCodingStandard

- [#2409] Add --match-git-diff option

#### EasyHydrator

- [#2430] ParameterTypeRecognizer added support for array|null union type, Thanks to [@janatjak]
- [#2431] added ArrayTypeCaster - allow retype array of scalars, Thanks to [@janatjak]

#### EasyTesting

- [#2222] Add splitFileInfoToLocalInputAndExpected()

#### MonorepoBuilder

- [#2302] Add validation for directories and repositories parameter for split
- [#2437] Add scoped-only prefixed version + New package for package scoping
- [#2389] Add DIRECTORIES_TO_REPOSITORIES_CONVERT_FORMAT option for pascal case directory names
- [#2391] Add number test
- [#2398] Add check-split-test-workflow command

#### PHPStanRules

- [#2447] Add example, fix typo, Thanks to [@staabm]

#### PackageBuilder

- [#2202] Add provideIntParameter()

#### PackageScoper

- [#2439] Add 2 more commands for config generating

#### Packages

- [#2232] Add paypal sponsor link

#### Unknown Package

- [#2239] Fixes [#2177] : add CheckRequiredMethodTobeAutowireWithClassName rule, Thanks to [@samsonasik]
- [#2257] Fixes [#2243] : Add No Setter on Service rule, Thanks to [@samsonasik]
- [#2320] add init command to ECS, Thanks to [@Kerrialn]
- [#2324] remove .idea and add it to .gitignore, Thanks to [@samsonasik]
- [#2340] Fixes [#2332] Add RequireThisOnParentMethodCallRule, Thanks to [@samsonasik]
- [#2344] Fixes [#2247] : Add ForbiddenNestedForeachWithEmptyStatementRule, Thanks to [@samsonasik]
- [#2348] Fixes [#2167] add RequireConstantInMethodCallPositionRule, Thanks to [@samsonasik]
- [#2351] Fixes [#2343] : Add ForbiddenMultipleClassLikeInOneFileRule, Thanks to [@samsonasik]
- - [#2361] Fixes [#2342] : Add NoMethodTagInClassDocblockRule, Thanks to [@samsonasik]

#### ci

- [#2410] make rector_ci run use auto commit action + add ci-review
- [#2427] add composer install cache

#### cs

- [#2319] Fixes [#2214] : add PrefferedMethodCallOverFuncCallRule, Thanks to [@samsonasik]
- [#2375] Fixes [#2329] Add TooDeepNewClassNestingRule, Thanks to [@samsonasik]
- [#2341] Fixes [#2328] Add ForbiddenAssignInIfRule, Thanks to [@samsonasik]

### Changed

#### CodingStandard

- [#2385] Rename Forbid to Forbidden prefix in Rule name, Thanks to [@samsonasik]
- [#2315] Skip array return if required by parent method of class/interface
- [#2223] Skip Event and Entity for scalar in constructor
- [#2267] Rename ForceMethodCallArgumentConstantRule to RequireMethodCallArgumentConstantRule
- [#2384] Protected rules improvements
- [#2407] Improve line lenght fixer + array break fixer combination
- [#2237] Make symplify-rules easier to copy
- [#2288] Skip variable in RequireMethodCallArgumentConstantRule
- [#2286] PHPStan rules improvoments
- [#2285] Get rules from container for tests if possible
- [#2282] Various PHPStan rules improvements
- [#2387] Improve ForbiddenMethodOrFuncCallInForeachRule : Allow empty args, Thanks to [@samsonasik]
- [#2291] Skip exception in NoParentMethodCallOnEmptyStatementInParentMethodRule
- [#2307] Allow uuid factory static call
- [#2339] Skip array square wrap over multi chain call
- [#2209] Update NoScalarAndArrayConstructorParameterRule to work with doc types
- [#2203] Resolve FQN name for class in NoProtectedElementInFinalClassRule

#### EasyCodingStandard

- [#2213] Enable debug progress bar on -v
- [#2313] Make system errors in separated stream with SystemError object
- [#2245] Use i/o json testing

#### EasyHydrator

- [#2360] Make use rector split package
- [#2255] DateTimeImmutable resolver (symplify[#2254]), Thanks to [@vyacheslav-startsev]
- [#2416] Support for objects with default constructor values, Thanks to [@JanMikes]
- [#2195] Support for hydrating nested objects and typed arrays, Thanks to [@JanMikes]

#### MonorepoBuilder

- [#2259] Composer repository priority, Thanks to [@NoorAdiana]
- [#2392] Merge ConvertFormat to main test

#### PHPStanRules

- [#2444] Split PHPStan rules only package from CodingStandard package
- [#2445] update ManyNodeRuleInterface location

#### Skipper

- [#2420] Decouple new package

#### SmartFileSystem

- [#2227] Sort names by SmartFinder
- [#2231] Sort names by SmartFinder

#### SymplifyKernel

- [#2294] Init

#### Unknown Package

- [#2359] improve CheckUsedNamespacedNameOnClassNodeRule, Thanks to [@samsonasik]
- [#2402] Provisional support for PHP 8.0, Thanks to [@zingimmick]
- [#2408] make sure it passes
- [#2358] improve ForbiddenProtectedPropertyRule, Thanks to [@samsonasik]
- [#2435] Various changes
- [#2311] use DI over manual instantiation
- - [#2364] Update README.md, Thanks to [@ThomasLandauer]
- [#2303] various static improvement
- [#2368] Update README.md, Thanks to [@ThomasLandauer]
- [#2369] Update README.md, Thanks to [@ThomasLandauer]
- [#2289] change HelpfulApplicationTrait to AbstractSymplifyConsoleApplication
- [#2370] reactivate coverage report, Thanks to [@samsonasik]
- [#2386] Use DIRECTORY_SEPARATOR constant instead of `/`, Thanks to [@ComiR]
- [#2374] improve ForbiddenProtectedPropertyRule : handle injection with [@required] and autowire, Thanks to [@samsonasik]
- [#2304] typo
- [#2210] update regex link

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

- [#2284] Fixes [#2253] : Prefer class::constant over $variable::constant, Thanks to [@samsonasik]
- [#2290] Fix chain indenxt on multi with arg
- [#2314] Fix intersection for RequireMethodCallArgumentConstantRule
- [#2380] Protected and factory PHPStan fixes
- [#2308] Various PHPStan rule fixes
- [#2325] Fixes [#2318] : Bug with CheckUnneededSymfonyStyleUsageRule that requires to use MethodCall with $scope context, Thanks to [@samsonasik]
- [#2383] fix NoProtectedElementInFinalClassRule for trait
- [#2293] Fix previous func call in chain
- [#2281] Fixes [#2277] Fixes false positive CheckUnneededSymfonyStyleUsageRule, Thanks to [@samsonasik]
- [#2279] Fixes [#2278] handle False positive in case of parent interface/trait in NoProtectedElementInFinalClassRule, Thanks to [@samsonasik]
- [#2371] fix new anonymous for ForbiddenNewInMethodRule
- [#2216] Improve memory on array fixes

#### EasyCodingStandard

- [#2297] Fix missing exception
- [#2224] Fixes [#2219] : Fixes StandardizeHereNowDocKeywordFixer indentation closing heredoc/nowdoc to not be removed, Thanks to [@samsonasik]

#### EasyHydrator

- [#2396] Fixes [#2393] AutowireArrayParameterCompilerPass, Thanks to [@janatjak]
- [#2428] fix TypeCastersCollector::$typeCasters sort, Thanks to [@janatjak]
- [#2397] fix missing ParameterTypeRecognizer dependencies, Thanks to [@janatjak]

#### MonorepoBuilder

- [#2337] Fix propagate
- [#2246] Fixes [#2139] ensure check file exists of monorepo-builder.yaml, fallback to monorepo-builder.php, Thanks to [@samsonasik]
- [#2353] Fix absolute to relative paths

#### PackageBuilder

- [#2438] fix DefinitionFinder class not found, Thanks to [@janatjak]

#### SFS

- [#2413] Fixed skipping rules fails on Windows system ([#2399]), Thanks to [@BoGnY]

#### Unknown Package

- [#2336] Fixes [#2234] use namespaceName on $class node, Thanks to [@samsonasik]
- [#2441] ecs/readme: fix fdefault typo, Thanks to [@glensc]
- [#2305] fixes
- [#2310] Typo fix: errorMessaeg -> errorMessage, Thanks to [@samsonasik]
- [#2271] Typo fix: NoAbstactMethodRule should be NoAbstractMethodRule, Thanks to [@samsonasik]
- [#2299] various fixes
- [#2272] Fixes [#2225] : No __construct, only setUp() in tests, Thanks to [@samsonasik]

### Removed

#### ChangelogLinker

- [#2418] Drop YAML, use PHP

#### EasyCodingStandard

- [#2411] drop YAML support

#### EasyHydrator

- [#2426] Drop unused ParameterValueGetterInterface

#### Unknown Package

- [#2300] remove ctor factory
- [#2323] Init command feature - remove .idea, Thanks to [@Kerrialn]
- [#2406] Drop dependency on Composer
