# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

PRs and issues are linked, so you can find more about it. Thanks to [ChangelogLinker](https://github.com/Symplify/ChangelogLinker).

<!-- changelog-linker -->

## Unreleased

### Added

#### CodingStandard

- [#1305] Add `CatchExceptionNameMatchingTypeFixer`
- [#1306] Add `PregDelimiterFixer`

### Fixed

#### FlexLoader

- [#1310] Fix missing dependency `Nette\Utils`, Thanks to [@tavy315]

#### Statie

- [f17caf](https://github.com/Symplify/Symplify/commit/f17caf2a391e377f4e3b2276057dff1b3f65d76e) Fix missing dot file in `init`

## [v5.3.1] - 2019-01-05

- [#1303] Standardize config location to root `/config`

### Fixed

#### Statie

- [89eab9](https://github.com/Symplify/Symplify/commit/89eab9337d720e2f399e7e1b4ba56b187d881a84) Fix `*.twig` parsing by Latte

## [v5.3.0] - 2019-01-05

### Added

#### Statie

- [#1300] Add .travis.yml to InitCommand
- [#1288] Add `create-post` command
- [#1285] Add `init` command

### Changed

#### EasyCodingStandard

- [#1301] allow various format to skip
- [#1299] PHP CS Fixer finally on PHP 7.3
- [#1298] Move `exclude_checkers` to `skip` parameter

### Fixed

#### Autodiscovery

- [#1297] autodiscovery should be autoconfigure, Thanks to [@bendavies]

#### CodingStandard

- [#1302] Fix `PropertyNameMatchingTypeFixer` for double types

#### EasyCodingStandard

- [#1287] Fix color support with xdebug, Thanks to [@ostrolucky]

#### Statie

- [#1290] Configure templating for user

## [v5.2.20] - 2018-12-28

### Added

#### Autodiscovery

- [#1281] Add exclude support

#### PackageBuilder

- [#1279] Add `HelpfulApplicationTrait`

### Changed

#### Autodiscovery

- [#1280] Skip classes in vendor

## [v5.2.19] - 2018-12-27

### Added

#### Autodiscovery

- [#1276] Use single service approach
- [#1277] Add xml Doctrine mapping discovery
- [#1271] Add Translation path autodiscovery
- [#1263] Add Yaml explicit to autodiscovery converter
- [#1278] Run `convert-yaml` over directory, not just single file
- [#1268] Add posibility to setup config extensions to `FlexLoader`, Thanks to [@tavy315]

### Fixed

#### CodingStandard

- [#1262] fix `FinalInterfaceFixer `false positive for anonymous class, Thanks to [@suin]

## [v5.2.18] - 2018-12-18

### Added

#### Autodiscovery

- [#1257] Add new package

#### FlexLoader

- [#1256] Add new package

### Changed

#### PackageBuilder

- [#1260] Improve `LevelFileFinder` exception reporting

## [v5.2.16] - 2018-12-12

### Added

#### MonorepoBuilder

- [#1246] Add `PropagateCommand`

### Changed

- [#1248] Allow first tag for release command + throw exception on missing .git

### Fixed

- [#1233] Fix tests for MacOS, Thanks to [@azdanov]

## [v5.2.15] - 2018-12-07

### Added

#### EasyCodingStandard

- [#1243] Add xdebug-handler to improve performance while xdebug is on

### Fixed

#### Statie

- [#1242] Fix bug for multiple generator elements

## [v5.2.14] - 2018-12-03

### Added

#### CodingStandard

- [#1237] Add `BoolPropertyDefaultValueFixer`
- [#1236] Add duplicated array type remover

#### MonorepoBuilder

- [#1240] Add `stages_to_allow_existing_tag` to "release" command

#### PHPStanExtensions

- [#1239] Add `MatchingTypeConstantRule`

### Fixed

- [#1231] fix Symfony 4.2 compat

#### PackageBuiler

- [#1232] Fix `AutowireSinglyImplementedCompilerPass` for invalid Symfony definitions

### Added

#### EasyCodingStandard

- [#1234] Add JSON Formatter, thanks to [@azdanov]

## [v5.2.13] - 2018-11-28

### Added

- [#1227] travis: add PHP 7.3 build

### Changed

#### ChanelogLinker

- [#1224] Fixing some small typos and adding description to link command, Thanks to [@jawira]

#### EasyCodingStandard

- [#1226] Decouple reporting from `CheckCommand` to `CheckCommandReporter`, Thanks to [@azdanov]

#### TokenRunner

- [#1228] Improve `DescriptionAnalyzer`

## [v5.2.11] - 2018-11-26

### Added

#### CodingStandard

- [#1221] Add `RemoveEndOfFunctionCommentFixer`, improve `@var` malform fixers
- [#1220] Add support for return multi vars in `ParamReturnAndVarTagMalformsFixer`

## [v5.2.9] - 2018-11-22

### Added

#### MonorepoBuilder

- [#1219] Integrate feedback - show priority, add version to description, add `Confirmable`, add `--stage`
- [#1217] Add `inline_sections` to dump `*.json` content into single line

### Changed

#### CodingStandard

- [#1213] Improve `ParamReturnAndVarTagMalformsFixer` for this and missing dollar signs

## [v5.2.8] - 2018-11-22

### Fixed

#### EasyCodingStandard

- [#1216] Fix dual run
- [#1215] Fix --no-progress-bar conflict with uninitialized advance

## [v5.2.7] - 2018-11-21

### Added

#### MonorepoBuilder

- [#1212] Add interdependency update only for existing local packages

## [v5.2.4] - 2018-11-20

### Added

#### BetterPhpDocParser

- [#1196] Add support for `GenericTypeNode`, Thanks to [@enumag]

#### CodingStandard

- [#1206] Add `AnnotationTypeExistsSniff` and `ParamReturnAndVarTagMalformsFixer`

#### EasyCodingStandard

- [#1210] Add support for 2 spaces indent

### Fixed

#### MonorepoBuilder

- [#1209] Fix merging of scalar values in `composer.json`
- [#1207] Fix merging of duplicated items

## [v5.2.2] - 2018-11-15

### Added

#### MonoporeBuilder

- [#1202] Add way to disable default workers
- [#1200] Open for extension by `ReleaseWorkers`

## [v5.2.1] - 2018-11-12

### Added

#### CodingStandard

- [#1189] Add `AbstractSymplifyFixer`

#### PackageBuilder

- [#1185] Add `AutoReturnFactoryCompilerPass`

### Fixed

#### EasyCodingStandard

- [#1192] fix [#1191] add `Fixer::rollbackChangeset`, Thanks to [@ostrolucky]

### Changed

- [#1188] Use `FileInfo` instead of real/absolute paths

### Removed

#### EasyCodingStandard

- [#1187] Drop buggy unused `skip` reporting

## [v5.2.0] - 2018-11-04

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

- [#1179] Fix dump-merges for empty PR stack [closes [#1176]]

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
[#1177]: https://github.com/Symplify/Symplify/pull/1177
[#1175]: https://github.com/Symplify/Symplify/pull/1175
[#1174]: https://github.com/Symplify/Symplify/pull/1174
[#1173]: https://github.com/Symplify/Symplify/pull/1173
[#1171]: https://github.com/Symplify/Symplify/pull/1171
[#1170]: https://github.com/Symplify/Symplify/pull/1170
[@mantiz]: https://github.com/mantiz
[v5.1.4]: https://github.com/Symplify/Symplify/compare/v5.1.3...v5.1.4
[#1192]: https://github.com/Symplify/Symplify/pull/1192
[#1191]: https://github.com/Symplify/Symplify/pull/1191
[#1189]: https://github.com/Symplify/Symplify/pull/1189
[#1188]: https://github.com/Symplify/Symplify/pull/1188
[#1187]: https://github.com/Symplify/Symplify/pull/1187
[#1185]: https://github.com/Symplify/Symplify/pull/1185
[#1179]: https://github.com/Symplify/Symplify/pull/1179
[#1176]: https://github.com/Symplify/Symplify/pull/1176
[v5.2.1]: https://github.com/Symplify/Symplify/compare/v5.2.0...v5.2.1
[v5.2.0]: https://github.com/Symplify/Symplify/compare/v5.1.4...v5.2.0
[#1210]: https://github.com/Symplify/Symplify/pull/1210
[#1209]: https://github.com/Symplify/Symplify/pull/1209
[#1207]: https://github.com/Symplify/Symplify/pull/1207
[#1206]: https://github.com/Symplify/Symplify/pull/1206
[#1202]: https://github.com/Symplify/Symplify/pull/1202
[#1200]: https://github.com/Symplify/Symplify/pull/1200
[#1196]: https://github.com/Symplify/Symplify/pull/1196
[#1219]: https://github.com/Symplify/Symplify/pull/1219
[#1217]: https://github.com/Symplify/Symplify/pull/1217
[#1216]: https://github.com/Symplify/Symplify/pull/1216
[#1215]: https://github.com/Symplify/Symplify/pull/1215
[#1213]: https://github.com/Symplify/Symplify/pull/1213
[#1212]: https://github.com/Symplify/Symplify/pull/1212
[v5.2.8]: https://github.com/Symplify/Symplify/compare/v5.2.7...v5.2.8
[v5.2.7]: https://github.com/Symplify/Symplify/compare/v5.2.4...v5.2.7
[v5.2.4]: https://github.com/Symplify/Symplify/compare/v5.2.2...v5.2.4
[v5.2.2]: https://github.com/Symplify/Symplify/compare/v5.2.1...v5.2.2
[#1221]: https://github.com/Symplify/Symplify/pull/1221
[#1220]: https://github.com/Symplify/Symplify/pull/1220
[v5.2.9]: https://github.com/Symplify/Symplify/compare/v5.2.8...v5.2.9
[#1228]: https://github.com/Symplify/Symplify/pull/1228
[#1227]: https://github.com/Symplify/Symplify/pull/1227
[#1226]: https://github.com/Symplify/Symplify/pull/1226
[#1224]: https://github.com/Symplify/Symplify/pull/1224
[@jawira]: https://github.com/jawira
[v5.2.11]: https://github.com/Symplify/Symplify/compare/v5.2.9...v5.2.11
[#1243]: https://github.com/Symplify/Symplify/pull/1243
[#1242]: https://github.com/Symplify/Symplify/pull/1242
[#1240]: https://github.com/Symplify/Symplify/pull/1240
[#1239]: https://github.com/Symplify/Symplify/pull/1239
[#1237]: https://github.com/Symplify/Symplify/pull/1237
[#1236]: https://github.com/Symplify/Symplify/pull/1236
[#1234]: https://github.com/Symplify/Symplify/pull/1234
[#1232]: https://github.com/Symplify/Symplify/pull/1232
[#1231]: https://github.com/Symplify/Symplify/pull/1231
[v5.2.14]: https://github.com/Symplify/Symplify/compare/v5.2.13...v5.2.14
[v5.2.13]: https://github.com/Symplify/Symplify/compare/v5.2.11...v5.2.13
[#1260]: https://github.com/Symplify/Symplify/pull/1260
[#1257]: https://github.com/Symplify/Symplify/pull/1257
[#1256]: https://github.com/Symplify/Symplify/pull/1256
[#1248]: https://github.com/Symplify/Symplify/pull/1248
[#1246]: https://github.com/Symplify/Symplify/pull/1246
[#1233]: https://github.com/Symplify/Symplify/pull/1233
[v5.2.16]: https://github.com/Symplify/Symplify/compare/v5.2.15...v5.2.16
[v5.2.15]: https://github.com/Symplify/Symplify/compare/v5.2.14...v5.2.15
[#1278]: https://github.com/Symplify/Symplify/pull/1278
[#1277]: https://github.com/Symplify/Symplify/pull/1277
[#1276]: https://github.com/Symplify/Symplify/pull/1276
[#1271]: https://github.com/Symplify/Symplify/pull/1271
[#1268]: https://github.com/Symplify/Symplify/pull/1268
[#1263]: https://github.com/Symplify/Symplify/pull/1263
[#1262]: https://github.com/Symplify/Symplify/pull/1262
[@tavy315]: https://github.com/tavy315
[@suin]: https://github.com/suin
[v5.2.18]: https://github.com/Symplify/Symplify/compare/v5.2.16...v5.2.18
[#1302]: https://github.com/Symplify/Symplify/pull/1302
[#1301]: https://github.com/Symplify/Symplify/pull/1301
[#1300]: https://github.com/Symplify/Symplify/pull/1300
[#1299]: https://github.com/Symplify/Symplify/pull/1299
[#1298]: https://github.com/Symplify/Symplify/pull/1298
[#1297]: https://github.com/Symplify/Symplify/pull/1297
[#1290]: https://github.com/Symplify/Symplify/pull/1290
[#1288]: https://github.com/Symplify/Symplify/pull/1288
[#1287]: https://github.com/Symplify/Symplify/pull/1287
[#1285]: https://github.com/Symplify/Symplify/pull/1285
[#1281]: https://github.com/Symplify/Symplify/pull/1281
[#1280]: https://github.com/Symplify/Symplify/pull/1280
[#1279]: https://github.com/Symplify/Symplify/pull/1279
[@bendavies]: https://github.com/bendavies
[v5.2.20]: https://github.com/Symplify/Symplify/compare/v5.2.19...v5.2.20
[v5.2.19]: https://github.com/Symplify/Symplify/compare/v5.2.18...v5.2.19
[#1303]: https://github.com/Symplify/Symplify/pull/1303
[v5.3.0]: https://github.com/Symplify/Symplify/compare/v5.2.20...v5.3.0
[v5.3.1]: https://github.com/Symplify/Symplify/compare/v5.3.0...v5.3.1
[#1310]: https://github.com/Symplify/Symplify/pull/1310
[#1306]: https://github.com/Symplify/Symplify/pull/1306
[#1305]: https://github.com/Symplify/Symplify/pull/1305