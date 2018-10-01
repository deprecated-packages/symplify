# Changelog for Symplify 3.x

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

PRs and issues are linked, so you can find more about it. Thanks to [ChangelogLinker](https://github.com/Symplify/ChangelogLinker).

## [v3.2.0] - 2018-01-13

### Added

- [#570] **EasyCodingStandard** Add reporting for duplicated checkers
- [#577] **Statie** Add customizable `ObjectSorter` for [Generators](https://www.statie.org/docs/generators/) as `object_sorter` option in Generator configuration

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

### Changed

- [#576] Bump to PHP CS Fixer 2.10 + minor lock to prevent BC breaks that happen for last 4 minor versions

#### EasyCodingStandard

- [#560] Added `UnnecessaryStringConcatSniff` to `clean-code.neon` level, thanks to [@carusogabriel]
- [#560] Added `PhpdocVarWithoutNameFixer` to `docblock.neon` level, thanks to [@carusogabriel]

### Fixed

#### Statie

- [#574] Fix path in `FileFinder` for Windows, thanks to [@tomasfejfar]
- [#562] Fix `preg_quote()` escaping, thanks to [@tomasfejfar]

### Deprecated

- [#559] **Statie** Deprecated `push-to-github` command; use [Github Deploy](https://www.statie.org/docs/github-pages/) instead
- [#558] **CodingStandard** Deprecated `Symplify\CodingStandard\Fixer\Strict\InArrayStrictFixer`; use `PhpCsFixer\Fixer\Strict\StrictParamFixer` instead; thanks to [@carusogabriel]

## [v3.1.0] - 2018-01-02

### Added

- [#505] Added `CHANGELOG.md`

### Changed

- [#508] **CodingStandard** `RemoveUselessDocBlockFixer` is now configurable to accept types to remove with your own preferences

    ```yaml
    # easy-coding-standard.neon
    checkers:
        Symplify\CodingStandard\Fixer\Commenting\RemoveUselessDocBlockFixer:
            useless_types: ['mixed']
            # "[]" is default
    ```
- [3fce4e] **EasyCodingStandard** drop `LineLimitSebastianBergmanDiffer` over `PhpCsFixer\Differ\UnifiedDiffer`

## [v3.0.0] - 2017-12-09

### Added

- [#473] bump to Symfony 4
- [#437] **TokenRunner** improved `AbstractSimpleFixerTestCase` with clearly named methods

#### Statie

- [#475] Added support for generators
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
- [9b154d] Added `-vvv` CLI option for debug output

#### EasyCodingStandard

- [#481] Add warning as error support, to make useful already existing Sniffs, closes [#477]
- [#473] Added `LineLimitSebastianBergmannDiffer` for nicer and compact diff outputs
- [#443] Added smaller common configs for better `--level` usage
- [#447] Allow `-vvv` for ProgressBar + **27 % speed improvement**
- [#388] Added support for ignoring particular sniff codes
- [#406] Added support for ignoring particular codes and files, Thanks to [@ostrolucky]
- [#397] Added validation to `exclude_checkers` option, Thanks to [@mzstic]

#### Coding Standard

- [#480] Added `RemoveSuperfluousDocBlockWhitespaceFixer`, which removes 2 spaces in a row in doc blocks
- [#466] Added `Symplify\CodingStandard\Sniffs\DeadCode\UnusedPublicMethodSniff`
- [#452] `ClassStringToClassConstantFixer` now covers classes with double slashes: `SomeNamespace\\SomeClass`
- [0ab538] Added `BlankLineAfterStrictTypesFixer`
- [#385] Added `RequireFollowedByAbsolutePathFixer`
- [#421] Added `ImportNamespacedNameFixer`
- [#427] Added `RemoveUselessDocBlockFixer`

#### PackageBuilder

- [#442] Added `AutoloadFinder` to find nearest `/vendor/autoload.php`
- [#442] Added `provideParameter()` and `changeParameter()` methods to `ParameterProvider`
- [#431] Added `--level` shortcut helper builder

### Changed

- [#473] **CodingStandard** use [ReflectionDocBlock](https://github.com/phpDocumentor/ReflectionDocBlock) for docblock analysis and modification

#### Statie

- [#484] Add *dry-run* option to `StatieApplication` and `BeforeRenderEvent` to improve extendability, closes [#483]
- [9a9c0e] Use `statie.yml` config based on Symfony DI over "fake" `statie.neon` to prevent confusion, closes [#487]

    ```diff
    -includes:
    +imports:
    -     - source/data/config.neon
    +     - { resource: 'source/data/config.yml' }
    ```

    And simple services:

    ```diff
     services:
    -    -
    -        class: App\TranslationProvider
    +    App\TranslationProvider: ~
    ```
- [#475] Renamed `relatedPosts` filter to `relatedItems` with general usage (not only posts, but any other own generator element)
    ```diff
    -{var $relatedPosts = ($post|relatedPosts)}
    +{var $relatedPosts = ($post|relatedItems)}
    ```
- [#399] Filter `similarPosts` renamed to `relatedPosts`, closes [#386]

#### EasyCodingStandard

- [#474] Prefer diff report for changes over table report
- [#472] Improve `FileProcessorInterface`, improve performance via `CachedFileLoader`
- [881577] Removed `-checkers` suffix to make file naming consistent

### Removed

- [#475] **Statie** removed `postRoute`, only `prefix` is now available per item in generator
- [#412] **PackageBuilder** Removed Nette related-features, make package mostly internall for Symplify
- [#404] **SymbioticController** package deprecated, closes [#402]

#### CodingStandard

- [#488] Dropped `PropertyAndConstantSeparationFixer`, use `PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer` instead
- [#476] Dropped `NoInterfaceOnAbstractClassFixer`, not useful in practise
- [#443] Dropped `FinalTestCase`, use `SlamCsFixer\FinalInternalClassFixer` instead
- [#417] Dropped `InjectToConstructorInjectionFixer`, use [@RectorPHP] instead
- [#419] Dropped `ControllerRenderMethodLimitSniff` and `InvokableControllerSniff`, as related to SymbioticController
- [#432] Dropped `NewClassSniff`, use `NewWithBracesFixer` instead

#### EasyCodingStandard

- [bc0cb0] `php54.neon` set removed
- [#430] Dropped `--fixer-set` and `--checker-set` options for `show` command, proven as not useful

[comment]: # (links to issues, PRs and release diffs)

[#577]: https://github.com/Symplify/Symplify/pull/577
[#576]: https://github.com/Symplify/Symplify/pull/576
[#574]: https://github.com/Symplify/Symplify/pull/574
[#570]: https://github.com/Symplify/Symplify/pull/570
[#562]: https://github.com/Symplify/Symplify/pull/562
[#560]: https://github.com/Symplify/Symplify/pull/560
[#559]: https://github.com/Symplify/Symplify/pull/559
[#558]: https://github.com/Symplify/Symplify/pull/558
[#508]: https://github.com/Symplify/Symplify/pull/508
[#505]: https://github.com/Symplify/Symplify/pull/505
[#488]: https://github.com/Symplify/Symplify/pull/488
[#487]: https://github.com/Symplify/Symplify/pull/487
[#484]: https://github.com/Symplify/Symplify/pull/484
[#483]: https://github.com/Symplify/Symplify/pull/483
[#481]: https://github.com/Symplify/Symplify/pull/481
[#480]: https://github.com/Symplify/Symplify/pull/480
[#477]: https://github.com/Symplify/Symplify/pull/477
[#476]: https://github.com/Symplify/Symplify/pull/476
[#475]: https://github.com/Symplify/Symplify/pull/475
[#474]: https://github.com/Symplify/Symplify/pull/474
[#473]: https://github.com/Symplify/Symplify/pull/473
[#472]: https://github.com/Symplify/Symplify/pull/472
[#466]: https://github.com/Symplify/Symplify/pull/466
[#452]: https://github.com/Symplify/Symplify/pull/452
[#447]: https://github.com/Symplify/Symplify/pull/447
[#443]: https://github.com/Symplify/Symplify/pull/443
[#442]: https://github.com/Symplify/Symplify/pull/442
[#437]: https://github.com/Symplify/Symplify/pull/437
[#432]: https://github.com/Symplify/Symplify/pull/432
[#431]: https://github.com/Symplify/Symplify/pull/431
[#430]: https://github.com/Symplify/Symplify/pull/430
[#427]: https://github.com/Symplify/Symplify/pull/427
[#421]: https://github.com/Symplify/Symplify/pull/421
[#419]: https://github.com/Symplify/Symplify/pull/419
[#417]: https://github.com/Symplify/Symplify/pull/417
[#412]: https://github.com/Symplify/Symplify/pull/412
[#406]: https://github.com/Symplify/Symplify/pull/406
[#404]: https://github.com/Symplify/Symplify/pull/404
[#402]: https://github.com/Symplify/Symplify/pull/402
[#399]: https://github.com/Symplify/Symplify/pull/399
[#397]: https://github.com/Symplify/Symplify/pull/397
[#388]: https://github.com/Symplify/Symplify/pull/388
[#386]: https://github.com/Symplify/Symplify/pull/386
[#385]: https://github.com/Symplify/Symplify/pull/385
[v3.2.0]: https://github.com/Symplify/Symplify/compare/v3.1.0...v3.2.0
[@tomasfejfar]: https://github.com/tomasfejfar
[@ostrolucky]: https://github.com/ostrolucky
[@mzstic]: https://github.com/mzstic
[@carusogabriel]: https://github.com/carusogabriel
[@RectorPHP]: https://github.com/RectorPHP