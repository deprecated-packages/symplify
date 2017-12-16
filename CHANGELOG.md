# Changelog

(@todo tool to complete all the links?)


## [Unreleased]

### Added

- [#505] Added `CHANGELOG.md` 


## [3.0.1] - 2017-12-10

### Added

### Removed

## [3.0]



## [v3.0.0-RC5]

### Added 

- [#480] **[CodingStandard]** add `RemoveSuperfluousDocBlockWhitespaceFixer`, which removes 2 spaces in a row in doc blocks
- [#481] **[EasyCodingStandard]** add warning as error support, to make useful already existing Sniffs, closes [#477]

### Changed

- [#484] **[Statie]** add *dry-run* optiont to `StatieApplication` and `BeforeRenderEvent` to improve extendability, closes [#483] 
- https://github.com/Symplify/Symplify/commit/9a9c0e61d0b7af073d3819e4c4798a251eca1f14 **[Statie]** use `statie.yml` config based on Symfony DI over "fake" `statie.neon` to prevent confusion, closes [#487] 

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


## [v3.0.0-RC4]

### Added

- #475 **[Statie]** added support for generators

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


- https://github.com/Symplify/Symplify/commit/9b154d9b6e88075e14b6812613bce7c1a2a79daa **[Statie]** added `-vvv` CLI option for debug output

- #473 bump to Symfony 4

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



**Full diff:** https://github.com/Symplify/Symplify/compare/v3.0.0-RC3...v3.0.0-RC4



[v3.0.0-RC5]: https://github.com/Symplify/Symplify/compare/v3.0.0-RC4...v3.0.0-RC5


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

[3.0]: https://github.com/Symplify/Symplify/compare/v3.0.0-RC5...3.0
[3.0.1]: https://github.com/Symplify/Symplify/compare/3.0...3.0.1
