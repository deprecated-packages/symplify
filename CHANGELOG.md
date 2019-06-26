# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

PRs and issues are linked, so you can find more about it. Thanks to [ChangelogLinker](https://github.com/Symplify/ChangelogLinker).

<!-- changelog-linker -->

## [v6.0.4] - 2019-06-26

### Added

#### PackageBuilder

- [#1573] Make `AutoReturnFactoryCompilerPass` work with `@return` annotations as well

### Fixed

#### EasyCodingStandard

- [#1569] Fix `exclude_checkers` option typo

#### MonorepoBuilder

- [#1568] Fix extra keys in repositories merge

### Deprecated

#### PackageBuilder

- [#1567] Deprecate `AutowireSinglyImplementedCompilerPass`

## [v6.0.3] - 2019-06-11

### Fixed

#### Unknown Package

- [#1565] Fix path to set config in readme, Thanks to [@Big-Shark]
- [#1561] Fixed AutowireSinglyImplementedCompilerPass - alias referencing itself, Thanks to [@JanMikes]

### Unknown Category

- [#1563] AutowireSinglyImplementedCompilerPass - Skipping singly implemented service if alias for interface is already registered, Thanks to [@JanMikes]

## [v6.0.2] - 2019-06-04

### Removed

#### PHPStanExtensions

- [#1559] remove blocked custom param in SymplifyPHPStanExtension

## [v6.0.1] - 2019-05-30

### Added

#### Unknown Package

- [#1558] Add psr/simple-cache as a dependency, Thanks to [@jakzal]

## [v6.0.0] - 2019-05-28

### Added

- [#1510] Added gitattribute rules to all packages, Thanks to [@JanMikes]
- [#1509] Added docs and \*.md to export-gngore for .gitattribute, Thanks to [@JanMikes]
- [#1525] Add list of tool integration, Thanks to [@nlubisch]

#### ChangelogLinker

- [#1512] added support for resolving repository name from URL with user name included, Thanks to [@TomasLudvik]

#### CodingStandard

- [#1499] Make UnusedPublicMethodSniff skip tests calls, add to CI

#### EasyCodingStandard

- [#1537] Add `only` feature support, as oppose to `skip`

#### Statie

- [#1511] Add source argument to tweet-post command
- [#1540] Add ApiItemDecoratorInterface for REST api
- [#1538] Add custom `output_path` to generator elements

### Changed

- [#1535] Bump to PHP CS Fixer 2.15
- [#1541] use single bin file over multiple small files
- [#1493] nette v3 utils, neon, di support, Thanks to [@solcik]

#### EasyCodingStandard

- [#1502] raised error for PSR2 warning sniff, Thanks to [@ektarum]

#### MonorepoBuilder

- [#1488] Simplify Autoload merging, Thanks to [@possi]

#### PackageBuilder

- [#1552] Make `LevelFileFinder` configurable

### Fixed

#### CodingStandard

- [#1521] Fix multi-line @var/@param at unrelated code

#### PackageBuilder

- [#1498] Fix LevelFinder bundled in phar file, Thanks to [@shyim]
- [#1551] Fix autobind parameters for autoconfigured definitions + bump min to Symfony 4.2

### Removed

- [#1489] Remove deprecations to prepare for Symplify 6
- [#1536] remove deprecated yml configs, use yaml instead [BC break]
- [#1548] remove illuminate/support dependency, Thanks to [@wppd]

#### CodingStandard

- [#1534] Drop min item count on StandaloneLineInMultilineArray

#### PackageBuilder

- [#1527] Remove PublicForTestsCompilerPass

#### Statie

- [#1514] Make sort_by_field insensitive for better name compare
- [#1528] Headline linker, Thanks to [@crazko]
- [#1500] Take care of elements in anchor linker, Thanks to [@crazko]

[#1552]: https://github.com/Symplify/Symplify/pull/1552
[#1551]: https://github.com/Symplify/Symplify/pull/1551
[#1548]: https://github.com/Symplify/Symplify/pull/1548
[#1541]: https://github.com/Symplify/Symplify/pull/1541
[#1540]: https://github.com/Symplify/Symplify/pull/1540
[#1538]: https://github.com/Symplify/Symplify/pull/1538
[#1537]: https://github.com/Symplify/Symplify/pull/1537
[#1536]: https://github.com/Symplify/Symplify/pull/1536
[#1535]: https://github.com/Symplify/Symplify/pull/1535
[#1534]: https://github.com/Symplify/Symplify/pull/1534
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
[v6.0.0]: https://github.com/Symplify/Symplify/compare/v5.4.15...v6.0.0
[@wppd]: https://github.com/wppd
[@solcik]: https://github.com/solcik
[@shyim]: https://github.com/shyim
[@possi]: https://github.com/possi
[@nlubisch]: https://github.com/nlubisch
[@ektarum]: https://github.com/ektarum
[@crazko]: https://github.com/crazko
[@TomasLudvik]: https://github.com/TomasLudvik
[@JanMikes]: https://github.com/JanMikes

[#1573]: https://github.com/Symplify/Symplify/pull/1573
[#1569]: https://github.com/Symplify/Symplify/pull/1569
[#1568]: https://github.com/Symplify/Symplify/pull/1568
[#1567]: https://github.com/Symplify/Symplify/pull/1567
[#1565]: https://github.com/Symplify/Symplify/pull/1565
[#1563]: https://github.com/Symplify/Symplify/pull/1563
[#1561]: https://github.com/Symplify/Symplify/pull/1561
[#1559]: https://github.com/Symplify/Symplify/pull/1559
[#1558]: https://github.com/Symplify/Symplify/pull/1558
[#1553]: https://github.com/Symplify/Symplify/pull/1553
[#1530]: https://github.com/Symplify/Symplify/pull/1530
[v6.0.3]: https://github.com/Symplify/Symplify/compare/v6.0.2...v6.0.3
[v6.0.2]: https://github.com/Symplify/Symplify/compare/v6.0.1...v6.0.2
[v6.0.1]: https://github.com/Symplify/Symplify/compare/v6.0.0...v6.0.1
[@return]: https://github.com/return
[@jakzal]: https://github.com/jakzal
[@Big-Shark]: https://github.com/Big-Shark