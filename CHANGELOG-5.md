# Changelog for Symplify 5.x

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

PRs and issues are linked, so you can find more about it. Thanks to [ChangelogLinker](https://github.com/Symplify/ChangelogLinker).

## [v5.4.15] - 2019-03-05

**This is the last release in 5.x row. The next will be Symplify 6.0 without BC layer.**

### Removed

- [#1483] Remove token-runner dependency from coding-standard, Thanks to [@jeroennoten]

## [v5.4.14] - 2019-03-04

### Added

#### CodingStandard

- [#1477] Add DuplicatedClassShortNameSniff

### Changed

- [#1481] Bump to slevomat coding-standard 5.0

### EasyCodingStandard

- [#1480] Make dual run clear cache by default
- [#1482] Use `.yaml` over `.yml`

### Fixed

- [#1469] Fix `UnusedPublicMethodSniff` offset

#### MonorepoBuilder

- [#1476] Fix appended extra repository duplicate

### Deprecated

#### CodingStandard

- [#1473] Deprecate `RemoveUselessDocBlockFixer`

#### BetterPhpDocParser

- [#1474] Deprecate package, used only in `RemoveUselessDocBlockFixer`

#### EasyCodingStandard

- [#1482] Deprecated .yml sets

#### TokenRunner

- [#1479] Deprecate and move under `CodingStandard` package

## [v5.4.13] - 2019-02-28

### Added

#### BetterPhpDocParser

- [#1468] add `createFromNode()` method

### Fixed

#### EasyCodingStandard

- [#1467] Report sniff just once

## [v5.4.11] - 2019-02-25

### Fixed

#### EasyCodingStandard

- [#1460] Fix PHP_CodeSniffer Loop inconsistency

## [v5.4.10] - 2019-02-25

### Added

#### LatteToTwigConverter

- [#1463] Add `date` and `sprintf` support

### Changed

#### Autodiscovery

- [#1465] Allow file as convert-yaml argument

#### EasyCodingStandard

- [#1462] Make PSR-2 sub-configs names unique to prevent duplications [BC break]

### Fixed

#### BetterPhpDocParser

- [#1464] Fix incorrect intersection to union retype

#### Statie

- [#1456] Fix missing `.md` file generation
- [#1454] Fix in-title url for similiar Tweet text

### Deprecated

#### PackageBuilder

- [#1457] Deprecate `ConfigurableCollectorCompilerPass`

## [v5.4.9] - 2019-02-20

### Removed

#### PHPStanExtension

- [#1452] remove cached run, not ready yet; also drop `StatsErrorFormatter`, not useful

## [v5.4.7] - 2019-02-20

### Added

- [#1447] Add Illuminate helpers, improve PHPStan cache and configs

### Fixed

#### CodingStandard

- [#1451] Add double quote and dot slash support to `RequireFollowedByAbsolutePathFixer`

## [v5.4.6] - 2019-02-16

### Changed

#### BetterPhpDocParser

- [#1442] Add `PhpDocNodeDecoratorInterface` again, simplify `PhpDocInfo`
- [#1445] Various improvements, turn active record to value object + service

### Removed

#### TokenRunner

- [#1444] remove `TypeNodeToStringsConverter` from `DescriptionAnalyzer`

## [v5.4.5] - 2019-02-13

### Changed

#### BetterPhpDocParser

- [#1437] Allow node attributes

## [v5.4.3] - 2019-02-11

### Added

#### NeonToYamlConverter

- [#1421] Init new package
- [#1435] merge 'rename' commands to 'convert' commands + keep nice spaces

#### BetterPhpDocParser

- [#1430] Add `replaceTypeNode()` array support to `PhpDocModifier`

### Changed

- [#1426] Make Kernel more Symfony standard-like
- [#1416] Allow PHPUnit 8.0, Thanks to [@enumag]
- [#1423] move Kernel classes under HttpKernel, cleanup tests
- [#1422] make bin commands fail for error

#### MonorepoBuilder

- [#1432] Add `suggest` section to merge
- [#1431] Add `replace` section on merge

### Fixed

#### CodingStandard

- [#1413] Fix `LineLengthTransformer` for short lines

#### MonorepoBuilder

- [#1411] Fix typo in readme to use the right package name, Thanks to [@natepage]

#### NeonToYamlConvertor

- [#1433] fix `%env` conversion
- [#1434] Make parameter order independent, inline array params smart way

#### PackageBuilder

- [#1428] fix `SmartFileInfo` `getRelativePath` on Windows - use normalized path, Thanks to [@jDolba]

## [v5.4.2] - 2019-02-02

### Added

#### MonorepoBuilder

- [#1405] Add `suggest` to `section_order`, Thanks to [@natepage]

### Fixed

#### ChangelogLinker

- [#1403] Fix typo in README.md `mergers` -> `merges`, Thanks to [@natepage]

#### EasyCodingStandard

- [#1402] Detect EOL instead of using PHP_EOL in SniffRunner, Thanks to [@ikeblaster]

### Removed

- [#1409] Remove Safe package

## [v5.4.1] - 2019-01-31

### Fixed

#### FlexLoader

- [#1400] loading fails when extra path does not exists, Thanks to [@vrbata]
- [#1399] Load `parameters.*` as well, Thanks to [@vrbata]

## [v5.4.0] - 2019-01-27

### Added

#### EasyCodingStandard

- [#1357] Add links to files with lines in reported errors
- [#1397] Add typo-proof configuration of checkers

#### Statie

- [#1394] Add `|link` filter to link generator files
- [#1393] Add `-` to `_` param name converter to `migrate-jekyll`
- [#1375] Add `twitter_maximal_days_in_past` break with default value of 60 days
- [#1391] Add parameter name by file to `migrate-jekyll`
- [#1387] Add xdebug-handler to improve performance
- [#1383] Display process info on `-v`
- [#1352] Add "markdown", "reading_time", "sort_by_field", "date_to_xmlschema", "xml_escape" and "related_items" filters/functions
- [#1339] Add `migrate-jekyll` command
- [#1362] Add `migrate-sculpin` command
- [#1347] Add raw-content support for generated files
- [#1359] Add `dump-joind-in` command
- [#1358] Add redirect support directly to config
- [#1356] Add "group_by_field" filter
- [#1355] Add "diff_from_today_in_days" filter
- [#1366] Add api generator via `api_parameters` parameter

### Changed

#### PHPStanExtensions

- [#1377] Improve Symfony and clickable error formatter support

#### MonorepoBuilder

- [#1349] Extend dependency updater to allow skip callback
- [#1323] `release` command stage is taken into account in success message, Thanks to [@vitek-rostislav]

#### Statie

- [#1365] Improve gulpfile for Docker, Thanks to [@tomasfejfar]

### Fixed

#### BetterPhpDocParser

- [#1368] TYPO in name of class: `TypeNodeToStringsConvertor` instead `TypeNodeToStringsConverter`, Thanks to [@themark147]

#### ChangelogLinker

- [#1350] link only full matching names

#### CodingStandard

- [#1396] Fix `ForbiddenReferenceSniff` for extra space before &

#### Statie

- [#1380] Fix `markdown` filter extra spaces
- [#1389] Make Twig report syntax errors on generate
- [#1395] Configure headline links per generator element + enable by default
- [#1363] Listen on every address, Thanks to [@tomasfejfar]

#### MonorepoBuilder

- [#1386] Resolve complete paths for `exclude-from-classmap`

#### PackageBuilder

- [#1376] Skip non-scalar params on `AutoBindParametersCompilerPass`
- [#1392] Make `BetterGuzzleClient` pass on success code

### Removed

#### PHPStanExtensions

- [#1384] Remove old grouping from `SymplifyErrorFormatter`, show all errors

## [v5.3.12] - 2019-01-13

### Fixed

#### MonorepoBuilder

- [#1338] Fix package classmap merging

## [v5.3.10] - 2019-01-13

### Added

#### LatteToTwigConverter

- [#1336] Add n:ifset, n:inner-foreach, n:class
- [#1335] Add n:if and n:foreach macros
- [#1332] Cover more Latte cases

## [v5.3.9] - 2019-01-11

### Added

#### LatteToTwigConverter

- [#1330] Add `rename` command

### Fixed

### LatteToTwigConverter

- [#1329] Actually display `convert` command

## [v5.3.8] - 2019-01-11

### Fixed

#### Statie

- [#1325] Add `LayoutsAndSnippetsLoader` to fix missing layouts in tweet post command

## [v5.3.6] - 2019-01-10

### Added

#### Statie

- [#1321] Add post headline linker
- [#1320] Add TemplatingDetector to resolve templating detection
- [#1317] Add Github Contributors Thanker
- [#1316] Add Tweeter for post tweeting

## [v5.3.5] - 2019-01-08

### Added

#### CodingStandard

- [#1315] Add flags support to `PregDelimiterFixer` + cover quotes + make configurable

## [v5.3.2] - 2019-01-08

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
[#1315]: https://github.com/Symplify/Symplify/pull/1315
[v5.3.2]: https://github.com/Symplify/Symplify/compare/v5.3.1...v5.3.2
[#1321]: https://github.com/Symplify/Symplify/pull/1321
[#1320]: https://github.com/Symplify/Symplify/pull/1320
[#1317]: https://github.com/Symplify/Symplify/pull/1317
[#1316]: https://github.com/Symplify/Symplify/pull/1316
[v5.3.5]: https://github.com/Symplify/Symplify/compare/v5.3.2...v5.3.5
[#1325]: https://github.com/Symplify/Symplify/pull/1325
[v5.3.8]: https://github.com/Symplify/Symplify/compare/v5.3.6...v5.3.8
[v5.3.6]: https://github.com/Symplify/Symplify/compare/v5.3.5...v5.3.6
[#1330]: https://github.com/Symplify/Symplify/pull/1330
[#1329]: https://github.com/Symplify/Symplify/pull/1329
[v5.3.9]: https://github.com/Symplify/Symplify/compare/v5.3.8...v5.3.9
[#1336]: https://github.com/Symplify/Symplify/pull/1336
[#1335]: https://github.com/Symplify/Symplify/pull/1335
[#1332]: https://github.com/Symplify/Symplify/pull/1332
[#1368]: https://github.com/Symplify/Symplify/pull/1368
[#1366]: https://github.com/Symplify/Symplify/pull/1366
[#1363]: https://github.com/Symplify/Symplify/pull/1363
[#1362]: https://github.com/Symplify/Symplify/pull/1362
[#1359]: https://github.com/Symplify/Symplify/pull/1359
[#1358]: https://github.com/Symplify/Symplify/pull/1358
[#1356]: https://github.com/Symplify/Symplify/pull/1356
[#1355]: https://github.com/Symplify/Symplify/pull/1355
[#1352]: https://github.com/Symplify/Symplify/pull/1352
[#1350]: https://github.com/Symplify/Symplify/pull/1350
[#1349]: https://github.com/Symplify/Symplify/pull/1349
[#1347]: https://github.com/Symplify/Symplify/pull/1347
[#1339]: https://github.com/Symplify/Symplify/pull/1339
[#1338]: https://github.com/Symplify/Symplify/pull/1338
[#1323]: https://github.com/Symplify/Symplify/pull/1323
[@vitek-rostislav]: https://github.com/vitek-rostislav
[@tomasfejfar]: https://github.com/tomasfejfar
[@themark147]: https://github.com/themark147
[v5.3.12]: https://github.com/Symplify/Symplify/compare/v5.3.10...v5.3.12
[v5.3.10]: https://github.com/Symplify/Symplify/compare/v5.3.9...v5.3.10
[#1397]: https://github.com/Symplify/Symplify/pull/1397
[#1396]: https://github.com/Symplify/Symplify/pull/1396
[#1395]: https://github.com/Symplify/Symplify/pull/1395
[#1394]: https://github.com/Symplify/Symplify/pull/1394
[#1393]: https://github.com/Symplify/Symplify/pull/1393
[#1392]: https://github.com/Symplify/Symplify/pull/1392
[#1391]: https://github.com/Symplify/Symplify/pull/1391
[#1389]: https://github.com/Symplify/Symplify/pull/1389
[#1387]: https://github.com/Symplify/Symplify/pull/1387
[#1386]: https://github.com/Symplify/Symplify/pull/1386
[#1384]: https://github.com/Symplify/Symplify/pull/1384
[#1383]: https://github.com/Symplify/Symplify/pull/1383
[#1380]: https://github.com/Symplify/Symplify/pull/1380
[#1377]: https://github.com/Symplify/Symplify/pull/1377
[#1376]: https://github.com/Symplify/Symplify/pull/1376
[#1375]: https://github.com/Symplify/Symplify/pull/1375
[#1365]: https://github.com/Symplify/Symplify/pull/1365
[#1357]: https://github.com/Symplify/Symplify/pull/1357
[#1430]: https://github.com/Symplify/Symplify/pull/1430
[#1428]: https://github.com/Symplify/Symplify/pull/1428
[#1423]: https://github.com/Symplify/Symplify/pull/1423
[#1422]: https://github.com/Symplify/Symplify/pull/1422
[#1421]: https://github.com/Symplify/Symplify/pull/1421
[#1416]: https://github.com/Symplify/Symplify/pull/1416
[#1413]: https://github.com/Symplify/Symplify/pull/1413
[#1411]: https://github.com/Symplify/Symplify/pull/1411
[#1409]: https://github.com/Symplify/Symplify/pull/1409
[#1405]: https://github.com/Symplify/Symplify/pull/1405
[#1403]: https://github.com/Symplify/Symplify/pull/1403
[#1402]: https://github.com/Symplify/Symplify/pull/1402
[#1400]: https://github.com/Symplify/Symplify/pull/1400
[#1399]: https://github.com/Symplify/Symplify/pull/1399
[v5.4.2]: https://github.com/Symplify/Symplify/compare/v5.4.1...v5.4.2
[v5.4.1]: https://github.com/Symplify/Symplify/compare/v5.4.0...v5.4.1
[@vrbata]: https://github.com/vrbata
[@natepage]: https://github.com/natepage
[@jDolba]: https://github.com/jDolba
[@ikeblaster]: https://github.com/ikeblaster
[v5.4.0]: https://github.com/Symplify/Symplify/compare/v5.3.12...v5.4.0
[#1435]: https://github.com/Symplify/Symplify/pull/1435
[#1434]: https://github.com/Symplify/Symplify/pull/1434
[#1433]: https://github.com/Symplify/Symplify/pull/1433
[#1432]: https://github.com/Symplify/Symplify/pull/1432
[#1431]: https://github.com/Symplify/Symplify/pull/1431
[#1426]: https://github.com/Symplify/Symplify/pull/1426
[#1451]: https://github.com/Symplify/Symplify/pull/1451
[#1447]: https://github.com/Symplify/Symplify/pull/1447
[#1445]: https://github.com/Symplify/Symplify/pull/1445
[#1444]: https://github.com/Symplify/Symplify/pull/1444
[#1442]: https://github.com/Symplify/Symplify/pull/1442
[#1437]: https://github.com/Symplify/Symplify/pull/1437
[v5.4.7]: https://github.com/Symplify/Symplify/compare/v5.4.6...v5.4.7
[v5.4.6]: https://github.com/Symplify/Symplify/compare/v5.4.5...v5.4.6
[v5.4.5]: https://github.com/Symplify/Symplify/compare/v5.4.3...v5.4.5
[v5.4.3]: https://github.com/Symplify/Symplify/compare/v5.4.2...v5.4.3
[#1465]: https://github.com/Symplify/Symplify/pull/1465
[#1464]: https://github.com/Symplify/Symplify/pull/1464
[#1463]: https://github.com/Symplify/Symplify/pull/1463
[#1462]: https://github.com/Symplify/Symplify/pull/1462
[#1457]: https://github.com/Symplify/Symplify/pull/1457
[#1456]: https://github.com/Symplify/Symplify/pull/1456
[#1454]: https://github.com/Symplify/Symplify/pull/1454
[#1452]: https://github.com/Symplify/Symplify/pull/1452
[v5.4.9]: https://github.com/Symplify/Symplify/compare/v5.4.7...v5.4.9
[#1460]: https://github.com/Symplify/Symplify/pull/1460
[v5.4.10]: https://github.com/Symplify/Symplify/compare/v5.4.9...v5.4.10
[#1482]: https://github.com/Symplify/Symplify/pull/1482
[#1481]: https://github.com/Symplify/Symplify/pull/1481
[#1480]: https://github.com/Symplify/Symplify/pull/1480
[#1479]: https://github.com/Symplify/Symplify/pull/1479
[#1477]: https://github.com/Symplify/Symplify/pull/1477
[#1476]: https://github.com/Symplify/Symplify/pull/1476
[#1474]: https://github.com/Symplify/Symplify/pull/1474
[#1473]: https://github.com/Symplify/Symplify/pull/1473
[#1469]: https://github.com/Symplify/Symplify/pull/1469
[#1468]: https://github.com/Symplify/Symplify/pull/1468
[#1467]: https://github.com/Symplify/Symplify/pull/1467
[v5.4.14]: https://github.com/Symplify/Symplify/compare/v5.4.13...v5.4.14
[v5.4.13]: https://github.com/Symplify/Symplify/compare/v5.4.11...v5.4.13
[v5.4.11]: https://github.com/Symplify/Symplify/compare/v5.4.10...v5.4.11
[#1553]: https://github.com/Symplify/Symplify/pull/1553
[#1552]: https://github.com/Symplify/Symplify/pull/1552
[#1551]: https://github.com/Symplify/Symplify/pull/1551
[#1548]: https://github.com/Symplify/Symplify/pull/1548
[#1545]: https://github.com/Symplify/Symplify/pull/1545
[#1541]: https://github.com/Symplify/Symplify/pull/1541
[#1540]: https://github.com/Symplify/Symplify/pull/1540
[#1538]: https://github.com/Symplify/Symplify/pull/1538
[#1537]: https://github.com/Symplify/Symplify/pull/1537
[#1536]: https://github.com/Symplify/Symplify/pull/1536
[#1535]: https://github.com/Symplify/Symplify/pull/1535
[#1534]: https://github.com/Symplify/Symplify/pull/1534
[#1529]: https://github.com/Symplify/Symplify/pull/1529
[#1528]: https://github.com/Symplify/Symplify/pull/1528
[#1527]: https://github.com/Symplify/Symplify/pull/1527
[#1525]: https://github.com/Symplify/Symplify/pull/1525
[#1521]: https://github.com/Symplify/Symplify/pull/1521
[#1514]: https://github.com/Symplify/Symplify/pull/1514
[#1512]: https://github.com/Symplify/Symplify/pull/1512
[#1511]: https://github.com/Symplify/Symplify/pull/1511
[#1510]: https://github.com/Symplify/Symplify/pull/1510
[#1509]: https://github.com/Symplify/Symplify/pull/1509
[#1502]: https://github.com/Symplify/Symplify/pull/1502
[#1500]: https://github.com/Symplify/Symplify/pull/1500
[#1499]: https://github.com/Symplify/Symplify/pull/1499
[#1498]: https://github.com/Symplify/Symplify/pull/1498
[#1493]: https://github.com/Symplify/Symplify/pull/1493
[#1489]: https://github.com/Symplify/Symplify/pull/1489
[#1488]: https://github.com/Symplify/Symplify/pull/1488
[#1483]: https://github.com/Symplify/Symplify/pull/1483
[@wppd]: https://github.com/wppd
[@solcik]: https://github.com/solcik
[@shyim]: https://github.com/shyim
[@possi]: https://github.com/possi
[@nlubisch]: https://github.com/nlubisch
[@jeroennoten]: https://github.com/jeroennoten
[@ektarum]: https://github.com/ektarum
[@TomasLudvik]: https://github.com/TomasLudvik
[@PetrHeinz]: https://github.com/PetrHeinz
[@JanMikes]: https://github.com/JanMikes
[v5.4.15]: https://github.com/Symplify/Symplify/compare/v5.4.14...v5.4.15
