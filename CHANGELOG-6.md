# Changelog for Symplify 6.x

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

PRs and issues are linked, so you can find more about it. Thanks to [ChangelogLinker](https://github.com/symplify/changelog-linker).

## [v6.1.0] - 2019-09-18

### Added

#### ChangelogLinker

- [#1589] allow releasing in multiple branches, Thanks to [@vitek-rostislav]

### Fixed

#### CodingStandard

- [#1611] Fix `LineLength` for breaking nowdoc

#### ChangelogLinker

- [#1605] Honoring final new line, Thanks to [@jawira]

#### MonorepoBuilder

- [#1597] Dynamic monorepo-builder version in Init templates, Thanks to [@natepage]
- [#1595] Stop requiring the remote URL to end with '.git', Thanks to [@Sargeros]

#### PackageBuilder

- [#1608] Skip parameters following an end of options (--) signal, Thanks to [@mantiz]
- [#1601] Determine relative paths using Symfony's makePathRelative(), Thanks to [@fitztrev]

#### Statie

- [#1587] Canonicalise md suffix for jekyll migrations, Thanks to [@dsas]

## [v6.0.5] - 2019-07-26

### Added

#### Autodiscovery

- [#1584] Add `--filter` option to `convert-yaml` command

#### CodingStandard

- [#1582] Add `RemoveSpacingAroundModifierAndConstFixer`

#### EasyCodingStandard

- [#1576] Add `validate` command

### Changed

#### PackageBuilder

- [#1578] Allow custom vendor path on windows system ([#1577]), Thanks to [@JohnDoe8521]

### Fixed

#### CodingStandard

- [#1585] Make `UnusedPublicMethodSniff` skip entities

#### MonorepoBuilder

- [#1581] Handle if `GITHUB_TOKEN` is an empty string, Thanks to [@mxr576]

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

[#1552]: https://github.com/symplify/symplify/pull/1552
[#1551]: https://github.com/symplify/symplify/pull/1551
[#1548]: https://github.com/symplify/symplify/pull/1548
[#1541]: https://github.com/symplify/symplify/pull/1541
[#1540]: https://github.com/symplify/symplify/pull/1540
[#1538]: https://github.com/symplify/symplify/pull/1538
[#1537]: https://github.com/symplify/symplify/pull/1537
[#1536]: https://github.com/symplify/symplify/pull/1536
[#1535]: https://github.com/symplify/symplify/pull/1535
[#1534]: https://github.com/symplify/symplify/pull/1534
[#1528]: https://github.com/symplify/symplify/pull/1528
[#1527]: https://github.com/symplify/symplify/pull/1527
[#1525]: https://github.com/symplify/symplify/pull/1525
[#1521]: https://github.com/symplify/symplify/pull/1521
[#1514]: https://github.com/symplify/symplify/pull/1514
[#1512]: https://github.com/symplify/symplify/pull/1512
[#1511]: https://github.com/symplify/symplify/pull/1511
[#1510]: https://github.com/symplify/symplify/pull/1510
[#1509]: https://github.com/symplify/symplify/pull/1509
[#1502]: https://github.com/symplify/symplify/pull/1502
[#1500]: https://github.com/symplify/symplify/pull/1500
[#1499]: https://github.com/symplify/symplify/pull/1499
[#1498]: https://github.com/symplify/symplify/pull/1498
[#1493]: https://github.com/symplify/symplify/pull/1493
[#1489]: https://github.com/symplify/symplify/pull/1489
[#1488]: https://github.com/symplify/symplify/pull/1488
[v6.0.0]: https://github.com/symplify/symplify/compare/v5.4.15...v6.0.0
[@wppd]: https://github.com/wppd
[@solcik]: https://github.com/solcik
[@shyim]: https://github.com/shyim
[@possi]: https://github.com/possi
[@nlubisch]: https://github.com/nlubisch
[@ektarum]: https://github.com/ektarum
[@crazko]: https://github.com/crazko
[@TomasLudvik]: https://github.com/TomasLudvik
[@JanMikes]: https://github.com/JanMikes

[#1573]: https://github.com/symplify/symplify/pull/1573
[#1569]: https://github.com/symplify/symplify/pull/1569
[#1568]: https://github.com/symplify/symplify/pull/1568
[#1567]: https://github.com/symplify/symplify/pull/1567
[#1565]: https://github.com/symplify/symplify/pull/1565
[#1563]: https://github.com/symplify/symplify/pull/1563
[#1561]: https://github.com/symplify/symplify/pull/1561
[#1559]: https://github.com/symplify/symplify/pull/1559
[#1558]: https://github.com/symplify/symplify/pull/1558
[v6.0.3]: https://github.com/symplify/symplify/compare/v6.0.2...v6.0.3
[v6.0.2]: https://github.com/symplify/symplify/compare/v6.0.1...v6.0.2
[v6.0.1]: https://github.com/symplify/symplify/compare/v6.0.0...v6.0.1
[@jakzal]: https://github.com/jakzal
[@Big-Shark]: https://github.com/Big-Shark
[#1585]: https://github.com/symplify/symplify/pull/1585
[#1584]: https://github.com/symplify/symplify/pull/1584
[#1582]: https://github.com/symplify/symplify/pull/1582
[#1581]: https://github.com/symplify/symplify/pull/1581
[#1578]: https://github.com/symplify/symplify/pull/1578
[#1577]: https://github.com/symplify/symplify/pull/1577
[#1576]: https://github.com/symplify/symplify/pull/1576
[@mxr576]: https://github.com/mxr576
[@JohnDoe8521]: https://github.com/JohnDoe8521
[v6.0.4]: https://github.com/symplify/symplify/compare/v6.0.3...v6.0.4
[#1611]: https://github.com/symplify/symplify/pull/1611
[#1608]: https://github.com/symplify/symplify/pull/1608
[#1605]: https://github.com/symplify/symplify/pull/1605
[#1601]: https://github.com/symplify/symplify/pull/1601
[#1597]: https://github.com/symplify/symplify/pull/1597
[#1595]: https://github.com/symplify/symplify/pull/1595
[#1589]: https://github.com/symplify/symplify/pull/1589
[#1587]: https://github.com/symplify/symplify/pull/1587
[@vitek-rostislav]: https://github.com/vitek-rostislav
[@natepage]: https://github.com/natepage
[@mantiz]: https://github.com/mantiz
[@jawira]: https://github.com/jawira
[@fitztrev]: https://github.com/fitztrev
[@dsas]: https://github.com/dsas
[@Sargeros]: https://github.com/Sargeros
[v6.0.5]: https://github.com/symplify/symplify/compare/v6.0.4...v6.0.5
[#1644]: https://github.com/symplify/symplify/pull/1644
[#1643]: https://github.com/symplify/symplify/pull/1643
[#1642]: https://github.com/symplify/symplify/pull/1642
[#1641]: https://github.com/symplify/symplify/pull/1641
[#1637]: https://github.com/symplify/symplify/pull/1637
[#1635]: https://github.com/symplify/symplify/pull/1635
[#1630]: https://github.com/symplify/symplify/pull/1630
[#1629]: https://github.com/symplify/symplify/pull/1629
[#1627]: https://github.com/symplify/symplify/pull/1627
[#1623]: https://github.com/symplify/symplify/pull/1623
[#1622]: https://github.com/symplify/symplify/pull/1622
[#1616]: https://github.com/symplify/symplify/pull/1616
[@ruudk]: https://github.com/ruudk
[@SerafimArts]: https://github.com/SerafimArts
[v6.1.0]: https://github.com/symplify/symplify/compare/v6.0.5...v6.1.0
[#1656]: https://github.com/symplify/symplify/pull/1656
[#1655]: https://github.com/symplify/symplify/pull/1655
[#1650]: https://github.com/symplify/symplify/pull/1650
[#1649]: https://github.com/symplify/symplify/pull/1649
[#1645]: https://github.com/symplify/symplify/pull/1645
[@orklah]: https://github.com/orklah
[#1675]: https://github.com/symplify/symplify/pull/1675
[#1674]: https://github.com/symplify/symplify/pull/1674
[#1671]: https://github.com/symplify/symplify/pull/1671
[#1670]: https://github.com/symplify/symplify/pull/1670
[#1669]: https://github.com/symplify/symplify/pull/1669
[#1668]: https://github.com/symplify/symplify/pull/1668
[#1667]: https://github.com/symplify/symplify/pull/1667
[#1666]: https://github.com/symplify/symplify/pull/1666
[#1663]: https://github.com/symplify/symplify/pull/1663
[#1662]: https://github.com/symplify/symplify/pull/1662
[@fchris82]: https://github.com/fchris82
[#1694]: https://github.com/symplify/symplify/pull/1694
[#1693]: https://github.com/symplify/symplify/pull/1693
[#1690]: https://github.com/symplify/symplify/pull/1690
[#1687]: https://github.com/symplify/symplify/pull/1687
[#1686]: https://github.com/symplify/symplify/pull/1686
[#1685]: https://github.com/symplify/symplify/pull/1685
[#1684]: https://github.com/symplify/symplify/pull/1684
[#1682]: https://github.com/symplify/symplify/pull/1682
[#1681]: https://github.com/symplify/symplify/pull/1681
[#1677]: https://github.com/symplify/symplify/pull/1677
[#1676]: https://github.com/symplify/symplify/pull/1676
[v7.0.2]: https://github.com/symplify/symplify/compare/v7.0.1...v7.0.2
[v7.0.1]: https://github.com/symplify/symplify/compare/v7.0.0...v7.0.1
[@sustmi]: https://github.com/sustmi
[@DayS]: https://github.com/DayS
[v7.0.0]: https://github.com/symplify/symplify/compare/v6.1.0...v7.0.0
[#1726]: https://github.com/symplify/symplify/pull/1726
[#1724]: https://github.com/symplify/symplify/pull/1724
[#1723]: https://github.com/symplify/symplify/pull/1723
[#1722]: https://github.com/symplify/symplify/pull/1722
[#1720]: https://github.com/symplify/symplify/pull/1720
[#1718]: https://github.com/symplify/symplify/pull/1718
[#1717]: https://github.com/symplify/symplify/pull/1717
[#1716]: https://github.com/symplify/symplify/pull/1716
[#1715]: https://github.com/symplify/symplify/pull/1715
[#1713]: https://github.com/symplify/symplify/pull/1713
[#1711]: https://github.com/symplify/symplify/pull/1711
[#1710]: https://github.com/symplify/symplify/pull/1710
[#1708]: https://github.com/symplify/symplify/pull/1708
[#1707]: https://github.com/symplify/symplify/pull/1707
[#1706]: https://github.com/symplify/symplify/pull/1706
[#1705]: https://github.com/symplify/symplify/pull/1705
[#1704]: https://github.com/symplify/symplify/pull/1704
[#1698]: https://github.com/symplify/symplify/pull/1698
[#1696]: https://github.com/symplify/symplify/pull/1696
[#1695]: https://github.com/symplify/symplify/pull/1695
[#1692]: https://github.com/symplify/symplify/pull/1692
[v7.1.3]: https://github.com/symplify/symplify/compare/v7.1.2...v7.1.3
[v7.1.2]: https://github.com/symplify/symplify/compare/v7.1.1...v7.1.2
[v7.1.1]: https://github.com/symplify/symplify/compare/v7.1...v7.1.1
[v7.1]: https://github.com/symplify/symplify/compare/v7.0.2...v7.1
[@schrapel]: https://github.com/schrapel
[@ltribolet]: https://github.com/ltribolet
[@leofeyer]: https://github.com/leofeyer
[@enumag]: https://github.com/enumag
[@Rarst]: https://github.com/Rarst
[#1737]: https://github.com/symplify/symplify/pull/1737
[#1735]: https://github.com/symplify/symplify/pull/1735
[#1734]: https://github.com/symplify/symplify/pull/1734
[#1731]: https://github.com/symplify/symplify/pull/1731
[#1728]: https://github.com/symplify/symplify/pull/1728
[v7.2.0]: https://github.com/symplify/symplify/compare/v7.1.3...v7.2.0
[#1773]: https://github.com/symplify/symplify/pull/1773
[#1772]: https://github.com/symplify/symplify/pull/1772
[#1771]: https://github.com/symplify/symplify/pull/1771
[#1768]: https://github.com/symplify/symplify/pull/1768
[#1767]: https://github.com/symplify/symplify/pull/1767
[#1762]: https://github.com/symplify/symplify/pull/1762
[#1760]: https://github.com/symplify/symplify/pull/1760
[#1759]: https://github.com/symplify/symplify/pull/1759
[#1757]: https://github.com/symplify/symplify/pull/1757
[#1756]: https://github.com/symplify/symplify/pull/1756
[#1755]: https://github.com/symplify/symplify/pull/1755
[#1754]: https://github.com/symplify/symplify/pull/1754
[#1753]: https://github.com/symplify/symplify/pull/1753
[#1752]: https://github.com/symplify/symplify/pull/1752
[#1751]: https://github.com/symplify/symplify/pull/1751
[#1748]: https://github.com/symplify/symplify/pull/1748
[#1747]: https://github.com/symplify/symplify/pull/1747
[#1745]: https://github.com/symplify/symplify/pull/1745
[#1741]: https://github.com/symplify/symplify/pull/1741
[#1740]: https://github.com/symplify/symplify/pull/1740
[#1739]: https://github.com/symplify/symplify/pull/1739
[v7.2.2]: https://github.com/symplify/symplify/compare/v7.2.1...v7.2.2
[v7.2.1]: https://github.com/symplify/symplify/compare/v7.2.0...v7.2.1
[@ondraondra81]: https://github.com/ondraondra81
[@migrify]: https://github.com/migrify
