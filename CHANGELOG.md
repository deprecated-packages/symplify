# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

PRs and issues are linked, so you can find more about it. Thanks to [ChangelogLinker](https://github.com/Symplify/ChangelogLinker).

<!-- changelog-linker -->

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
[#1483]: https://github.com/Symplify/Symplify/pull/1483
[v6.0.0]: https://github.com/Symplify/Symplify/compare/v5.4.15...v6.0.0
[@wppd]: https://github.com/wppd
[@solcik]: https://github.com/solcik
[@shyim]: https://github.com/shyim
[@possi]: https://github.com/possi
[@nlubisch]: https://github.com/nlubisch
[@jeroennoten]: https://github.com/jeroennoten
[@ektarum]: https://github.com/ektarum
[@crazko]: https://github.com/crazko
[@TomasLudvik]: https://github.com/TomasLudvik
[@JanMikes]: https://github.com/JanMikes
