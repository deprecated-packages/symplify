# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

PRs and issues are linked, so you can find more about it. Thanks to [ChangelogLinker](https://github.com/Symplify/ChangelogLinker).

<!-- changelog-linker -->

## v5.2.0 - 2018-11-04

### Added

#### CodingStandard

- [#1170] Add `PreferredClassSniff`
- [#1171] Add `PropertyOrderByComplexityFixer`
- [#1173] Add `PrivateMethodOrderFixer`

#### MonorepoBuilder

- [#1175] Add option for maximum number `--max-processes` of parallel processes to split command, Thanks to [@mantiz]

### Changed

#### EasyCodingStandard

- [#1177] `-v` debug options now show files

#### CodingStandard

- [#1174] Simplify fixer configuration to make them readable

### Fixed

#### ChangelogLinker

- [#1179] Fix dump-merges for empty PR stack [closes #1176]

## [v5.1.4] - 2018-10-27

### Fixed

#### EasyCodingStandard

- [#1168] Add conflicting constants checkers [ref #1167]

#### PackageBuilder

- [#1166] Make `VendorDirProvider` work for global installs

## [v5.1.3] - 2018-10-19

### Added

#### EasyCodingStandard

- [#1148] Add way to suppress specific sniff message, Thanks to [@ostrolucky]

#### LatteToTwigConverter

- [#1163] Add quote in `<script>` support [closes [#1155]]

### Changed

#### BetterPhpDocParser

- [#1161] Decouple `PhpDocInfo` decorators

#### CodingStandard

- [#1154] Improve `ClassStringToClassConstantFixer` class type matching

### Fixed

#### PackageBuilder

- [#1160] Remove need of default `[]` for autowired array arguments

## [v5.1.2] - 2018-10-11

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
[#1163]: https://github.com/Symplify/Symplify/pull/1163
[#1161]: https://github.com/Symplify/Symplify/pull/1161
[#1160]: https://github.com/Symplify/Symplify/pull/1160
[#1155]: https://github.com/Symplify/Symplify/pull/1155
[#1154]: https://github.com/Symplify/Symplify/pull/1154
[#1148]: https://github.com/Symplify/Symplify/pull/1148
[@ostrolucky]: https://github.com/ostrolucky
[v5.1.2]: https://github.com/Symplify/Symplify/compare/v5.1.1...v5.1.2
[#1168]: https://github.com/Symplify/Symplify/pull/1168
[#1166]: https://github.com/Symplify/Symplify/pull/1166
[v5.1.3]: https://github.com/Symplify/Symplify/compare/v5.1.2...v5.1.3
[#1178]: https://github.com/Symplify/Symplify/pull/1178
[#1177]: https://github.com/Symplify/Symplify/pull/1177
[#1175]: https://github.com/Symplify/Symplify/pull/1175
[#1174]: https://github.com/Symplify/Symplify/pull/1174
[#1173]: https://github.com/Symplify/Symplify/pull/1173
[#1171]: https://github.com/Symplify/Symplify/pull/1171
[#1170]: https://github.com/Symplify/Symplify/pull/1170
[#1169]: https://github.com/Symplify/Symplify/pull/1169
[#1165]: https://github.com/Symplify/Symplify/pull/1165
[@mantiz]: https://github.com/mantiz
[v5.1.4]: https://github.com/Symplify/Symplify/compare/v5.1.3...v5.1.4