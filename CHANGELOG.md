# Changelog

Note: due to rather agile development of packages and big amount of releases all changes are bunched in nearest minor version, e.g. changes from 3.1.0-3.1.15 => 3.2.0. That makes changelog more clear and readable, rather then having 15 lines with one change per version, and also helps to actually maintain this file.

## [v4.0.0alpha1][Unreleased]

Biggest change of this release is moving from mixture of Yaml and Neon format in `*.neon` files to Yaml format in `*.yaml` files. That will make Symplify packages more world-friendly and standard rather than Czech-only Neon format. See [#651](https://github.com/Symplify/Symplify/pull/651) about more reasoning behind this.

This change was finished in [Statie](https://github.com/Symplify/Statie) and [EasyCodingStandard](https://github.com/Symplify/EasyCodingStandard), where mostly requested.

### Added

- [#656] **[EasyCodingStandard]** Add configurable cache directory for changed files, closes [#650], thanks to [@marmichalski]
    ```yml
    # easy-coding-standard.yml
    parameters:
        cache_directory: .ecs_cache # defaults to sys_get_temp_dir() . '/_easy_coding_standard'
    ```
- [#645] **[PackageBuilder]** Add `AutowireSinglyImplementedCompilerPass` to prevent redundant singly-interface binding
- [#633] **[CodingStandard]** Add `ClassNameSuffixByParentFixer`, closes [#607]
- [#591] **[CodingStandard]** Add `BreakArrayListFixer` to break/inline array items over/under 120 chars
- [#585] **[CodingStandard]** Add `BreakMethodCallsFixer` to break/inline method calls over/under 120 chars
- [#584] **[EasyCodingStandard]** Add `fnmatch` check to `exclude_files`:
   ```yml
   # easy-coding-standard.yml
   parameters:
       exclude_files:
           - 'lib/PhpParser/Parser/Php5.php'
           - 'lib/PhpParser/Parser/Php7.php'
           # new
           - '*/lib/PhpParser/Parser/Php*.php'
   ```
- [#583] **[EasyCodingStandard]** Add `exclude_files` option to config
- [#612] **[PackageBuilder]** Add `CommandNaming` to get command name from the class name
- [#589] **[Statie], [EasyCodingStandard]** Add version printing on `-V` option in CLI, thanks to [@ostrolucky]
- [#585] **[TokenRunner]** Add `MethodCallWrapper` helper class to count line lengths of method calls

### Changed

- [#660] **EasyCodingStandard** Move from `checkers` to `services`, follow up to [#651]
    ```diff
    # easy-coding-standard.yml
    -    checkers:
    +    services:
             Symplify\CodingStandard\Fixer\Import\ImportNamespacedNameFixer:
                 include_doc_blocks: true

    # this is needed to respect yaml format
    -        - SlamCsFixer\FinalInternalClassFixer:
    +        SlamCsFixer\FinalInternalClassFixer: ~
- [#661] **[EasyCodingStandard]** Merge `parameters > skip_codes` to `parameters > skip` section
    ```diff
    # easy-coding-standard.yml
    parameters:
        skip:
            PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff:
                - 'packages/CodingStandard/src/Fixer/ClassNotation/LastPropertyAndFirstMethodSeparationFixer.php'

-       skip_codes:
            SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff.UselessDocComment:
                - '*packages*'
    ```
- [#651] **[EasyCodingStandard]** Move from mixture custom neon + Symfony service DI to Yaml;
    How to migrate from '*.neon' to '*.yml'? First, replace tabs with spaces and:
    ```diff
    # easy-coding-standard.yml
    -   includes:
    +   imports:
    -       - packages/EasyCodingStandard/config/psr2.neon
    +       - { resource: 'packages/EasyCodingStandard/config/psr2.yml' }

    -       - common/array.neon
    -       - common/control-structures.neon
    -       - common/docblock.neon
    +       - { resource: 'common/*.yml' }

        checkers:
            # class should be Abstact or Final
    -       - SlamCsFixer\FinalInternalClassFixer
    +       SlamCsFixer\FinalInternalClassFixer: ~

        parameters:
            skip:
                SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff:
    -               - *packages/CodingStandard/src/Sniffs/*/*Sniff.php
    +               - '*packages/CodingStandard/src/Sniffs/*/*Sniff.php'

            skip_codes:
    -           - SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff.UselessDocComment
    +           SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff.UselessDocComment:
    +               - '*src*'
    ```
- [#654] **[Statie]** Move from Yaml + Neon mixture to Yaml, similar to [#651]
    ```diff
    -multiline:  """
    -    one
    -    two
    -"""
    +multiline: >
    +    one
    +    two
    ```

### Fixed

- [#599] **[BetterReflectionDocBlock]** Fix respecting spaces of inner tag
- [#603] **[BetterReflectionDocBlock]** Fix union-types pre-slash clean + some more for `RemoveUselessDocBlockFixer`
- [1fcc92] **[BetterReflectionDocBlock]** Fix variadic detection
- [caf08e] **[BetterReflectionDocBlock]** Fix escaping and variadic param resolver
- [#606] **[CodingStandard]** Fix few `RemoveUselessDocBlockFixer` cases
- [#598] **[CodingStandard]** Fix `PropertyNameMatchingTypeFixer` for self cases, fixes [#597]
- [#640] **[EasyCodingStandard]** Fix pre-mature adding file to cache, fixes [#637]
- [#595] **[Statie]** Fix race condition for element sorting with configuration
- [59bdfc] **[Statie]** Fix non-root `index.html` route, fixes [#638]

### Removed

- [#647] **[Statie]** Removed deprecated `vendor/bin/statie push-to-github` command, use [Github pages on Travis](https://www.statie.org/docs/github-pages/#allow-travis-to-make-changes) instead
- [#647] **[CodingStandard]** Removed deprecated `LastPropertyAndFirstMethodSeparationFixer`, see [#594], use [`PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer`](https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/b7cc8727c7faa8ebe7cc4220daaaabe29751bc5c/src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php) instead; extends it if you need different space count
- [#647] **[CodingStandard]** Removed deprecated `Symplify\CodingStandard\Fixer\Strict\InArrayStrictFixer`, use [`PhpCsFixer\Fixer\Strict\StrictParamFixer`](https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/b7cc8727c7faa8ebe7cc4220daaaabe29751bc5c/src/Fixer/Strict/StrictParamFixer.php) instead, that does the same job
- [#647] **[Statie]** Removed deprecated `parameters > github_repository_slug` option, use `github_repository_source_directory` instead

    #### Before

    ```yml
    # statie.yml
    parameters:
        # <user>/<repository>
        github_repository_slug: "pehapkari/pehapkari.cz"
    ```

    #### After

    ```yml
    # statie.yml
    parameters:
        # https://github.com/<user>/<repository>/tree/master/<source>, where <source> is name of directory with Statie content
        github_repository_source_directory: "https://github.com/pehapkari/pehapkari.cz/tree/master/source"
    ```

- [#647] **[Statie]** Removed deprecated `statie.neon` note, use `statie.yml` instead
- [#647] **[EasyCodingStandard]** Removed deprecated bin files: `vendor/bin/easy-coding-standard` and `vendor/bin/easy-coding-standard.php`; use `vendor/bin/ecs` instead

- [#651] **[PackagesBuilder]** Removed `Symplify\PackageBuilder\Neon\Loader\NeonLoader` and `Symplify\PackageBuilder\Neon\NeonLoaderAwareKernelTrait`, that attempted to put Neon into Symfony Kernel, very poorly though

## [v3.2.0] - 2018-01-13

### Added

- [#570] **[EasyCodingStandard]** Add reporting for duplicated checkers
- [#577] **[Statie]** Add customizable `ObjectSorter` for [Generators](https://www.statie.org/docs/generators/) as `object_sorter` option in Generator configuration

    ```yaml
    # statie.yml
    parameters:
        generators:
            posts:
                # ...

                # Symplify\Statie\Generator\FileNameObjectSorter is used by default,
                # it sorts files newer to older posts work by default
                object_sorter: 'Website\Statie\Generator\DateObjectSorter'
    ```

    The sorter needs to implement `Symplify\Statie\Generator\Contract\ObjectSorterInterface` and be [loaded by composer](https://stackoverflow.com/a/25960097/1348344).

    It returns sorting function. For inspiration see [`Symplify\Statie\Generator\FileNameObjectSorter`](/packages/Statie/packages/Generator/src/FileNameObjectSorter.php) for inspiration.

- [#573] Add Psalm, thanks to [@muglug]

### Changed

- [#576] Bump to PHP CS Fixer 2.10 + minor lock to prevent BC breaks that happen for last 4 minor versions
- [#560] **[EasyCodingStandard]** Added `UnnecessaryStringConcatSniff` to `clean-code.neon` level, thanks to [@carusogabriel]
- [#560] **[EasyCodingStandard]** Added `PhpdocVarWithoutNameFixer` to `docblock.neon` level, thanks to [@carusogabriel]
- [#578] Use `@doesNotPerformAssertions` in tests, thanks to [@carusogabriel]

### Fixed

- [#574] **[Statie]** Fix path in `FileFinder` for Windows, thanks to [@tomasfejfar]
- [#562] **[Statie]** Fix `preg_quote()` escaping, thanks to [@tomasfejfar]

### Deprecated

- [#559] **[Statie]** Deprecated `push-to-github` command; use [Github Deploy](https://www.statie.org/docs/github-pages/) instead
- [#558] **[CodingStandard]** Deprecated `Symplify\CodingStandard\Fixer\Strict\InArrayStrictFixer`; use `PhpCsFixer\Fixer\Strict\StrictParamFixer` instead; thanks to [@carusogabriel]

## [v3.1.0] - 2018-01-02

### Added

- [#505] Added `CHANGELOG.md`

### Changed

- [#508] `RemoveUselessDocBlockFixer` is now configurable to accept types to remove with your own preferences

    ```yaml
    # easy-coding-standard.neon
    checkers:
        Symplify\CodingStandard\Fixer\Commenting\RemoveUselessDocBlockFixer:
            useless_types: ['mixed']
            # "[]" is default
    ```

- [3fce4e] **[EasyCodingStandard]** drop `LineLimitSebastianBergmanDiffer` over `PhpCsFixer\Differ\UnifiedDiffer`

## [v3.0.0] - 2017-12-09

## [v3.0.0-RC5] - 2017-12-09

### Added

- [#480] **[CodingStandard]** add `RemoveSuperfluousDocBlockWhitespaceFixer`, which removes 2 spaces in a row in doc blocks
- [#481] **[EasyCodingStandard]** add warning as error support, to make useful already existing Sniffs, closes [#477]

### Changed

- [#484] **[Statie]** add *dry-run* optiont to `StatieApplication` and `BeforeRenderEvent` to improve extendability, closes [#483]
- [9a9c0e] **[Statie]** use `statie.yml` config based on Symfony DI over "fake" `statie.neon` to prevent confusion, closes [#487]

    **Before**

    ```yml
    # statie.neon
    includes:
         - source/data/config.neon
    ```

    **After**
    ```yml
    # statie.yml
    imports:
        - { resource: 'source/data/config.yml' }
    ```

    **Before**
    ```yml
    services:
        -
            class: App\TranslationProvider
    ```

    **After**
    ```yml
    services:
        App\TranslationProvider: ~
    ```

### Removed

- [#488] **[CodingStandard]** drop `PropertyAndConstantSeparationFixer`, use `PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer` instead

## [v3.0.0-RC4] - 2017-12-06

### Added

- [#475] **[Statie]** added support for generators

```yaml
parameters:
    generators:
        # key name, it's nice to have for more informative error reports
        posts:
            # name of variable inside single such item
            variable: post
            # name of variable that contains all items
            varbiale_global: posts
            # directory, where to look for them
            path: '_posts'
            # which layout to use
            layout: '_layouts/@post.latte'
            # and url prefix, e.g. /blog/some-post.md
            route_prefix: 'blog'
            # an object that will wrap it's logic, you can add helper methods into it and use it in templates
            object: 'Symplify\Statie\Renderable\File\PostFile'
```

- [9b154d] **[Statie]** added `-vvv` CLI option for debug output

- [#473] bump to Symfony 4

- [#466] **[CodingStandard]** added `Symplify\CodingStandard\Sniffs\DeadCode\UnusedPublicMethodSniff`

- [#471] **[EasyCodingStandard]** various performance improvements
- [#473] **[EasyCodingStandard]** added `LineLimitSebastianBergmannDiffer` for nicer and compact diff outputs

- [#437] **[TokenRunner]** improved `AbstractSimpleFixerTestCase` with clearly named methods

### Changed

- [#475] **[Statie]** renamed `related_posts` filter to `related_items` with general usage (not only posts, but any other own generator element)

    **Before**
    ```twig
    {var $relatedPosts = ($post|relatedPosts)}
    ```

    **After**
    ```twig
    {var $relatedPosts = ($post|relatedItems)}
    ```

- [#473] **[CodingStandard]** use [ReflectionDocBlock](https://github.com/phpDocumentor/ReflectionDocBlock) for docblock analysis and modification

- [#474] **[EasyCodingStandard]** prefer diff report for changes over table report
- [#472] **[EasyCodingStandard]** improve `FileProcessorInterface`, improve performance via `CachedFileLoader`

### Removed

- [#475] **[Statie]** removed `postRoute`, only `prefix` is now available per item in generator

- [#476] **[CodingStandard]** dropped `NoInterfaceOnAbstractClassFixer`, not useful in practise

## [v3.0.0-RC3] - 2017-11-18

### Added

- [#452] **[CodingStandard]** `ClassStringToClassConstantFixer` now covers classes with double slashes: `SomeNamespace\\SomeClass`

## [v3.0.0-RC2] - 2017-11-17

### Added

- [0ab538] **[CodingStandard]** Added `BlankLineAfterStrictTypesFixer`

- [#443] **[EasyCodingStandard]** Added smaller common configs for better `--level` usage
- [#447] **[EasyCodingStandard]** Allow `-vvv` for ProgressBar + **27 % speed improvement**

- [#442] **[PackageBuilder]** Added `AutoloadFinder` to find nearest `/vendor/autoload.php`
- [#442] **[PackageBuilder]** Added `provideParameter()` and `changeParameter()` methods to `ParameterProvider`

### Changed

- [881577] **[EasyCodingStandard]** Removed `-checkers` suffix to make file naming consistent

### Fixed

- [#422] **[EasyCodingStandard]** Fix `skip_codes` option for `--fix` run

### Removed

- [#443] **[CodingStandard]** Dropped `FinalTestCase`, use `SlamCsFixer\FinalInternalClassFixer` instead
- [bc0cb0] **[EasyCodingStandard]** `php54.neon` set removed

## [v3.0.0-RC1] - 2017-11-12

### Added

- [#385] **[CodingStandard]** Added `RequireFollowedByAbsolutePathFixer`
- [#421] **[CodingStandard]** Added `ImportNamespacedNameFixer`
- [#427] **[CodingStandard]** Added `RemoveUselessDocBlockFixer`

- [#388] **[EasyCodingStandard]** Added support for ignoring particular sniff codes
- [#406] **[EasyCodingStandard]** Added support for ignoring particular codes and files, Thanks to [@ostrolucky]
- [#397] **[EasyCodingStandard]** Added validation to `exclude_checkers` option, Thanks to [@mzstic]

- [#431] **[PackageBuilder]** Added `--level` shortcut helper builder

### Changed

- [#399] **[Statie]** Filter `similarPosts` renamed to `relatedPosts`, closes [#386]

### Removed

- [#417] **[CodingStandard]** Dropped `InjectToConstructorInjectionFixer`, use [@RectorPHP] instead
- [#419] **[CodingStandard]** Dropped `ControllerRenderMethodLimitSniff` and `InvokableControllerSniff`, as related to SymbioticController
- [#432] **[CodingStandard]** Dropped `NewClassSniff`, use `NewWithBracesFixer` instead

- [#430] **[EasyCodingStandard]** Dropped ` --fixer-set` and `--checker-set` options for `show` command

- [#412] **[PackageBuilder]** Removed Nette related-features, make package mostly internall for Symplify

- [#404] **[SymbioticController]** package deprecated, closes [#402]

## [v2.5.0] - 2017-10-08

### Added

- [#374] **[CodingStandard]** Added customg matching with `fnmatch()` to PropertyNameMatchingTypeFixer

### Changed

- [#365] **[CodingStandard]** Bumped to PHP_CodeSniffer 3.1

### Fixed

- [#370] **[CodingStandard]** Fixed PropertyAndConstantSeparation for multiple classes
- [#372] **[CodingStandard]** Fixed incorrect namespace resolving in NoClassInstantiationSniff
- [#376] **[CodingStandard]** Fixed nested array in ArrayPropertyDefaultValueFixer
- [#381] **[CodingStandard]** Fixed DynamicProperySniff miss

- [#379] **[Statie]** Fixed source path bug, Thanks to [@chemix]

### Removed

- [#382] **[Statie]** Dropped broken and poor AMP support

## [v2.4.0] - 2017-09-20

### Added

- [#360] **[CodingStandard]** Added `--no-progress-bar` option, added `--no-error-table` option
- [#358] **[CodingStandard]** Added `AnnotateMagicContainerGetterFixer`
- [#356] **[CodingStandard]** Added `PropertyNameMatchingTypeFixer`
- [#346] **[CodingStandard]** Added `LastPropertyAndFirstMethodSeparationFixer`

- [#354] **[EasyCodingStandard]** Added [clean-code set](https://www.tomasvotruba.cz/blog/2017/09/18/4-simple-checkers-for-coding-standard-haters-but-clean-code-lovers/)
- [430fc5] **[EasyCodingStandard]** ConflictingGuard feature added, see [#333]
- [33f28a] **[EasyCodingStandard]** Add new rules to `symfony-checkers`
- [#342] **[EasyCodingStandard]** Add parser error reporting, Thanks [@webrouse]
- [#359] **[Statie]** Added Markdown support in `perex` in post

### Changed

- [d350b1] Bump to `slevomat/coding-standard` 4.0
- [bf8024] Bump to `friendsofphp/php-cs-fixer` 2.6

### Fixed

- [#347] **[CodingStandard]** Fix comment behind constant in `PropertyAndConstantSeparationFixer`
- [#355] **[EasyCodingStandard]** Fix `fnmatch` support both for relative and absolute paths

## [v2.3.0] - 2017-09-06

### Added

- [#360] **[CodingStandard]** Added `--no-progress-bar` option, added `--no-error-table` option
- [#338] **[CodingStandard]** Added `PropertyAndConstantSeparationFixer`
- [#332] **[CodingStandard]** Added `DynamicPropertySniff`
- [#320] **[CodingStandard]** Added `NoClassInstantiationSniff`
- [#311] **[CodingStandard]** Added `StandaloneLineInMultilineArrayFixer`
- [#283] **[CodingStandard]** Added `ExceptionNameFixer`

- [#330] **[EasyCodingStandard]** Added performance overview per checker via `--show-performance` options
- [#305] **[EasyCodingStandard]** Added MutualCheckerExcluder
- [#301] **[EasyCodingStandard]** Added `exclude_checkers` option to config, that allows to exclude specific checkers, e.g. inherited from configs
- [#290] **[EasyCodingStandard]** Added prepared sets with checkers
- [#285] **[EasyCodingStandard]** Added sniff set support to `show` command via `--sniff-set` option

### Changed

- [#334] **[CodingStandard]** `ArrayPropertyDefaultValueFixer` now allows single line comments, Thanks to [@vlastavesely]
- [#314] **[CodingStandard]** Make `ClassStringToClassConstantFixer` configurable

- [#337] **[EasyCodingStandard]** Fail table is less agressive
- [#287] **[EasyCodingStandard]** Allow SourceFinder to return Nette or Symfony Finder without any extra work

- [#295] **[Statie]** Rework similar posts concept from semi-AI magic to manual option `related_posts` in post file

### Fixed

- [#331] **[CodingStandard]** Fix `StandaloneLineInMultilieArray` with comments

- [#289] **[EasyCodingStandard]** Fix skipper for `fnmatch`

- [#328] **[Statie]** Removed hardcoded path from github filter, Thanks to [@crazko]
- [#327] **[Statie]** Fixed ability to set layout for posts
- [#325] **[Statie]** Disable decoration when AMP disabled

## [v2.2.0] - 2017-07-26

**News for EasyCodingStandard 2.2 explained in a post: https://www.tomasvotruba.cz/blog/2017/08/07/7-new-features-in-easy-coding-standard-22/**

### Added

- [#262] **[CodingStandard]** Added `ClassStringToClassConstantFixer`, convert `"SomeClass"` to `SomeClass::class`
- [#279] **[CodingStandard]** Added `MagicMethodsNamingFixer`, converts `__CONSTUCT()` to `__construct()`, Thanks [@SpacePossum]

- [#234] **[EasyCodingStandard]** Added support for custom spaces/tabs indentation in PHP-CS-Fixer
- [#266], [#272] **[EasyCodingStandard]** Added support for custom SourceProvider
- [#267] **[EasyCodingStandard]** Added ready to go configs with group of PHP-CS-Fixer fixers, `psr2`, `symfony`, `php70`, `php71` etc.

    Use in CLI:

    ```bash
    vendor/bin/ecs check src --config  vendor/symplify/easy-coding-standard/config/psr2-checkers.neon
    ```

    or `easy-coding-standard.neon`

    ```yaml
    includes:
        - vendor/symplify/easy-coding-standard/config/php70-checkers.neon
        - vendor/symplify/easy-coding-standard/config/php71-checkers.neon
        - vendor/symplify/easy-coding-standard/config/psr2-checkers.neon
    ```

- [#267] **[EasyCodingStandard]** Added option to `show` command, to show fixers from specific set from PHP-CS-Fixer:

    ```bash
    vendor/bin/ecs show --fixer-set Symfony
    ```

    And with configuration (parameters):

    ```bash
    vendor/bin/ecs show --fixer-set Symfony --with-config
    ```

- [#281] **[EasyCodingStandard]** Added info about no checkers loaded + allow checker merging in configuration

- [#276] **[PackageBuilder]** Added support for absolute path in `--config`, Thanks [@dg]
- [#225] **[PackageBuilder]** Added `ParameterProvider` for Nette

- [#243], [#258], [#275] **[Statie]** Added cache for AMP + various fixes
- [#252], [#256] **[Statie]** Added support for Latte code in highlight in posts, Thanks [@enumag]

### Changed

- [#278] **[CodingStandard]** **[EasyCodingStandard]** Bumped to **PHP-CS-Fixer 2.4** + applied many related fixes

- [#232] **[EasyCodingStandard]** Improved report after all is fixed
- [#255] **[EasyCodingStandard]** Fixers are sorted by priority
- [#239] **[EasyCodingStandard]** `PHP_EOL` is now default line-ending for PHP-CS-Fixer, Thanks [@dg]

### Fixed

- [#230] **[EasyCodingStandard]** Fixed Configuration BC break by PHP-CS-Fixer 2.3
- [#238] **[EasyCodingStandard]** Fixed caching invalidation for config including other configs
- [#257] **[EasyCodingStandard]** Error is propagated to exit code, Thanks [@dg]

- [#245] **[Statie]** Fixed Configuration in ParametersProvider

### Deprecated

- [#240] **[CodingStandard]** Deprecated `VarPropertyCommentSniff`, use `SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff` instead
- [#264] **[CodingStandard]** Deprecated `ClassNamesWithoutPreSlashSniff`, use `\SlevomatCodingStandard\Sniffs\Namespaces\ReferenceUsedNamesOnlySniff` instead
- [#282] **[CodingStandard]** Deprecated `ForbiddenTraitSniff`, was too strict

## [v2.1.0] - 2017-07-04

### Added

- [#165] **[CodingStandard]** added `ArrayPropertyDefaultValueFixer`; require default values for array property types

    ```php
    class SomeClass
    {
        /**
         * @var int[]
         */
        public $property = []; // here!
    }
    ```

    Thanks [@keradus] and [@SpacePossum]

- [#190] **[EasyCodingStandard]** add show command to display all loaded checkers
- [#194] **[EasyCodingStandard]** added shorter CLI alternative: `vendor/bin/ecs`
- [#198] **[EasyCodingStandard]** allow local config with `--config` option
- [#217] **[EasyCodingStandard]** added "Did you mean" feature for sniff configuration typos
- [#215] **[EasyCodingStandard]** allow checker with empty configuration; this is possible now:

    ```yml
    checkers:
        PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff:
        # someTemporaryCommentedConfig: value
    ```

    Thanks [@dg]

- [#197] **[PackageBuilder]** added `AbstractCliKernel` for CLI apps bootstrapping
- [#199] **[PackageBuilder]** added `ConfigFilePathHelper` for CLI and local config detection
- [#223] **[PackageBuilder]** NeonLoader - validate allowed sections feature added
- [#211] **[PackageBuilder]** improve configs - allow including for `*.neon` and `*.yml`, add `NeonLoaderAwareKernelTrait` for `*.neon` support in `Kernel`

- [#197] **[Statie]** add configuration via `statie.neon`
- [#201] **[Statie]** AMP support added

- [#222] added Code of Conduct based on Github's recommendation

### Changed

- [#188] **[CodingStandard]** add all rules to `README.md`

- [#221] **[EasyCodingStandard]** throw nicer exception on Container build fail
- [#214] **[EasyCodingStandard]** migrate RunCommand to more flexible Configuration service

- [#190] **[PackageBuilder]** `DefinitionCollector::loadCollectorWithType()` now allows multiple `$collectors`
- [#212] **[PackageBuilder]** add exception for missing file

- [#224] **[Statie]** use local `statie.neon` config file over global loading + use `underscore_case` (due to Symfony) - **BC BREAK!**
- [#196] **[Statie]** improved message for Latte parser exception
- [#195] **[Statie]** improved NEON parser error exception, closes [#99]

### Fixed

- [b45335] **[EasyCodingStandard]** fix missing `nette\robot-loader` dependency
- [b02535] **[EasyCodingStandard]** fix ChangedFilesDetector for missing config file

## [v2.0.0] - 2017-06-16

### Added

- [#179] **[EasyCodingStandard]** check for unused skipped errors and report them (inspired by [@phpstan])

### Changed

- [#183] **[EasyCodingStandard]** **[CodingStandard]** use squizlabs/PHP_CodeSniffer 3.0.1
- [#179] **[EasyCodingStandard]** use Symfony\DependencyInjection instead of Nette\DI, due to [new Symfony 3.3 features](https://www.tomasvotruba.cz/blog/2017/05/07/how-to-refactor-to-new-dependency-injection-features-in-symfony-3-3/)

- [#184] **[Statie]** use Symfony\DependencyInjection instead of Nette\DI

- [#173] use Coveralls over Scrutinizerfor code coverage

### Removed

Based on discussion with friends and maintainers, I've found there are better managed and actively used packages, that provide similar features as few Simplify packages.

- [#170] **[EventDispatcher]** package deprecated in favor of [contributte/event-dispatcher]
- [#162] **[DefaultAutowire]** package deprecated in favor of Symfony 3.3 `_defaults` section
- [#186] **[ModularLatteFilter]** package deprecated in favor of https://github.com/contributte/latte
- [#182] **[ModularRouting]** package deprecated based poor usage and discussion in [#181]
- [#184] **[Statie]** dropped translation support, not very extensive and shown unable in practise, implement own simple filter instead

## [v2.0.0-RC3] - 2017-05-05

### Changed

- [#155] bump min version to Symfony 3.3

### Fixed

- [#157] **[CodingStandard]** fix property docblock sniff for multiple annotations
- [#164] **[SymbioticController]** fixed typo in nette application request event name, Thanks [@Lexinek]

### Removed

- [#155] **[AutoServiceRegistration]** package deprecated
    - Use [@Symfony] 3.3 PSR-4 service autodiscovery: symfony/symfony#21289
- [#155] **[ControllerAutowire]** package deprecated
    - Use [@Symfony] 3.3 `AbstractController` symfony/symfony#22157
    - Use [@Symfony] 3.3 service PSR-4 autodiscovery: symfony/symfony#21289
- [#155] **[ServiceDefinitionDecorator]** package deprecated
    - Use `_instanceof` [@Symfony] 3.3: https://symfony.com/blog/new-in-symfony-3-3-simpler-service-configuration#interface-based-service-configuration

For more deprecation details see https://www.tomasvotruba.cz/blog/2017/05/29/symplify-packages-deprecations-brought-by-symfony-33/

## [v2.0.0-RC2] - 2017-04-27

### Added

- [#144] **[CodingStandard]** added new sniffs
    - `Symplify\CodingStandard\Sniffs\Architecture\ForbiddenTraitSniff`
    - `Symplify\CodingStandard\Sniffs\Commenting\VarConstantCommentSniff`
    - `Symplify\CodingStandard\Sniffs\Controller\ControllerRenderMethodLimitSniff`
    - `Symplify\CodingStandard\Sniffs\Controller\InvokableControllerSniff`

- [#149] **[CodingStandard]** added `Symplify\CodingStandard\Sniffs\Classes\EqualInterfaceImplementationSniff`
- [#149] **[CodingStandard]** added `Symplify\CodingStandard\Sniffs\Debug\CommentedOutCodeSniff`
- [#152] **[CodingStandard]** check for duplicated checker added - https://github.com/Symplify/Symplify/pull/152/files#diff-9c8034d27d44f02880909bfad4a7f853

- [#150] **[Statie]** decouple Latte related files to FlatWhite sub-package

### Changed

- [#151] **[EasyCodingStandard]** Nette\DI conControllerRenderMethodLimitSnifffig loading style added, parameters are now in Container and sniffs/fixers are registered as services

### Fixed

- [#142] **[ControllerAutowire]** prevent duplicated controller registraction

### Removed

- [#144] **[CodingStandard]** drop sniffs duplicated in 3rd party packages
    - `Symplify\CodingStandard\Sniffs\Commenting\MethodCommentSniff`, replaced by `SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff`
    - `Symplify\CodingStandard\Sniffs\Commenting\MethodReturnTypeSniff`, replaced by `SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff`
- [#152] **[CodingStandard]** removed unused sniff `Symplify\CodingStandard\Sniffs\Commenting\ComponentFactoryCommentSniff`
- [#153] **[SymfonySecurityVoters]** package deprecated, for no practical use

## [v2.0.0-RC1] - 2017-04-15

### Changed

- badges improvements

### Fixed

- fixed missing composer dependencies after subsplit

[comment]: # (links to issues, PRs and release diffs)

[#505]: https://github.com/Symplify/Symplify/pull/505
[#488]: https://github.com/Symplify/Symplify/pull/488
[#484]: https://github.com/Symplify/Symplify/pull/484
[#481]: https://github.com/Symplify/Symplify/pull/481
[#480]: https://github.com/Symplify/Symplify/pull/480
[#476]: https://github.com/Symplify/Symplify/pull/476
[#475]: https://github.com/Symplify/Symplify/pull/475
[#474]: https://github.com/Symplify/Symplify/pull/474
[#473]: https://github.com/Symplify/Symplify/pull/473
[#472]: https://github.com/Symplify/Symplify/pull/472
[#471]: https://github.com/Symplify/Symplify/pull/471
[#466]: https://github.com/Symplify/Symplify/pull/466
[#437]: https://github.com/Symplify/Symplify/pull/437
[#487]: https://github.com/Symplify/Symplify/issues/487
[#483]: https://github.com/Symplify/Symplify/issues/483
[#477]: https://github.com/Symplify/Symplify/issues/477
[v3.0.1]: https://github.com/Symplify/Symplify/compare/v3.0.0...v3.0.1
[v3.0.0]: https://github.com/Symplify/Symplify/compare/v3.0.0-RC5...v3.0.0
[v3.0.0-RC5]: https://github.com/Symplify/Symplify/compare/v3.0.0-RC4...v3.0.0-RC5
[#452]: https://github.com/Symplify/Symplify/pull/452
[v3.0.0-RC4]: https://github.com/Symplify/Symplify/compare/v3.0.0-RC3...v3.0.0-RC4
[v3.0.0-RC3]: https://github.com/Symplify/Symplify/compare/v3.0.0-RC2...v3.0.0-RC3
[v3.0.0-RC2]: https://github.com/Symplify/Symplify/compare/v3.0.0-RC1...v3.0.0-RC2
[v3.0.0-RC1]: https://github.com/Symplify/Symplify/compare/v2.5.0...v3.0.0-RC1
[v2.5.0]: https://github.com/Symplify/Symplify/compare/v2.4.0...v2.5.0
[v2.4.0]: https://github.com/Symplify/Symplify/compare/v2.3.0...v2.4.0
[v2.3.0]: https://github.com/Symplify/Symplify/compare/v2.2.0...v2.3.0
[v2.2.0]: https://github.com/Symplify/Symplify/compare/v2.1.0...v2.2.0
[v2.1.0]: https://github.com/Symplify/Symplify/compare/v2.0.0...v2.1.0
[v2.0.0]: https://github.com/Symplify/Symplify/compare/v2.0.0-RC3...v2.0.0
[v2.0.0-RC3]: https://github.com/Symplify/Symplify/compare/v2.0.0-RC2...v2.0.0-RC3
[v2.0.0-RC2]: https://github.com/Symplify/Symplify/compare/v2.0.0-RC1...v2.0.0-RC2
[v2.0.0-RC1]: https://github.com/Symplify/Symplify/compare/v1.4.10...v2.0.0-RC1
[#447]: https://github.com/Symplify/Symplify/pull/447
[#443]: https://github.com/Symplify/Symplify/pull/443
[#442]: https://github.com/Symplify/Symplify/pull/442
[#432]: https://github.com/Symplify/Symplify/pull/432
[#431]: https://github.com/Symplify/Symplify/pull/431
[#430]: https://github.com/Symplify/Symplify/pull/430
[#427]: https://github.com/Symplify/Symplify/pull/427
[#422]: https://github.com/Symplify/Symplify/issues/422
[#421]: https://github.com/Symplify/Symplify/pull/421
[#419]: https://github.com/Symplify/Symplify/pull/419
[#417]: https://github.com/Symplify/Symplify/pull/417
[#412]: https://github.com/Symplify/Symplify/pull/412
[#406]: https://github.com/Symplify/Symplify/pull/406
[#404]: https://github.com/Symplify/Symplify/pull/404
[#399]: https://github.com/Symplify/Symplify/pull/399
[#397]: https://github.com/Symplify/Symplify/pull/397
[#388]: https://github.com/Symplify/Symplify/pull/388
[#385]: https://github.com/Symplify/Symplify/pull/385
[#382]: https://github.com/Symplify/Symplify/pull/382
[#381]: https://github.com/Symplify/Symplify/pull/381
[#379]: https://github.com/Symplify/Symplify/pull/379
[#376]: https://github.com/Symplify/Symplify/pull/376
[#374]: https://github.com/Symplify/Symplify/pull/374
[#372]: https://github.com/Symplify/Symplify/issues/372
[#370]: https://github.com/Symplify/Symplify/pull/370
[#365]: https://github.com/Symplify/Symplify/pull/365
[#360]: https://github.com/Symplify/Symplify/pull/360
[#359]: https://github.com/Symplify/Symplify/pull/359
[#358]: https://github.com/Symplify/Symplify/pull/358
[#356]: https://github.com/Symplify/Symplify/pull/356
[#355]: https://github.com/Symplify/Symplify/pull/355
[#354]: https://github.com/Symplify/Symplify/pull/354
[#347]: https://github.com/Symplify/Symplify/pull/347
[#346]: https://github.com/Symplify/Symplify/pull/346
[#342]: https://github.com/Symplify/Symplify/pull/342
[#338]: https://github.com/Symplify/Symplify/pull/338
[#337]: https://github.com/Symplify/Symplify/pull/337
[#334]: https://github.com/Symplify/Symplify/pull/334
[#332]: https://github.com/Symplify/Symplify/pull/332
[#331]: https://github.com/Symplify/Symplify/pull/331
[#330]: https://github.com/Symplify/Symplify/pull/330
[#328]: https://github.com/Symplify/Symplify/pull/328
[#327]: https://github.com/Symplify/Symplify/pull/327
[#325]: https://github.com/Symplify/Symplify/pull/325
[#320]: https://github.com/Symplify/Symplify/pull/320
[#314]: https://github.com/Symplify/Symplify/pull/314
[#311]: https://github.com/Symplify/Symplify/pull/311
[#305]: https://github.com/Symplify/Symplify/pull/305
[#301]: https://github.com/Symplify/Symplify/pull/301
[#295]: https://github.com/Symplify/Symplify/pull/295
[#290]: https://github.com/Symplify/Symplify/pull/290
[#289]: https://github.com/Symplify/Symplify/pull/289
[#287]: https://github.com/Symplify/Symplify/pull/287
[#285]: https://github.com/Symplify/Symplify/pull/285
[#283]: https://github.com/Symplify/Symplify/pull/283
[#282]: https://github.com/Symplify/Symplify/pull/282
[#281]: https://github.com/Symplify/Symplify/pull/281
[#279]: https://github.com/Symplify/Symplify/pull/279
[#278]: https://github.com/Symplify/Symplify/pull/278
[#276]: https://github.com/Symplify/Symplify/pull/276
[#275]: https://github.com/Symplify/Symplify/issues/275
[#272]: https://github.com/Symplify/Symplify/pull/272
[#267]: https://github.com/Symplify/Symplify/pull/267
[#264]: https://github.com/Symplify/Symplify/pull/264
[#262]: https://github.com/Symplify/Symplify/pull/262
[#257]: https://github.com/Symplify/Symplify/pull/257
[#256]: https://github.com/Symplify/Symplify/pull/256
[#255]: https://github.com/Symplify/Symplify/pull/255
[#245]: https://github.com/Symplify/Symplify/pull/245
[#240]: https://github.com/Symplify/Symplify/pull/240
[#239]: https://github.com/Symplify/Symplify/pull/239
[#238]: https://github.com/Symplify/Symplify/pull/238
[#234]: https://github.com/Symplify/Symplify/pull/234
[#232]: https://github.com/Symplify/Symplify/pull/232
[#230]: https://github.com/Symplify/Symplify/pull/230
[#225]: https://github.com/Symplify/Symplify/pull/225
[#224]: https://github.com/Symplify/Symplify/pull/224
[#223]: https://github.com/Symplify/Symplify/pull/223
[#222]: https://github.com/Symplify/Symplify/pull/222
[#221]: https://github.com/Symplify/Symplify/pull/221
[#217]: https://github.com/Symplify/Symplify/pull/217
[#215]: https://github.com/Symplify/Symplify/pull/215
[#214]: https://github.com/Symplify/Symplify/pull/214
[#212]: https://github.com/Symplify/Symplify/pull/212
[#211]: https://github.com/Symplify/Symplify/pull/211
[#201]: https://github.com/Symplify/Symplify/pull/201
[#199]: https://github.com/Symplify/Symplify/pull/199
[#198]: https://github.com/Symplify/Symplify/pull/198
[#197]: https://github.com/Symplify/Symplify/pull/197
[#196]: https://github.com/Symplify/Symplify/pull/196
[#195]: https://github.com/Symplify/Symplify/pull/195
[#194]: https://github.com/Symplify/Symplify/pull/194
[#190]: https://github.com/Symplify/Symplify/pull/190
[#188]: https://github.com/Symplify/Symplify/pull/188
[#186]: https://github.com/Symplify/Symplify/pull/186
[#184]: https://github.com/Symplify/Symplify/pull/184
[#183]: https://github.com/Symplify/Symplify/pull/183
[#182]: https://github.com/Symplify/Symplify/pull/182
[#179]: https://github.com/Symplify/Symplify/pull/179
[#173]: https://github.com/Symplify/Symplify/pull/173
[#170]: https://github.com/Symplify/Symplify/pull/170
[#165]: https://github.com/Symplify/Symplify/pull/165
[#164]: https://github.com/Symplify/Symplify/pull/164
[#162]: https://github.com/Symplify/Symplify/pull/162
[#157]: https://github.com/Symplify/Symplify/pull/157
[#155]: https://github.com/Symplify/Symplify/pull/155
[#153]: https://github.com/Symplify/Symplify/pull/153
[#152]: https://github.com/Symplify/Symplify/pull/152
[#151]: https://github.com/Symplify/Symplify/pull/151
[#150]: https://github.com/Symplify/Symplify/pull/150
[#149]: https://github.com/Symplify/Symplify/pull/149
[#144]: https://github.com/Symplify/Symplify/pull/144
[#142]: https://github.com/Symplify/Symplify/pull/142
[d350b1]: https://github.com/Symplify/Symplify/commit/d350b1c5ff8f763a41907068d6a5e9cbb6a13379
[bf8024]: https://github.com/Symplify/Symplify/commit/bf802422b9528946a8bd7e7f0331d858a9bf5740
[bc0cb0]: https://github.com/Symplify/Symplify/commit/bc0cb09d5e5166830ba4ad95fd4d0ba8f4bcacf4
[881577]: https://github.com/Symplify/Symplify/commit/881577af893ed1e73260f713153004be78aaf101
[430fc5]: https://github.com/Symplify/Symplify/commit/430fc59da26c5a43ccdbb2d2f8d75b2edff4aea6
[33f28a]: https://github.com/Symplify/Symplify/commit/33f28a03daafa76f7bbdad380348a736650e357b
[0ab538]: https://github.com/Symplify/Symplify/commit/0ab538bd53c971f6a7163485230a44658f613768
[#99]: https://github.com/Symplify/Symplify/issues/99
[#402]: https://github.com/Symplify/Symplify/issues/402
[#386]: https://github.com/Symplify/Symplify/issues/386
[#333]: https://github.com/Symplify/Symplify/issues/333
[#181]: https://github.com/Symplify/Symplify/issues/181
[#266]: https://github.com/Symplify/Symplify/pull/266
[#258]: https://github.com/Symplify/Symplify/pull/258
[#252]: https://github.com/Symplify/Symplify/pull/252
[#243]: https://github.com/Symplify/Symplify/pull/243
[@webrouse]: https://github.com/webrouse
[@vlastavesely]: https://github.com/vlastavesely
[@phpstan]: https://github.com/phpstan
[@ostrolucky]: https://github.com/ostrolucky
[@mzstic]: https://github.com/mzstic
[@keradus]: https://github.com/keradus
[@enumag]: https://github.com/enumag
[@dg]: https://github.com/dg
[@crazko]: https://github.com/crazko
[@chemix]: https://github.com/chemix
[@Symfony]: https://github.com/Symfony
[@SpacePossum]: https://github.com/SpacePossum
[@RectorPHP]: https://github.com/RectorPHP
[@Lexinek]: https://github.com/Lexinek
[b45335]: https://github.com/Symplify/Symplify/commit/b45335c4e3674f7d0348ab31f1c359695d9d1d51
[b02535]: https://github.com/Symplify/Symplify/commit/b025353e06364cdb06f81d535dcb1d70b76b3a53
[9b154d]: https://github.com/Symplify/Symplify/commit/9b154d9b6e88075e14b6812613bce7c1a2a79daa
[9a9c0e]: https://github.com/Symplify/Symplify/commit/9a9c0e61d0b7af073d3819e4c4798a251eca1f14
[#508]: https://github.com/Symplify/Symplify/pull/508
[contributte/event-dispatcher]: https://github.com/contributte/event-dispatcher
[3fce4e]: https://github.com/Symplify/Symplify/commit/3fce4e4168a67efe3d7e19be5fd8dc231d352c76
[v3.1.0]: https://github.com/Symplify/Symplify/compare/v3.0.1...v3.1.0
[#558]: https://github.com/Symplify/Symplify/pull/558
[@carusogabriel]: https://github.com/carusogabriel
[v3.2.0]: https://github.com/Symplify/Symplify/compare/v3.1.0...v3.2.0
[#577]: https://github.com/Symplify/Symplify/pull/577
[#576]: https://github.com/Symplify/Symplify/pull/576
[#574]: https://github.com/Symplify/Symplify/pull/574
[#573]: https://github.com/Symplify/Symplify/pull/573
[#570]: https://github.com/Symplify/Symplify/pull/570
[#562]: https://github.com/Symplify/Symplify/pull/562
[#560]: https://github.com/Symplify/Symplify/pull/560
[#559]: https://github.com/Symplify/Symplify/pull/559
[@tomasfejfar]: https://github.com/tomasfejfar
[@muglug]: https://github.com/muglug
[#583]: https://github.com/Symplify/Symplify/pull/583
[#578]: https://github.com/Symplify/Symplify/pull/578
[caf08e]: https://github.com/Symplify/Symplify/commit/caf08e93b2627e1e981493349957f4e49d55cd6a
[59bdfc]: https://github.com/Symplify/Symplify/commit/59bdfc3c0d4945f946d17f127e6a329384d5bab8
[257e5b]: https://github.com/Symplify/Symplify/commit/257e5bc68b9341f8fbe1e306d08f736038d6d626
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