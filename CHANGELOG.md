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






[v3.0.0-RC5]: https://github.com/Symplify/Symplify/compare/v3.0.0-RC4...v3.0.0-RC5

