# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

PRs and issues are linked, so you can find more about it. Thanks to [ChangelogLinker](https://github.com/Symplify/ChangelogLinker).

<!-- changelog-linker -->

## v5.1.2 - 2018-10-11

### Added

#### PackageBuilder

- [#1145] Add `AutowireArrayParameterCompilerPass`
- [#1144] Add `AutowireInterfacesCompilerPass`

### Fixed

#### CodingStandard

- [#1139] Allow `NoExtraBlankLinesFixer` in PSR-12, Thanks to [@mynameisbogdan]
- [#1140] Fix missing forbidden partial namespace use Symplify set

#### Statie

- [#1152] Fix relative path to generated file

## [v5.1.1] - 2018-10-01

### Added

#### PackageBuilder

- [#1133] Add `ConfigurableCollectorCompilerPass`
- [#1131] Add `ParameterTypoProofreader` [closes [#1128]]

### Fixed

#### ChanglogLinker

- [#1129] Fix version linking - order + matching new versions

#### MonorepoBuilder

- [#1135] Fix duplicated `require` and `require-dev` items

#### PackageBuilder

- [#1137] Fix `SmartFileInfo` invalid dir argument

## [v5.1.0] - 2018-09-28

### Added

#### PHPStanExtensions

- [#1123] Add `SplFileInfoTolerantDynamicMethodReturnTypeExtension`

#### PackageBuilder

- [#1126] Apply `SmartFileInfo`
- [#1125] Add `SmartFileInfo`

#### Statie

- [#1127] Add logo, Thanks to [@crazko]

## [v5.0.1] - 2018-09-19

### Added

#### TokenRunner

- [#1120] Add `getClassTypes()` to `ClassWrapper` + `getClassName()` now returns FQN

#### BettePhpDocParser

- [#1116] Add support for `IntersectionTypeNode`, Thanks to [@enumag]

### Changed

#### CodingStandard

- [#1119] Now skip Abstract classes for `MethodOrderByTypeFixer`
- [#1121] Make `MethodOrderByTypeFixer` use `getClassTypes()`

#### EasyCodingStandard

- [#1113] Add readme section for vscode integration, Thanks to [@azdanov]

## [v5.0.0] - 2018-09-15

Same as 4.8, just without BC layer. Thanks Symfony for inspiration!

<br>

**See [CHANGELOG-4.md](/CHANGELOG-4.md) for changes in Symplify 4.x.**

[comment]: # (links to issues, PRs and release diffs)

[@enumag]: https://github.com/enumag
[@azdanov]: https://github.com/azdanov
[#1121]: https://github.com/Symplify/Symplify/pull/1121
[#1120]: https://github.com/Symplify/Symplify/pull/1120
[#1119]: https://github.com/Symplify/Symplify/pull/1119
[#1116]: https://github.com/Symplify/Symplify/pull/1116
[#1113]: https://github.com/Symplify/Symplify/pull/1113
[v5.0.0]: https://github.com/Symplify/Symplify/compare/v4.8.0...v5.0.0
[#1127]: https://github.com/Symplify/Symplify/pull/1127
[#1126]: https://github.com/Symplify/Symplify/pull/1126
[#1125]: https://github.com/Symplify/Symplify/pull/1125
[#1123]: https://github.com/Symplify/Symplify/pull/1123
[@crazko]: https://github.com/crazko
[v5.1.0]: https://github.com/Symplify/Symplify/compare/v5.0.1...v5.1.0
[v5.0.1]: https://github.com/Symplify/Symplify/compare/v5.0.0...v5.0.1
[#1137]: https://github.com/Symplify/Symplify/pull/1137
[#1135]: https://github.com/Symplify/Symplify/pull/1135
[#1133]: https://github.com/Symplify/Symplify/pull/1133
[#1131]: https://github.com/Symplify/Symplify/pull/1131
[#1129]: https://github.com/Symplify/Symplify/pull/1129
[#1128]: https://github.com/Symplify/Symplify/pull/1128
[v5.1.1]: https://github.com/Symplify/Symplify/compare/v5.1.0...v5.1.1
[#1145]: https://github.com/Symplify/Symplify/pull/1145
[#1144]: https://github.com/Symplify/Symplify/pull/1144
[#1140]: https://github.com/Symplify/Symplify/pull/1140
[#1139]: https://github.com/Symplify/Symplify/pull/1139
[@mynameisbogdan]: https://github.com/mynameisbogdan
[#1152]: https://github.com/Symplify/Symplify/pull/1152
[#1147]: https://github.com/Symplify/Symplify/pull/1147
[@veewee]: https://github.com/veewee