# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

PRs and issues are linked, so you can find more about it. Thanks to [ChangelogLinker](https://github.com/Symplify/ChangelogLinker).

<!-- changelog-linker -->

## Unreleased

### Added

- [#1102] Add `thecodingmachine/safe`

#### ChangelogLinker

- [#1067] Improve readme, add `package_aliases` [closes [#1066]]

#### CodingStandard

- [#1068] Add `Doctrine\ORM\Query\Expr` to allowed instantiable classes [closes [#1063]]
- [#1073] Add own `symplify.yml` set, grouping also other sniffs

#### MonorepoBuilder

- [#1081] Add validation into merge command [closes [#1071]]
- [#1106] Add SortComposerJsonDecorator
- [#1088] Add init command [closes [#1082]]

#### PHPStanExtensions

- [#1060] Add `stats` and `ignore` to `--error-format` options [closes [#1051]]

#### Statie

- [#1075] Add function support to `TwigFactory` via `FilterProvider`, 1:1 compat with Latte

#### EasycodingStandard

- [#1092] add section for integration with phpstorm, Thanks to [@azdanov]

### Changed

#### ChangelogLinker

- [#1064] improve regexes for guessing the type of change [surpasses #1059]

#### EasyCodingStandard

- [#1078] Allow combination of `--level` and `--config` [closes [#1072]]

#### MonorepoBuilder

- [#1104] Now validate only merge and validate command
- [#1079] Show versions from all `composer.json` files when calling validate
- [#1105] Make validation of command needs more precise

### Fixed

#### ChangelogLinker

- [#1094] Fix aliasing spaced and category resolving with spacing and slash
- [#1093] Fix merge title with markdown signs spacing
- [#1065] Fix package name resolving
- [#1069] Fix detection of Repository url from Git
- [#1090] Fix package message without package resolving [closes [#1089]]
- [#1085] Resolve new pull-request from merge date instead of id

#### CodingStandard

- [#1074] Fix wrong class reference to MagicMethodCasingFixer, Thanks to [@OndraM]

#### EasyCodingStandard

- [#1101] Fix Undefined property [closes [#1097]]

### Deprecated

#### CodingStandard

- [#1110] Deprecated `ImportNamespacedNameFixer`, use `ReferenceUsedNamesOnlySniff`

## [v4.7.0] - 2018-09-04

### Added

#### CodingStandard

- [#1044] Add `ForbiddenDoubleAssignSniff` [closes [#1012]]
- [#1042] Add `MethodOrderByTypeFixer` [closes [#1021]]
- [#1038] Add `allow_classes` option to `ClassStringToClassConstantFixer` [closes [#1015]]

#### PackageBuilder

- [#1035] Add CommandNaming support for `UPPPERCase` names [closes [#1016]]

#### ChangelogLinker

- [#1054] Add `--since-id` option to `dump-merges` command
- [#1047] Allow to specify path to `CHANGELOG.md` as argument
- [#1045] Add `cleanup` command

#### MonorepoBuilder

- [#1048] Add `release` command
- [#1036] Make branch alias configurable [closes [#997]]

### Fixed

#### CodingStandard

- [#1041] Fix `LineLengthFixer` for commented parts [closes [#973]]
- [#1039] Fix `BlockPropertyCommentFixer` for invalid annotation [closes [#972]]
- [#1040] Exclude `getSubscribedEvents()` from static functions [closes [#1030]]

#### EasyCodingStandard

- [#1043] Display relative path in error list [closes [#1034]]

#### MonorepoBuilder

- [#1049] Git tag version fixes

### Changed

- [#1050] Use new Sniff from Slevomat/CS 4.7, Thanks to [@carusogabriel]
- [#1046] Use `FileSystem` instead of file_get_contents

#### CodingStandard

- [#1032] bump to PHP CS Fixer 2.13, deprecate `MagicMethodsNamingFixer`

#### MonorepoBuilder

- [#1037] Remove only packages that were merged to the root [closes [#1007]]

## [v4.6.1] - 2018-08-20

### Changed

#### EasyCodingStandard

- [#1013] Move `YamlFileLoader` to `FileLoader` namespace

### Fixed

#### CodingStandard

- [#1020] Fix `ClassNameSuffixByParentSniff` for *Abstract*
- [#1014] Fix UPPERCASE start name for `PropertyNameMatchingTypeFixer`

#### PackageBuilder

- [#1009] Fix `AutowireSinglyImplementedCompilerPass` for missing class

## [v4.6.0] - 2018-08-03

### Added

#### CodingStandard

- [#1002] Add `ForbiddenParentClassSniff` [closes [#993]]

#### PackageBuilder

- [#998] Add `AutoBindParametersCompilerPass` [closes [#994]]
- [#989] Add `ParametersMergingYamlLoader`

### Fixed

- [#988] CHANGELOG: Correct `ClassNameSuffixByParentSniff` migration instruction, Thanks to [@sustmi]

#### ChangelogLinker

- [#992] Fix `ChangeSorter` for tags

#### CodingStandard

- [#1003] Fix for `ForbiddenParentClassSniff` in case of missing parent
- [#1004] Fix `ForbiddenParentClassSniff` for exact class skip

#### EasyCodingStandard

- [#984] Fix inconsistency between Symplify Fixer and `PHP_Codesniffer`, closes [#975]
- [#995] Update common and psr12 sets and fix `CheckerTolerantYamlFileLoader` for empty config

#### MonorepoBuilder

- [#1005] Fix autoloading for overlapping namespaces, Thanks to [@mantiz]

## [v4.5.1] - 2018-07-17

### Added

#### MonorepoBuilder

- [#983] Add root `composer.json` to `ValidateVersionsCommand` process

### Changed

- [#979] Update SlevomatCS and use new sniffs in pre-sets, Thanks to [@carusogabriel]
- [#976] Weaken `phpdoc-parser` depencency to prevent forcing people to upgrade to phpstan 0.10

#### Statie

- [#974] Return missing layout to file params
- [#982] Cleanup Twig and Latte FileDecorators, decouple constants

### Fixed

#### ChangelogLinker

- [#987] Fix autoload

#### Statie

- [#977] Fix twig code rendering in the post contents
- [#978] Fix latte code rendering in the post contents
- [#981] Fix incorrect block content wrapper removal

## [v4.5.0] - 2018-07-13

**The Top News?**

- [#892] Statie now supports Twig

**2 New Packages!**

- [LatteToTwigConverter](http://github.com/symplify/lattetotwigconverter) to convert Latte templates to Twig, see [the intro post](https://www.tomasvotruba.cz/blog/2018/07/05/how-to-convert-latte-templates-to-twig-in-27-regular-expressions/)
- and [MonorepoBuilder](https://github.com/symplify/monorepobuilder) that helps you with monorepo maintenance, from `composer.json` synchronization, version validation to Travis automated and parallel splits

### Added

#### LatteToTwigConverter

- [#943] Prepare the package
- [#941] Init new package

#### MonorepoBuilder

- [#946] Add Github Token split support
- [#936] Make split command 1-process setup
- [#932] Add split command from sh to php
- [#916] Installs git subsplit before running split command, Thanks to [@JanMikes]
- [#858] Init new package

#### ChangelogLinker

- [#911] Add `ChangeSorter` test to cover tags, categories and packages together
- [#879] Add `--token` option to increase Github API rate [closes [#874]]
- [#881] Simplify `ChangeFactory` creating + Add tags feature supports
- [#902] Add `--dry-run` option to dump-merges command to dump to the output vs write into `CHANGELOG.md`
- [#903] Add `--linkify` option to dump-merges command
- [#840] Add `LinkifyWorker`
- [#854] Add `dump-merges` command
- [#868] Add `ChangeTree`, `--in-packages` and `--in-categories` options to manage merge messages
- [#831] Allow `--config`

#### CodingStandard

- [#957] Interlink `ClassNameSuffixByParentFixer` deprecation to `CHANGELOG.md`
- [#900] Add `extra_parent_types_to_suffixes` option to `ClassNameSuffixByParentFixer`
- [#851] Add _ support to `PropertyNameMatchingTypeFixer`
- [#860] Add test case for [#855], Thanks to [@OndraM]
- [#836] Improve cognitive complexity error, Thanks to [@enumag]
- [#845] Extended `RemoveEmptyDocBlockFixer` fix

#### EasyCodingStandard

- [#967] Add `Find` command to simplify sniff/fixer finding
- [#965] Add `FilsystemCacheFactory` with per project unique namespace [closes [#964]]
- [#956] Add test case for [#923]
- [#849] Add `CurrentFileProvider` to standardize file format used over PHP CS Fixer and PHP_CodeSniffer
- [#852] Add support for `line_ending` configuration
- [#832] Allow short `ecs.yml` config [closes [#819]]

#### Statie

- [#950] Add `DumpFileDecoratorsCommand` [closes [#894]]
- [#931] Add `AbstractTemplatingFileDecorator`
- [#892] Add Twig

#### MonorepoBuilder

- [#862] Add new commands

#### PackageBuilder

- [#963] Add `FileSystem` with `separateFilesAndDirectories()` method

### Changed

- [#945] Cleanup bin files
- [#928] Bump `phpstan/phpdoc-parser` version, Thanks to [@marmichalski]
- [#918] Improve autoconfiguration
- [#847] Bump to PHP CS Fixer 2.12
- [#857] Bump to PHP_CodeSniffer 3.3 and related fixes

#### EasyCodingStandard

- [#948] Make `fnmatch` skipping more user-friendly [closes [#942]]

#### ChangelogLinker

- [#961] Linkify "closes" issues as well
- [#951] Enable all workers by default to make usage more pleasant
- [#949] Make `--in-tags` and `--linkify` defaults in `DumpMergesCommand`
- [#908] Change appending links on dump-merge in the end of file
- [#871] Improve test coverage
- [#883] Improve `--in-tags` option
- [#884] Change `--in-tags` option to cooperate with `--in-packages` and `--in-categories`
- [#867] Change Worker registration from implicit to explicit
- [#837] move curl dependency to pr/issue resolving
- [#839] Match issues by regex

#### CodingStandard

- [#901] Allow list option in `ClassNameSuffixByParentFixer`

#### Statie

- [#925] Normalize latte include paths from file name to relative paths [BC break]
    ##### Before

    ```html
    layout: "default"
    ---

    {include "postMetadata"}
    ```

    ##### After

    ```html
    layout: "_layouts/default.latte"
    ---

    {include "_snippets/postMetadata.latte"}
    ```

- [#895] Decouple `CodeBlocksProtector`
- [#893] Rename FlatWhite to Latte and move Latte-related code there
- [#888] Return collector-based approach to FileDecorators, with priorities
- [#887] Improve latte decoupling from the Statie

### Fixed

- [#952] Fix Travis-related Tagging

#### BetterPhpDocParser

- [#886] Fix annotation spacing

#### CodingStandard

<a name="change-link-1"></a>
- [#919] Change `ClassNameSuffixByParentFixer` to `ClassNameSuffixByParentSniff`
    ##### Before
    ```yaml
    services:
        Symplify\CodingStandard\Fixer\Naming\ClassNameSuffixByParentFixer:
            parent_types_to_suffixes:
                '*Command': 'Command'
                - '*Controller'
            extra_parent_types_to_suffixes:
                - '*FileDecorator'
    ```
    ##### After
    ```yaml
    services:
        Symplify\CodingStandard\Sniffs\Naming\ClassNameSuffixByParentSniff:
            defaultParentClassToSuffixMap:
                - 'Command'
                - 'Controller'
            extraParentTypesToSuffixes:
                - 'FileDecorator'
    ```

- [#955] Cover #896 [closes [#896]]
- [#870] `RemoveUselessDocBlockFixer` should not reformat custom annotations, Thanks to [@jankonas]
- [#842] `LineLengthSniff` - Initial support for tokens with newlines, Thanks to [@ostrolucky]

#### EasyCodingStandard

- [#958] Fix cache invalidation by checking only definitions and parameters
- [#939] Fix custom source provider troubles
- [#848] Fix single file processing

#### TokenRunner

- [#863] anonymous class now returns null on name [fixes [#855]]

#### Statie

- [#969] Fix File not `GeneratorFile` exception in empty generator config
- [#930] Latte & Twig File Decorators fixes
- [#927] Twig support fixes

#### ChangelogLinker

- [#962] Fix split tests
- [#917] Fix duplicated user links

### Removed

#### ChangelogLinker

- [#885] Drop `ReleaseReferencesWorker` - replaced by `dump-merges`
- [#905] Drop commit referencing to prevent promoting my bad practise

#### EasyCodingStandard

- [#924] Drop Performance overview per checker [reverts #330]

### Deprecated

#### Statie

- [#947]  Deprecate enableMarkdownHeadlineAnchors() [closes [#891]]

---

## [v4.4.0] - 2018-06-03

### Added

#### BetterPhpDocParser

- [#811] Add multi-types method support
- [#810] Add `AbstractPhpDocInfoDecorator`
- [#809] Allow `PhpDocInfoFactory` extension without modification
- [#807], [#808] Add `replaceTagByAnother()`
- [#806] Add `getParamTypeNodeByName()`
- [#804] Add `hasTag()` to `PhpDocInfo` and other improvements
- [#801] Add `PhpDocModifier` class

#### CodingStandard

- [#851] Add _ support to PropertyNameMatchingTypeFixer
- [#845] Extended RemoveEmptyDocBlockFixer fix
- [#836] Improve cognitive complexity error, Thanks to [@enumag]
- [#823] Add Cognitive complexity sniff

#### EasyCodingStandard

- [#852] Add support for `line_ending` configuration
- [#849] Add CurrentFileProvider to standardize file format used over PHP CS Fixer and PHP_CodeSniffer
- [#832] Allow short `ecs.yml` config

#### ChangelogLinker

- [#840] Add `LinkifyWorker
- [#839] Match issues by regex
- [#831] Allow `--config`
- [#829] From static to DI
- [#828] Add Unreleased to last tagged version feature

### Changed

- [#847] Bump to PHP CS Fixer 2.12
- [#818] Allow to install Symfony 3.4
- [#813] Unique parameter `id:` is now required for Generator elements, `PostFile` mostly; add "id: x" to your `_posts/<post-name>.md`
- [#813] `Symplify\Statie\Event\BeforeRenderEvent` - changed `getObjectsToRender()` to `getFiles()` + `getGeneratorFilesByType()` for GeneratorFiles added

### Fixed

#### CodingStandard

- [#843] Fix priorities in RemoveEmptyDocBlockFixer (closes [#838]), Thanks to [@ostrolucky]
- [#842] LineLengthSniff: Initial support for tokens with newlines, Thanks to [@ostrolucky]
- [#827] ImportNamespacedNameFixer: add support for union and array types + uniquate doc types

#### EasyCodingStandard

- [#802] Fix inconsistencies in symfony and symfony-risky rules, thanks to [@ostrolucky]
- [#848] Fix single file processing

#### Statie

- [#799] Fix 'posts' parameter override in Generator

#### BetterPhpDocParser

- [#796] Fix few edges cases

## [v4.1.0] - 2018-04-24

### Added

- [#791] Add **BetterPhpDocParser** package, born from **BetterReflectionDocBlock** package deprecation
- [#768] Add `Symplify\CodingStandard\Sniffs\ControlStructure\SprintfOverContactSniff`

### Changed

#### BetterReflectionDocBlock

- [#783], [#786], [#788], [#789] Migrate from `phpdocumentor/reflection-docblock` to `phpstan/phpdoc-parser` with more advanced API, keeping format preserving printer

#### CodingStandard

- [#790] Add inline support to `LineLenghtFixer` for arrays to be consistent with other structures - function, method arguments, new arguments etc. already do

- [#766] Make class-renaming checkers inform only
    - `Symplify\CodingStandard\Sniffs\Naming\AbstractClassNameSniff`
    - `Symplify\CodingStandard\Sniffs\Naming\InterfaceNameSniff`
    - `Symplify\CodingStandard\Sniffs\Naming\TraitNameSniff`

#### EasyCodingStandard

- [#764] Remove `sqlite` hidden dependency by using `symfony/cache`
- [#776] Print fixable messages as warnings, thanks [@OndraM]

### Fixed

- [#770] **CodingStandard** Fix `RemoveUselessDocBlockFixer` for useful `@return` tag description

#### EasyCodingStandard

- [#785] Fix skipper - remove global code skip from unused
- [#759] Fix cache invalidation for files with error or change, closes [#759]
- [#758] Fix missed unreported skips, closes [#750]
- [#757] Fix `@var` invalid types by adding `TolerantVar`
- [#771] Fix false report of unused errors while using `parameters > skip: > Sniff.Code: ~`
- [#763] Fix `CheckerServiceParametersShifter` for `null` value

#### BetterReflectionDocBlock

- [#769] Fix `TolerantVar` consistency

### CodingStandard

- [#825] Fix LineLenghtFixer in-string
- [#815] Fix LineLenghtFixer in functions body

### Deprecated

#### CodingStandard

- [#767] Deprecate `Symplify\CodingStandard\Fixer\Naming\ExceptionNameFixer` in favor of `Symplify\CodingStandard\Fixer\Naming\ClassNameSuffixByParentFixer` that does the same job and is extra configurable

#### BetterReflectionDocBlock

- Deprecate after migration to [phpstan/phpdoc-parser](https://github.com/phpstan/phpdoc-parser) in [#783], [#786], [#788], [#789], use **BetterPhpDocParser** package instead

## [v4.0.0] - 2018-04-02

Biggest change of this release is moving from mixture of Yaml and Neon format in `*.neon` files to Yaml format in `*.yaml` files. That will make Symplify packages more world-friendly and standard rather than Czech-only Neon format. See [#651](https://github.com/Symplify/Symplify/pull/651) about more reasoning behind this.

This change was finished in [Statie](https://github.com/Symplify/Statie) and [EasyCodingStandard](https://github.com/Symplify/EasyCodingStandard), where mostly requested.

### Added

- [#589] Add version printing on `-V` option in Console Applications, thanks to [@ostrolucky]

#### PackageBuilder

- [#755] Add `Symplify\PackageBuilder\Yaml\AbstractParameterMergingYamlFileLoader` for standalone use
- [#732] Add support for `Error` rendering to `Symplify\PackageBuilder\Console\ThrowableRenderer` (former `ExceptionRenderer`)
- [#720] Add `Symplify\PackageBuilder\Console\ExceptionRenderer` to render exception nicely Console Applications but anywhere outside it; follow up to [#715] and [#702]
- [#713] Add shortcut support for config `-c` in `ConfigFileFinder` and for level `-l` in `LevelFileFinder`
- [#680](https://github.com/Symplify/Symplify/pull/680/files#diff-412c71ea9d7b9fa9322e1cf23e39a1e7) Add `PublicForTestsCompilerPass` to remove `public: true` in configs and still allow `get()` use in tests
- [#645] Add `AutowireSinglyImplementedCompilerPass` to prevent redundant singly-interface binding
- [#612] Add `CommandNaming` to get command name from the class name

#### CodingStandard

- [#749] Add `Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer`, based on previous PRs: [#747], [#743], [#585], [#591], with configuration:

    ```yaml
    # easy-coding-standard.yml
    services:
        Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer:
            max_line_length: 100 # default: 120
            break_long_lines: true # default: true
            inline_short_lines: false # default: true
    ```

- [#722] Add `ForbiddenStaticFunctionSniff`
- [#707], [#709] Upgrade to [PHP CS Fixer 2.11](https://github.com/FriendsOfPHP/PHP-CS-Fixer/tree/v2.11.0)
- [#692] Add `ForbiddenReferenceSniff` to check all `&$var` references
- [#690] Make `RemoveUselessDocBlockFixer` cover functions as well
- [#633] Add `ClassNameSuffixByParentFixer`, closes [#607]

#### EasyCodingStandard

- [#706] Add [PSR-12 set](https://github.com/php-fig/fig-standards/blob/master/proposed/extended-coding-style-guide.md), use in CLI `--level psr12` or import `psr12.yml`

- [#741] Add support for `%vendor_dir%`, `%current_working_dir%` variables in `imports` section of configs to allow simpler loading [lmc-eu/php-coding-standard#6](https://github.com/lmc-eu/php-coding-standard/pull/6)

    ```yaml
    # lmc-coding-standard.yml
    imports:
        - { resource: '%vendor_dir%/symplify/easy-coding-standard/config/psr2.yml' }
        # or
        - { resource: '%current_working_dir%/vendor/symplify/easy-coding-standard/config/psr2.yml' }
    ```

- [#705] Add `-c` shortcut for `--config` CLI option, thanks to [@OndraM]
- [#698] Autodiscover `*.yaml` suffix as well
- [#656] Add configurable cache directory for changed files, closes [#650], thanks to [@marmichalski]
    ```yml
    # easy-coding-standard.yml
    parameters:
        cache_directory: .ecs_cache # defaults to sys_get_temp_dir() . '/_easy_coding_standard'
    ```

- [#583][#584] Add `exclude_files` option to exclude files, with `fnmatch` support:
   ```yml
   # easy-coding-standard.yml
   parameters:
       exclude_files:
           - 'lib/PhpParser/Parser/Php5.php'
           - 'lib/PhpParser/Parser/Php7.php'
           # new
           - '*/lib/PhpParser/Parser/Php*.php'
   ```

### Changed

- [#744] **CodingStandard**, **TokenRunner** Abstract line length logic from all fixers to `LineLengthTransformer`
- [#722] **TokenRunner** Move form `static` to service and constructor injection
- [#693] **CodingStandard** Move checkers from static to services, follow up to [#680]
- [#680] **BetterReflectionDocBlock** First steps to migration from [phpDocumentor/ReflectionDocBlock](https://github.com/phpDocumentor/ReflectionDocBlock) to [phpstan/phpdoc-parser](https://github.com/phpstan/phpdoc-parser)
- [#654] **Statie** Move from Yaml + Neon mixture to Yaml, similar to [#651]
    - [How to migrate from `*.neon` to `*.yml`](https://www.tomasvotruba.cz/blog/2018/03/12/neon-vs-yaml-and-how-to-migrate-between-them/#how-to-migrate-from-neon-to-yaml)?
- [#721] Prefer `Input` and `Output` instances injected via constructor in used Console Applications

#### PackageBuilder

- [#742] Decouple `Symplify\PackageBuilder\Yaml\ParameterInImportResolver` class for standalone use
- [#730] Renamed class `Symplify\PackageBuilder\Console\ExceptoinRenderer` to `Symplify\PackageBuilder\Console\ThrowableRenderer`
- [#713] Renamed class `Symplify\PackageBuilder\Configuration\ConfigFilePathHelper` to `Symplify\PackageBuilder\Configuration\LevelConfigShortcutFinder`
- [#713] Renamed class `Symplify\PackageBuilder\Configuration\ConfigFileFinder` to `Symplify\PackageBuilder\Configuration\LevelFileFinder`
- [#713] Renamed method `Symplify\PackageBuilder\Configuration\LevelFileFinder::resolveLevel()` to `Symplify\PackageBuilder\Configuration\LevelFileFinder::detectFromInputAndDirectory()`

#### EasyCodingStandard

- [#717] Make error report more verbose, closes [#701]
- [#712] Move from `Symplify\EasyCodingStandard\Testing\AbstractContainerAwareCheckerTestCase` to new `Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase`
- [#700] Rename deprecated Fixers to their new equivalents, thanks [@OndraM]
- [#680] Move from statics in checkers to autowired DI
- [#660] Move from `checkers` to `services`, follow up to [#651]
    ```diff
    # easy-coding-standard.yml
    -    checkers:
    +    services:
             Symplify\CodingStandard\Fixer\Import\ImportNamespacedNameFixer:
                 include_doc_blocks: true

    # this is needed to respect yaml format
    -        - SlamCsFixer\FinalInternalClassFixer:
    +        SlamCsFixer\FinalInternalClassFixer: ~
    ```
- [#661] Merge `parameters > skip_codes` to `parameters > skip` section
    ```diff
     # easy-coding-standard.yml
     parameters:
         skip:
             PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff:
                - 'packages/CodingStandard/src/Fixer/ClassNotation/LastPropertyAndFirstMethodSeparationFixer.php'

    -    skip_codes:
             SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff.UselessDocComment:
                 - '*packages*'
    ```
- [#651] Move from mixture custom neon + Symfony service DI to Yaml
    - [How to migrate from `*.neon` to `*.yml`](https://www.tomasvotruba.cz/blog/2018/03/12/neon-vs-yaml-and-how-to-migrate-between-them/#how-to-migrate-from-neon-to-yaml)?

### Fixed

#### EasyCodingStandard

- [#727][#740] Fix merging of `parameters` from imported configs, ref [symfony/symfony/#26713](https://github.com/symfony/symfony/issues/26713)
- [#729] Detect changes properly also in `*.yaml` files, thanks [@OndraM]
- [#640] Fix pre-mature adding file to cache, fixes [#637]

#### Statie

- [#595] Fix race condition for element sorting with configuration
- [59bdfc] Fix non-root `index.html` route, fixes [#638]

#### CodingStandard

- [#693] Fix `UnusedPublicMethodSniff` for static methods
- [#606] Fix few `RemoveUselessDocBlockFixer` cases
- [#598] Fix `PropertyNameMatchingTypeFixer` for self cases, fixes [#597]

#### BetterReflectionDocBlock

- [#599] Fix respecting spaces of inner tag
- [#603] Fix union-types pre-slash clean + some more for `RemoveUselessDocBlockFixer`
- [1fcc92] Fix variadic detection
- [caf08e] Fix escaping and variadic param resolver

### Removed

#### Statie

- [#647] Removed deprecated `vendor/bin/statie push-to-github` command, use [Github pages on Travis](https://www.statie.org/docs/github-pages/#allow-travis-to-make-changes) instead
- [#647] Removed deprecated `parameters > github_repository_slug` option, use `github_repository_source_directory` instead
    ```diff
     # statie.yml
     parameters:
    -    # <user>/<repository>
    -    github_repository_slug: "pehapkari/pehapkari.cz"
    +    # https://github.com/<user>/<repository>/tree/master/<source>, where <source> is name of directory with Statie content
    +    github_repository_source_directory: "https://github.com/pehapkari/pehapkari.cz/tree/master/source"
    ```
- [#647] Removed deprecated `statie.neon` note, use `statie.yml` instead

#### PackageBuilder

- [#720] Removed `Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory`, that was only for exception rendering; use `Symplify\PackageBuilder\Console\ExceptionRenderer` instead
- [#651] Removed `Symplify\PackageBuilder\Neon\Loader\NeonLoader` and `Symplify\PackageBuilder\Neon\NeonLoaderAwareKernelTrait`, that attempted to put Neon into Symfony Kernel, very poorly though; Yaml and Symfony DI is now used insteads

#### EasyCodingStandard

- [#712] Removed `Symplify\EasyCodingStandard\Testing\AbstractContainerAwareCheckerTestCase`, use `Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase` instead
- [#647] Removed deprecated bin files: `vendor/bin/easy-coding-standard` and `vendor/bin/easy-coding-standard.php`; use `vendor/bin/ecs` instead
- [#693] Removed `AbstractSimpleFixerTestCase` in favor of more general and advanced `AbstractContainerAwareCheckerTestCase`

#### CodingStandard

- [#708] Removed `AnnotateMagicContainerGetterFixer`, use awesome [Symfony Plugin](https://plugins.jetbrains.com/plugin/7219-symfony-plugin) instead
- [#688] Removed `DynamicPropertySniff`, use `PHPStan\Rules\Properties\AccessPropertiesRule`  PHPStan with [`--level 0`](https://github.com/phpstan/phpstan/blob/3485d8ce8c64a6becf6cc60f268d051af6ff7ceb/conf/config.level0.neon#L28) instead
- [#647] Removed deprecated `LastPropertyAndFirstMethodSeparationFixer`, see [#594], use [`PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer`](https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/b7cc8727c7faa8ebe7cc4220daaaabe29751bc5c/src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php) instead; extends it if you need different space count
- [#647] Removed deprecated `Symplify\CodingStandard\Fixer\Strict\InArrayStrictFixer`, use [`PhpCsFixer\Fixer\Strict\StrictParamFixer`](https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/b7cc8727c7faa8ebe7cc4220daaaabe29751bc5c/src/Fixer/Strict/StrictParamFixer.php) instead, that does the same job
- [#749] Removed `BreakMethodArgumentsFixer`, `BreakArrayListFixer`, use `Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer` instead

[comment]: # (links to issues, PRs and release diffs)

[@ostrolucky]: https://github.com/ostrolucky
[@enumag]: https://github.com/enumag
[@carusogabriel]: https://github.com/carusogabriel
[#583]: https://github.com/Symplify/Symplify/pull/583
[caf08e]: https://github.com/Symplify/Symplify/commit/caf08e93b2627e1e981493349957f4e49d55cd6a
[59bdfc]: https://github.com/Symplify/Symplify/commit/59bdfc3c0d4945f946d17f127e6a329384d5bab8
[1fcc92]: https://github.com/Symplify/Symplify/commit/1fcc927258710b0a03a806fa1661ed0179a5aaf7
[#640]: https://github.com/Symplify/Symplify/pull/640
[#638]: https://github.com/Symplify/Symplify/issues/638
[#637]: https://github.com/Symplify/Symplify/issues/637
[#633]: https://github.com/Symplify/Symplify/pull/633
[#612]: https://github.com/Symplify/Symplify/pull/612
[#607]: https://github.com/Symplify/Symplify/issues/607
[#606]: https://github.com/Symplify/Symplify/pull/606
[#603]: https://github.com/Symplify/Symplify/pull/603
[#599]: https://github.com/Symplify/Symplify/pull/599
[#598]: https://github.com/Symplify/Symplify/pull/598
[#597]: https://github.com/Symplify/Symplify/issues/597
[#595]: https://github.com/Symplify/Symplify/pull/595
[#594]: https://github.com/Symplify/Symplify/issues/594
[#591]: https://github.com/Symplify/Symplify/pull/591
[#589]: https://github.com/Symplify/Symplify/pull/589
[#585]: https://github.com/Symplify/Symplify/pull/585
[#584]: https://github.com/Symplify/Symplify/pull/584
[#647]: https://github.com/Symplify/Symplify/pull/647
[#645]: https://github.com/Symplify/Symplify/pull/645
[#654]: https://github.com/Symplify/Symplify/pull/654
[#651]: https://github.com/Symplify/Symplify/pull/651
[#650]: https://github.com/Symplify/Symplify/issues/650
[#656]: https://github.com/Symplify/Symplify/pull/656
[@marmichalski]: https://github.com/marmichalski
[#661]: https://github.com/Symplify/Symplify/pull/661
[#660]: https://github.com/Symplify/Symplify/pull/660
[#690]: https://github.com/Symplify/Symplify/pull/690
[#688]: https://github.com/Symplify/Symplify/pull/688
[#709]: https://github.com/Symplify/Symplify/pull/709
[#708]: https://github.com/Symplify/Symplify/pull/708
[#707]: https://github.com/Symplify/Symplify/pull/707
[#705]: https://github.com/Symplify/Symplify/pull/705
[#700]: https://github.com/Symplify/Symplify/pull/700
[#698]: https://github.com/Symplify/Symplify/pull/698
[#693]: https://github.com/Symplify/Symplify/pull/693
[#692]: https://github.com/Symplify/Symplify/pull/692
[#680]: https://github.com/Symplify/Symplify/pull/680
[@OndraM]: https://github.com/OndraM
[#722]: https://github.com/Symplify/Symplify/pull/722
[#721]: https://github.com/Symplify/Symplify/pull/721
[#720]: https://github.com/Symplify/Symplify/pull/720
[#717]: https://github.com/Symplify/Symplify/pull/717
[#715]: https://github.com/Symplify/Symplify/pull/715
[#713]: https://github.com/Symplify/Symplify/pull/713
[#712]: https://github.com/Symplify/Symplify/pull/712
[#702]: https://github.com/Symplify/Symplify/issues/702
[#701]: https://github.com/Symplify/Symplify/issues/701
[#747]: https://github.com/Symplify/Symplify/pull/747
[#744]: https://github.com/Symplify/Symplify/pull/744
[#743]: https://github.com/Symplify/Symplify/pull/743
[#742]: https://github.com/Symplify/Symplify/pull/742
[#741]: https://github.com/Symplify/Symplify/pull/741
[#740]: https://github.com/Symplify/Symplify/pull/740
[#732]: https://github.com/Symplify/Symplify/pull/732
[#730]: https://github.com/Symplify/Symplify/pull/730
[#729]: https://github.com/Symplify/Symplify/pull/729
[#749]: https://github.com/Symplify/Symplify/pull/749
[v4.0.0]: https://github.com/Symplify/Symplify/compare/v3.2.0...v4.0.0
[#755]: https://github.com/Symplify/Symplify/pull/755
[#706]: https://github.com/Symplify/Symplify/pull/706
[#759]: https://github.com/Symplify/Symplify/pull/759
[#758]: https://github.com/Symplify/Symplify/pull/758
[#757]: https://github.com/Symplify/Symplify/pull/757
[#750]: https://github.com/Symplify/Symplify/issues/750
[#764]: https://github.com/Symplify/Symplify/pull/764
[#763]: https://github.com/Symplify/Symplify/pull/763
[#769]: https://github.com/Symplify/Symplify/pull/769
[#768]: https://github.com/Symplify/Symplify/pull/768
[#767]: https://github.com/Symplify/Symplify/pull/767
[#766]: https://github.com/Symplify/Symplify/pull/766

[v4.1.0]: https://github.com/Symplify/Symplify/compare/v4.0.0...v4.1.0
[#791]: https://github.com/Symplify/Symplify/issues/791
[#790]: https://github.com/Symplify/Symplify/pull/790
[#789]: https://github.com/Symplify/Symplify/pull/789
[#788]: https://github.com/Symplify/Symplify/pull/788
[#786]: https://github.com/Symplify/Symplify/pull/786
[#785]: https://github.com/Symplify/Symplify/pull/785
[#783]: https://github.com/Symplify/Symplify/pull/783
[#776]: https://github.com/Symplify/Symplify/pull/776
[#771]: https://github.com/Symplify/Symplify/pull/771
[#770]: https://github.com/Symplify/Symplify/pull/770
[#796]: https://github.com/Symplify/Symplify/pull/796
[#811]: https://github.com/Symplify/Symplify/pull/811
[#810]: https://github.com/Symplify/Symplify/pull/810
[#809]: https://github.com/Symplify/Symplify/pull/809
[#806]: https://github.com/Symplify/Symplify/pull/806
[#804]: https://github.com/Symplify/Symplify/pull/804
[#802]: https://github.com/Symplify/Symplify/pull/802
[#801]: https://github.com/Symplify/Symplify/pull/801
[#799]: https://github.com/Symplify/Symplify/pull/799
[#813]: https://github.com/Symplify/Symplify/pull/813
[#808]: https://github.com/Symplify/Symplify/pull/808
[#807]: https://github.com/Symplify/Symplify/pull/807
[#852]: https://github.com/Symplify/Symplify/pull/852
[#851]: https://github.com/Symplify/Symplify/pull/851
[#849]: https://github.com/Symplify/Symplify/pull/849
[#848]: https://github.com/Symplify/Symplify/pull/848
[#847]: https://github.com/Symplify/Symplify/pull/847
[#845]: https://github.com/Symplify/Symplify/pull/845
[#842]: https://github.com/Symplify/Symplify/pull/842
[#840]: https://github.com/Symplify/Symplify/pull/840
[#839]: https://github.com/Symplify/Symplify/pull/839
[#838]: https://github.com/Symplify/Symplify/pull/838
[#836]: https://github.com/Symplify/Symplify/pull/836
[#832]: https://github.com/Symplify/Symplify/pull/832
[#831]: https://github.com/Symplify/Symplify/pull/831
[#829]: https://github.com/Symplify/Symplify/pull/829
[#828]: https://github.com/Symplify/Symplify/pull/828
[#827]: https://github.com/Symplify/Symplify/pull/827
[#825]: https://github.com/Symplify/Symplify/pull/825
[#823]: https://github.com/Symplify/Symplify/pull/823
[#818]: https://github.com/Symplify/Symplify/pull/818
[#815]: https://github.com/Symplify/Symplify/pull/815
[v4.4.0]: https://github.com/Symplify/Symplify/compare/v4.3.0...v4.4.0
[#868]: https://github.com/Symplify/Symplify/pull/868
[#867]: https://github.com/Symplify/Symplify/pull/867
[#863]: https://github.com/Symplify/Symplify/pull/863
[#862]: https://github.com/Symplify/Symplify/pull/862
[#858]: https://github.com/Symplify/Symplify/pull/858
[#857]: https://github.com/Symplify/Symplify/pull/857
[#855]: https://github.com/Symplify/Symplify/pull/855
[#854]: https://github.com/Symplify/Symplify/pull/854
[#837]: https://github.com/Symplify/Symplify/pull/837
[#860]: https://github.com/Symplify/Symplify/pull/860
[#843]: https://github.com/Symplify/Symplify/pull/843
[#727]: https://github.com/Symplify/Symplify/pull/727
[#905]: https://github.com/Symplify/Symplify/pull/905
[#903]: https://github.com/Symplify/Symplify/pull/903
[#902]: https://github.com/Symplify/Symplify/pull/902
[#901]: https://github.com/Symplify/Symplify/pull/901
[#900]: https://github.com/Symplify/Symplify/pull/900
[#895]: https://github.com/Symplify/Symplify/pull/895
[#893]: https://github.com/Symplify/Symplify/pull/893
[#892]: https://github.com/Symplify/Symplify/pull/892
[#888]: https://github.com/Symplify/Symplify/pull/888
[#887]: https://github.com/Symplify/Symplify/pull/887
[#886]: https://github.com/Symplify/Symplify/pull/886
[#885]: https://github.com/Symplify/Symplify/pull/885
[#884]: https://github.com/Symplify/Symplify/pull/884
[#883]: https://github.com/Symplify/Symplify/pull/883
[#881]: https://github.com/Symplify/Symplify/pull/881
[#879]: https://github.com/Symplify/Symplify/pull/879
[#871]: https://github.com/Symplify/Symplify/pull/871
[#870]: https://github.com/Symplify/Symplify/pull/870
[@jankonas]: https://github.com/jankonas
[@JanMikes]: https://github.com/JanMikes
[#908]: https://github.com/Symplify/Symplify/pull/908
[#905]: https://github.com/Symplify/Symplify/pull/905
[#903]: https://github.com/Symplify/Symplify/pull/903
[#902]: https://github.com/Symplify/Symplify/pull/902
[#901]: https://github.com/Symplify/Symplify/pull/901
[#900]: https://github.com/Symplify/Symplify/pull/900
[#895]: https://github.com/Symplify/Symplify/pull/895
[#893]: https://github.com/Symplify/Symplify/pull/893
[#892]: https://github.com/Symplify/Symplify/pull/892
[#888]: https://github.com/Symplify/Symplify/pull/888
[#887]: https://github.com/Symplify/Symplify/pull/887
[#886]: https://github.com/Symplify/Symplify/pull/886
[#885]: https://github.com/Symplify/Symplify/pull/885
[#884]: https://github.com/Symplify/Symplify/pull/884
[#883]: https://github.com/Symplify/Symplify/pull/883
[#881]: https://github.com/Symplify/Symplify/pull/881
[#879]: https://github.com/Symplify/Symplify/pull/879
[#871]: https://github.com/Symplify/Symplify/pull/871
[#870]: https://github.com/Symplify/Symplify/pull/870
[@jankonas]: https://github.com/jankonas

[#948]: https://github.com/Symplify/Symplify/pull/948
[#947]: https://github.com/Symplify/Symplify/pull/947
[#946]: https://github.com/Symplify/Symplify/pull/946
[#945]: https://github.com/Symplify/Symplify/pull/945
[#943]: https://github.com/Symplify/Symplify/pull/943
[#941]: https://github.com/Symplify/Symplify/pull/941
[#939]: https://github.com/Symplify/Symplify/pull/939
[#936]: https://github.com/Symplify/Symplify/pull/936
[#932]: https://github.com/Symplify/Symplify/pull/932
[#931]: https://github.com/Symplify/Symplify/pull/931
[#930]: https://github.com/Symplify/Symplify/pull/930
[#928]: https://github.com/Symplify/Symplify/pull/928
[#927]: https://github.com/Symplify/Symplify/pull/927
[#925]: https://github.com/Symplify/Symplify/pull/925
[#924]: https://github.com/Symplify/Symplify/pull/924
[#919]: https://github.com/Symplify/Symplify/pull/919
[#918]: https://github.com/Symplify/Symplify/pull/918
[#917]: https://github.com/Symplify/Symplify/pull/917
[#916]: https://github.com/Symplify/Symplify/pull/916
[#911]: https://github.com/Symplify/Symplify/pull/911
[#942]: https://github.com/Symplify/Symplify/pull/942
[#891]: https://github.com/Symplify/Symplify/pull/891
[#874]: https://github.com/Symplify/Symplify/pull/874
[#819]: https://github.com/Symplify/Symplify/pull/819
[#969]: https://github.com/Symplify/Symplify/pull/969
[#967]: https://github.com/Symplify/Symplify/pull/967
[#965]: https://github.com/Symplify/Symplify/pull/965
[#964]: https://github.com/Symplify/Symplify/pull/964
[#963]: https://github.com/Symplify/Symplify/pull/963
[#962]: https://github.com/Symplify/Symplify/pull/962
[#961]: https://github.com/Symplify/Symplify/pull/961
[#958]: https://github.com/Symplify/Symplify/pull/958
[#957]: https://github.com/Symplify/Symplify/pull/957
[#956]: https://github.com/Symplify/Symplify/pull/956
[#955]: https://github.com/Symplify/Symplify/pull/955
[#952]: https://github.com/Symplify/Symplify/pull/952
[#951]: https://github.com/Symplify/Symplify/pull/951
[#950]: https://github.com/Symplify/Symplify/pull/950
[#949]: https://github.com/Symplify/Symplify/pull/949
[#923]: https://github.com/Symplify/Symplify/pull/923
[#896]: https://github.com/Symplify/Symplify/pull/896
[#894]: https://github.com/Symplify/Symplify/pull/894
[v4.5.0]: https://github.com/Symplify/Symplify/compare/v4.4.0...v4.5.0
[#983]: https://github.com/Symplify/Symplify/pull/983
[#982]: https://github.com/Symplify/Symplify/pull/982
[#981]: https://github.com/Symplify/Symplify/pull/981
[#979]: https://github.com/Symplify/Symplify/pull/979
[#978]: https://github.com/Symplify/Symplify/pull/978
[#977]: https://github.com/Symplify/Symplify/pull/977
[#976]: https://github.com/Symplify/Symplify/pull/976
[#974]: https://github.com/Symplify/Symplify/pull/974
[#987]: https://github.com/Symplify/Symplify/pull/987
[v4.5.1]: https://github.com/Symplify/Symplify/compare/v4.5.0...v4.5.1

[#1005]: https://github.com/Symplify/Symplify/pull/1005
[#1004]: https://github.com/Symplify/Symplify/pull/1004
[#1003]: https://github.com/Symplify/Symplify/pull/1003
[#1002]: https://github.com/Symplify/Symplify/pull/1002
[#998]: https://github.com/Symplify/Symplify/pull/998
[#995]: https://github.com/Symplify/Symplify/pull/995
[#994]: https://github.com/Symplify/Symplify/pull/994
[#993]: https://github.com/Symplify/Symplify/pull/993
[#992]: https://github.com/Symplify/Symplify/pull/992
[#989]: https://github.com/Symplify/Symplify/pull/989
[#988]: https://github.com/Symplify/Symplify/pull/988
[@sustmi]: https://github.com/sustmi
[@mantiz]: https://github.com/mantiz
[#984]: https://github.com/Symplify/Symplify/pull/984
[#975]: https://github.com/Symplify/Symplify/pull/975
[v4.6.0]: https://github.com/Symplify/Symplify/compare/v4.5.1...v4.6.0
[#1020]: https://github.com/Symplify/Symplify/pull/1020
[#1014]: https://github.com/Symplify/Symplify/pull/1014
[#1013]: https://github.com/Symplify/Symplify/pull/1013
[#1009]: https://github.com/Symplify/Symplify/pull/1009
[v4.6.1]: https://github.com/Symplify/Symplify/compare/v4.6.0...v4.6.1
[#1044]: https://github.com/Symplify/Symplify/pull/1044
[#1043]: https://github.com/Symplify/Symplify/pull/1043
[#1042]: https://github.com/Symplify/Symplify/pull/1042
[#1041]: https://github.com/Symplify/Symplify/pull/1041
[#1040]: https://github.com/Symplify/Symplify/pull/1040
[#1039]: https://github.com/Symplify/Symplify/pull/1039
[#1038]: https://github.com/Symplify/Symplify/pull/1038
[#1037]: https://github.com/Symplify/Symplify/pull/1037
[#1036]: https://github.com/Symplify/Symplify/pull/1036
[#1035]: https://github.com/Symplify/Symplify/pull/1035
[#1034]: https://github.com/Symplify/Symplify/pull/1034
[#1032]: https://github.com/Symplify/Symplify/pull/1032
[#1030]: https://github.com/Symplify/Symplify/pull/1030
[#1021]: https://github.com/Symplify/Symplify/pull/1021
[#1016]: https://github.com/Symplify/Symplify/pull/1016
[#1015]: https://github.com/Symplify/Symplify/pull/1015
[#1012]: https://github.com/Symplify/Symplify/pull/1012
[#1007]: https://github.com/Symplify/Symplify/pull/1007
[#997]: https://github.com/Symplify/Symplify/pull/997
[#973]: https://github.com/Symplify/Symplify/pull/973
[#972]: https://github.com/Symplify/Symplify/pull/972
[#1054]: https://github.com/Symplify/Symplify/pull/1054
[#1050]: https://github.com/Symplify/Symplify/pull/1050
[#1049]: https://github.com/Symplify/Symplify/pull/1049
[#1048]: https://github.com/Symplify/Symplify/pull/1048
[#1047]: https://github.com/Symplify/Symplify/pull/1047
[#1046]: https://github.com/Symplify/Symplify/pull/1046
[#1045]: https://github.com/Symplify/Symplify/pull/1045

[#1110]: https://github.com/Symplify/Symplify/pull/1110
[#1106]: https://github.com/Symplify/Symplify/pull/1106
[#1105]: https://github.com/Symplify/Symplify/pull/1105
[#1104]: https://github.com/Symplify/Symplify/pull/1104
[#1102]: https://github.com/Symplify/Symplify/pull/1102
[#1101]: https://github.com/Symplify/Symplify/pull/1101
[#1098]: https://github.com/Symplify/Symplify/pull/1098
[#1097]: https://github.com/Symplify/Symplify/pull/1097
[#1094]: https://github.com/Symplify/Symplify/pull/1094
[#1093]: https://github.com/Symplify/Symplify/pull/1093
[#1092]: https://github.com/Symplify/Symplify/pull/1092
[#1090]: https://github.com/Symplify/Symplify/pull/1090
[#1089]: https://github.com/Symplify/Symplify/pull/1089
[#1088]: https://github.com/Symplify/Symplify/pull/1088
[#1085]: https://github.com/Symplify/Symplify/pull/1085
[#1082]: https://github.com/Symplify/Symplify/pull/1082
[#1081]: https://github.com/Symplify/Symplify/pull/1081
[#1079]: https://github.com/Symplify/Symplify/pull/1079
[#1078]: https://github.com/Symplify/Symplify/pull/1078
[#1075]: https://github.com/Symplify/Symplify/pull/1075
[#1074]: https://github.com/Symplify/Symplify/pull/1074
[#1073]: https://github.com/Symplify/Symplify/pull/1073
[#1072]: https://github.com/Symplify/Symplify/pull/1072
[#1071]: https://github.com/Symplify/Symplify/pull/1071
[#1069]: https://github.com/Symplify/Symplify/pull/1069
[#1068]: https://github.com/Symplify/Symplify/pull/1068
[#1067]: https://github.com/Symplify/Symplify/pull/1067
[#1066]: https://github.com/Symplify/Symplify/pull/1066
[#1065]: https://github.com/Symplify/Symplify/pull/1065
[#1064]: https://github.com/Symplify/Symplify/pull/1064
[#1063]: https://github.com/Symplify/Symplify/pull/1063
[#1060]: https://github.com/Symplify/Symplify/pull/1060
[#1051]: https://github.com/Symplify/Symplify/pull/1051
[@azdanov]: https://github.com/azdanov
[v4.7.0]: https://github.com/Symplify/Symplify/compare/v4.6.1...v4.7.0