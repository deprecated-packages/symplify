# Changelog for Symplify 2.x

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

PRs and issues are linked, so you can find more about it. Thanks to [ChangelogLinker](https://github.com/Symplify/ChangelogLinker).

## [v2.5.0] - 2017-10-08

### Added

- [#374] **CodingStandard** Added customg matching with `fnmatch()` to PropertyNameMatchingTypeFixer

### Changed

- [#365] **CodingStandard** Bumped to PHP_CodeSniffer 3.1

### Fixed

- [#379] **Statie** Fixed source path bug, Thanks to [@chemix]

#### CodingStandard

- [#370] Fixed `PropertyAndConstantSeparation` for multiple classes
- [#372] Fixed incorrect namespace resolving in `NoClassInstantiationSniff`
- [#376] Fixed nested array in `ArrayPropertyDefaultValueFixer`
- [#381] Fixed `DynamicProperySniff` miss

### Removed

- [#382] **Statie** Dropped broken and poor AMP support

## [v2.4.0] - 2017-09-20

### Added

- [#359] **Statie** Added Markdown support in `perex` in post

#### CodingStandard

- [#360] Added `--no-progress-bar` option, added `--no-error-table` option
- [#358] Added `AnnotateMagicContainerGetterFixer`
- [#356] Added `PropertyNameMatchingTypeFixer`
- [#346] Added `LastPropertyAndFirstMethodSeparationFixer`

#### EasyCodingStandard

- [#354] Added [clean-code set](https://www.tomasvotruba.cz/blog/2017/09/18/4-simple-checkers-for-coding-standard-haters-but-clean-code-lovers/)
- [430fc5] ConflictingGuard feature added, see [#333]
- [33f28a] Add new rules to `symfony-checkers`
- [#342] Add parser error reporting, Thanks [@webrouse]

### Changed

- [d350b1] Bump to `slevomat/coding-standard` 4.0
- [bf8024] Bump to `friendsofphp/php-cs-fixer` 2.6

### Fixed

- [#347] **CodingStandard** Fix comment behind constant in `PropertyAndConstantSeparationFixer`
- [#355] **EasyCodingStandard** Fix `fnmatch` support both for relative and absolute paths

## [v2.3.0] - 2017-09-06

### Added

#### CodingStandard

- [#360] Added `--no-progress-bar` option, added `--no-error-table` option
- [#338] Added `PropertyAndConstantSeparationFixer`
- [#332] Added `DynamicPropertySniff`
- [#320] Added `NoClassInstantiationSniff`
- [#311] Added `StandaloneLineInMultilineArrayFixer`
- [#283] Added `ExceptionNameFixer`

#### EasyCodingStandard

- [#330] Added performance overview per checker via `--show-performance` options
- [#305] Added `MutualCheckerExcluder`
- [#301] Added `exclude_checkers` option to config, that allows to exclude specific checkers, e.g. inherited from configs
- [#290] Added prepared sets with checkers
- [#285] Added sniff set support to `show` command via `--sniff-set` option

### Changed

- [#295] **Statie** Rework similar posts concept from semi-AI magic to manual option `related_posts` in post file

#### CodingStandard

- [#334] `ArrayPropertyDefaultValueFixer` now allows single line comments, Thanks to [@vlastavesely]
- [#314] Make `ClassStringToClassConstantFixer` configurable

#### EasyCodingStandard

- [#337] Fail table is less agressive
- [#287] Allow SourceFinder to return Nette or Symfony Finder without any extra work

### Fixed

- [#331] **CodingStandard** Fix `StandaloneLineInMultilieArray` with comments
- [#289] **EasyCodingStandard** Fix skipper for `fnmatch`

#### Statie

- [#328] Removed hardcoded path from github filter, Thanks to [@crazko]
- [#327] Fixed ability to set layout for posts
- [#325] Disable decoration when AMP disabled

## [v2.2.0] - 2017-07-26

**News for EasyCodingStandard 2.2 explained in a post: [7 new features in EasyCodingStandard 2.2](https://www.tomasvotruba.cz/blog/2017/08/07/7-new-features-in-easy-coding-standard-22)**

### Added

#### CodingStandard

- [#262] Added `ClassStringToClassConstantFixer`, convert `"SomeClass"` to `SomeClass::class`
- [#279] Added `MagicMethodsNamingFixer`, converts `__CONSTUCT()` to `__construct()`, Thanks [@SpacePossum]

#### EasyCodingStandard

- [#234] Added support for custom spaces/tabs indentation in PHP-CS-Fixer
- [#266], [#272] Added support for custom SourceProvider
- [#267] Added ready to go configs with group of PHP-CS-Fixer fixers, `psr2`, `symfony`, `php70`, `php71` etc.
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
- [#267] Added option to `show` command, to show fixers from specific set from PHP-CS-Fixer:
    ```bash
    vendor/bin/ecs show --fixer-set Symfony
    ```

    And with configuration (parameters):

    ```bash
    vendor/bin/ecs show --fixer-set Symfony --with-config
    ```

- [#281] Added info about no checkers loaded + allow checker merging in configuration

#### PackageBuilder

- [#276] Added support for absolute path in `--config`, Thanks [@dg]
- [#225] Added `ParameterProvider` for Nette

#### Statie

- [#243], [#258], [#275] Added cache for AMP + various fixes
- [#252], [#256] Added support for Latte code in highlight in posts, Thanks [@enumag]

### Changed

- [#278] **CodingStandard** **EasyCodingStandard** Bumped to **PHP-CS-Fixer 2.4** + applied many related fixes

#### EasyCodingStandard

- [#232] Improved report after all is fixed
- [#255] Fixers are sorted by priority
- [#239] `PHP_EOL` is now default line-ending for PHP-CS-Fixer, Thanks [@dg]

### Fixed

- [#245] **Statie** Fixed Configuration in ParametersProvider

#### EasyCodingStandard

- [#230] Fixed Configuration BC break by PHP-CS-Fixer 2.3
- [#238] Fixed caching invalidation for config including other configs
- [#257] Error is propagated to exit code, Thanks [@dg]

### Deprecated

#### CodingStandard

- [#240] Deprecated `VarPropertyCommentSniff`, use `SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff` instead
- [#264] Deprecated `ClassNamesWithoutPreSlashSniff`, use `\SlevomatCodingStandard\Sniffs\Namespaces\ReferenceUsedNamesOnlySniff` instead
- [#282] Deprecated `ForbiddenTraitSniff`, was too strict

## [v2.1.0] - 2017-07-04

### Added

- [#165] **CodingStandard** added `ArrayPropertyDefaultValueFixer`; require default values for array property types

    ```php
    class SomeClass
    {
        /**
         * @var int[]
         */
        public $property = []; // here!
    }
    ```

    Thanks [@keradus] and [@SpacePossum] for patient feedback and help with my first Fixer ever

#### EasyCodingStandard

- [#190] Added `show` command to display all loaded checkers
- [#194] Added shorter CLI alternative: `vendor/bin/ecs`
- [#198] Allow local config with `--config` option
- [#217] Added "Did you mean" feature for sniff configuration typos
- [#215] Allow checker with empty configuration; this is possible now:
    ```yaml
    checkers:
        PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff:
        # someTemporaryCommentedConfig: value
    ```

    Thanks [@dg]

#### PackageBuilder

- [#197] Added `AbstractCliKernel` for CLI apps bootstrapping
- [#199] Added `ConfigFilePathHelper` for CLI and local config detection
- [#223] `NeonLoader` - validate allowed sections feature added
- [#211] Improve configs - allow including for `*.neon` and `*.yml`, add `NeonLoaderAwareKernelTrait` for `*.neon` support in `Kernel`

#### Statie

- [#197] Add configuration via `statie.neon`
- [#201] AMP support added

### Changed

- [#188] **CodingStandard** add all rules to `README.md`

#### EasyCodingStandard

- [#221] throw nicer exception on Container build fail
- [#214] migrate RunCommand to more flexible Configuration service

#### PackageBuilder

- [#190] `DefinitionCollector::loadCollectorWithType()` now allows multiple `$collectors`
- [#212] Add exception for missing file

#### Statie

- [#224] Use local `statie.neon` config file over global loading + use `underscore_case` (due to Symfony) - **BC BREAK!**
- [#196] Improved message for Latte parser exception
- [#195] Improved NEON parser error exception, closes [#99]

### Fixed

#### EasyCodingStandard

- [b45335] Fix missing `nette\robot-loader` dependency
- [b02535] Fix ChangedFilesDetector for missing config file

## [v2.0.0] - 2017-06-16

### Added

- [#179] **EasyCodingStandard** check for unused skipped errors and report them (inspired by [@phpstan])
- [#150] **Statie** decouple Latte related files to FlatWhite sub-package

#### CodingStandard

- [#144] Added new sniffs
    - `Symplify\CodingStandard\Sniffs\Architecture\ForbiddenTraitSniff`
    - `Symplify\CodingStandard\Sniffs\Commenting\VarConstantCommentSniff`
    - `Symplify\CodingStandard\Sniffs\Controller\ControllerRenderMethodLimitSniff`
    - `Symplify\CodingStandard\Sniffs\Controller\InvokableControllerSniff`
- [#149] Added `Symplify\CodingStandard\Sniffs\Classes\EqualInterfaceImplementationSniff`
- [#149] Added `Symplify\CodingStandard\Sniffs\Debug\CommentedOutCodeSniff`
- [#152] Check for duplicated checker added - https://github.com/Symplify/Symplify/pull/152/files#diff-9c8034d27d44f02880909bfad4a7f853

### Changed

- [#155] bump min version to Symfony 3.3
- [#184] **Statie** use `Symfony\DependencyInjection` instead of `Nette\DI`

#### EasyCodingStandard

- [#179] use `Symfony\DependencyInjection` instead of `Nette\DI`, due to [new Symfony 3.3 features](https://www.tomasvotruba.cz/blog/2017/05/07/how-to-refactor-to-new-dependency-injection-features-in-symfony-3-3/)
- [#151] `Nette\DI` config loading style added, parameters are now in Container and sniffs/fixers are registered as services

### Fixed

- [#157] **CodingStandard** fix property docblock sniff for multiple annotations
- [#164] **SymbioticController** fixed typo in nette application request event name, Thanks [@Lexinek]
- [#142] **ControllerAutowire** prevent duplicated controller registraction

### Removed

- [#184] **Statie** dropped translation support, not very extensive and shown unable in practise, implement own simple filter instead

#### CodingStandard

- [#144] Drop sniffs duplicated in 3rd party packages
    - `Symplify\CodingStandard\Sniffs\Commenting\MethodCommentSniff`, use `SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff` instead
    - `Symplify\CodingStandard\Sniffs\Commenting\MethodReturnTypeSniff`, use `SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff` instead
- [#152] Removed unused sniff `Symplify\CodingStandard\Sniffs\Commenting\ComponentFactoryCommentSniff`
[Based on discussion with friends and maintainers](https://www.tomasvotruba.cz/blog/2017/05/29/symplify-packages-deprecations-brought-by-symfony-33/), I've found there are better managed and actively used packages, that provide similar features as few Simplify packages. So **these packages were deprecated**:
- [#170] **EventDispatcher** package deprecated in favor of [contributte/event-dispatcher]
- [#162] **DefaultAutowire** package deprecated in favor of Symfony 3.3 `_defaults` section
- [#186] **ModularLatteFilter** package deprecated in favor of https://github.com/contributte/latte
- [#182] **ModularRouting** package deprecated based poor usage and discussion in [#181]
- [#155] **AutoServiceRegistration** package deprecated
    - Use [@Symfony] 3.3 PSR-4 service autodiscovery
- [#155] **ControllerAutowire** package deprecated
    - Use [@Symfony] 3.3 `AbstractController`
    - Use [@Symfony] 3.3 service PSR-4 autodiscovery
- [#155] **ServiceDefinitionDecorator** package deprecated
    - Use `_instanceof` [@Symfony] 3.3: https://symfony.com/blog/new-in-symfony-3-3-simpler-service-configuration#interface-based-service-configuration
- [#153] **SymfonySecurityVoters** package deprecated, for no practical use

[comment]: # (links to issues, PRs and release diffs)

[#382]: https://github.com/Symplify/Symplify/pull/382
[#381]: https://github.com/Symplify/Symplify/pull/381
[#379]: https://github.com/Symplify/Symplify/pull/379
[#376]: https://github.com/Symplify/Symplify/pull/376
[#374]: https://github.com/Symplify/Symplify/pull/374
[#372]: https://github.com/Symplify/Symplify/pull/372
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
[#333]: https://github.com/Symplify/Symplify/pull/333
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
[#275]: https://github.com/Symplify/Symplify/pull/275
[#272]: https://github.com/Symplify/Symplify/pull/272
[#267]: https://github.com/Symplify/Symplify/pull/267
[#266]: https://github.com/Symplify/Symplify/pull/266
[#264]: https://github.com/Symplify/Symplify/pull/264
[#262]: https://github.com/Symplify/Symplify/pull/262
[#258]: https://github.com/Symplify/Symplify/pull/258
[#257]: https://github.com/Symplify/Symplify/pull/257
[#256]: https://github.com/Symplify/Symplify/pull/256
[#255]: https://github.com/Symplify/Symplify/pull/255
[#252]: https://github.com/Symplify/Symplify/pull/252
[#245]: https://github.com/Symplify/Symplify/pull/245
[#243]: https://github.com/Symplify/Symplify/pull/243
[#240]: https://github.com/Symplify/Symplify/pull/240
[#239]: https://github.com/Symplify/Symplify/pull/239
[#238]: https://github.com/Symplify/Symplify/pull/238
[#234]: https://github.com/Symplify/Symplify/pull/234
[#232]: https://github.com/Symplify/Symplify/pull/232
[#230]: https://github.com/Symplify/Symplify/pull/230
[#225]: https://github.com/Symplify/Symplify/pull/225
[#224]: https://github.com/Symplify/Symplify/pull/224
[#223]: https://github.com/Symplify/Symplify/pull/223
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
[#182]: https://github.com/Symplify/Symplify/pull/182
[#181]: https://github.com/Symplify/Symplify/pull/181
[#179]: https://github.com/Symplify/Symplify/pull/179
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
[#99]: https://github.com/Symplify/Symplify/pull/99
[v2.5.0]: https://github.com/Symplify/Symplify/compare/v2.4.0...v2.5.0
[@webrouse]: https://github.com/webrouse
[@vlastavesely]: https://github.com/vlastavesely
[@phpstan]: https://github.com/phpstan
[@keradus]: https://github.com/keradus
[@enumag]: https://github.com/enumag
[@dg]: https://github.com/dg
[@crazko]: https://github.com/crazko
[@chemix]: https://github.com/chemix
[@Symfony]: https://github.com/Symfony
[@SpacePossum]: https://github.com/SpacePossum
[@Lexinek]: https://github.com/Lexinek