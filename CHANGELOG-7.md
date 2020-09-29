# Changelog for Symplify 7.x

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

PRs and issues are linked, so you can find more about it. Thanks to [ChangelogLinker](https://github.com/symplify/changelog-linker).

## [v7.3.11] - 2020-05-11

### Added

#### EasyCodingStandard

- [#1935] added phar info & link to README.md, Thanks to [@c33s]

### Changed

#### ParamaterNameGuard

- [#1938] Decouple new package

#### SmartFileSystem

- [#1936] read zero sized files, Thanks to [@c33s]

## [v7.3.10] - 2020-05-07

### Added

#### CodingStandard

- [#1933] Add NoDefaultParameterValueRule + NoNullableParameterValueRule
- [#1932] Add RequireMethodCallArgumentConstantRule
- [#1931] Add NoDynamicMethodNameRule + NoDynamicPropertyFetchNameRule
- [#1930] Add [@see] rule to refer test case

### Changed

- [#1929] Move rules from PHPStan Extensions

### Fixed

## [v7.3.9] - 2020-05-05

### Changed

#### ConsoleColorDiff

- [#1920] Init new package

## [v7.3.7] - 2020-05-04

### Added

#### CodingStandard

- [#1913] Improve README, add example for every rule

### Deprecated

- [#1914] Deprecate PropertyOrderByComplexityFixer

## [v7.3.6] - 2020-05-04

- [#1912] Deprecate MethodOrderByTypeFixer
- [#1911] Deprecate PrivateMethodOrderByUseFixer
- [#1910] Deprecate ClassStringToClassConstantFixer

## [v7.3.5] - 2020-05-03

### Added

- [#1906] Add ClassNameRespectsParentSuffixRule, deprecate ClassNameSuffixByParentSniff
- [#1905] Add NoDebugFunCallRule + deprecate DebugFunctionCallSniff

#### PHPStanExtensions

- [#1908] Add AbstractServiceAwareRuleTestCase

### Changed

#### CodingStandard

- [#1909] Make use of phpstan generic test case extension

<!-- dumped content end -->

## [v7.3.4] - 2020-05-02

### Added

#### CodingStandard

- [#1901] Add NoReferenceRule + Deprecate ForbiddenReferenceSniff
- [#1899] Deprecate DuplicatedClassShortNameSniff, Added NoDuplicatedShortClassNameRule
- [#1896] Add PreferredClassRule, deprecate PrefferedClassSniff

### Changed

#### SymfonyStaticDumper

- [#1895] controller with multiple args, Thanks to [@Jibbarth]

### Deprecated

#### CodingStandard

- [#1904] Deprecate PropertyNameMatchingTypeFixer
- [#1903] Deprecate ArrayPropertyDefaultValueFixer
- [#1898] Deprecate VarConstantCommentSniff
- [#1894] Deprecate AnnotationTypeExistsSniff
- [#1893] Deprecate BoolPropertyDefaultValueFixer
- [#1897] Move rules location one level up, remove category

## [v7.3.3] - 2020-05-02

### Added

- [#1891] Add ForbiddenParentClassRule, deprecate ForbiddenParentClassSniff
- [#1892] Add NoDefaultExceptionRule +  Deprecate ExplicitExceptionSniff

### Deprecated

- [#1890] Deprecate ForbiddenDoubleAssignSniff

## [v7.3.2] - 2020-05-02

### Changed

#### EasyCodingStandard

- [#1889] Switch compiler to kernel

### Fixed

#### CodingStandard

- [#1886] Fixing cognitive complexity

#### EasyCodingStandard

- [#1888] Fixing compiler command
- [#1885] Fix ecs.phar compilation with phpstan

## [v7.3.1] - 2020-05-01

### Deprecated

#### CodingStandard

- [#1884] Deprecate ForbiddenStaticFunctionSniff + Deprecate RemoveEmptyDocBlockFixer
- [#1883] Deprecate cognitive complexity sniff

## [v7.3.0] - 2020-05-01

### Added

- [#1869] Add PHPStan version for object-calisthenics, Add PHPStan rule for cogntiive complexity
- [#1880] Add ClassLikeCognitiveComplexityRule
- [#1878] Add NoClassWithStaticMethodWithoutStaticNameRule

### Changed

- [#1877] move object calisthenics to its package
- [#1876] update README

#### EasyCodingStandard

- [#1882] Do not install box.phar on ecs require

#### MonorepoBuilder

- [#1873] Handling version exceptions in "init" command, Thanks to [@liarco]

#### SymfonyStaticDumper

- [#1875] Handle render page created with TemplateController, Thanks to [@Jibbarth]

### Deprecated

#### CodingStandard

- [#1881] Apply cognitive PHPStan rules to Symplify
- [#1879] Deprecate CatchExceptionNameMatchingTypeFixer

## [v7.2.20] - 2020-04-26

- [#1863] Deprecate TraitNameSniff, InterfaceNameSnfif and AbstractClassNameSniff to slam/phpstan-extensions
- [#1864] Deprecate RemoveEndOfFunctionCommentFixer
- [#1865] Deprecate FinalInterfaceFixer
- [#1866] Deprecate PregDelimiterFixer, deprecate RequireFollowedByAbsolutePathFixer

### Removed

#### EasyCodingStandard

- [#1862] Drop buggy UnusedPublicMethodSniff and dual run feature, better use Rector [BC break]
- [#1861] Show file/fixer/sniff on --debug, drop warning() method fallback in ECS Style

## [v7.2.19] - 2020-04-24

#### AutowireArrayParameter

- [#1860] Drop nette/application dependency

## [v7.2.18] - 2020-04-24

### Added

#### ComposerJsonManipulator

- [#1859] Add option to add file path, getAllClassmaps(), simple test

### Changed

- [#1836] Do not replace the symfony php70 polyfill in easy-coding-standard, Thanks to [@Agares]

## [v7.2.17] - 2020-04-20

### Deprecated

#### EasyCodingStandard

- [#1857] Deprecate "psr2" set

## [v7.2.16] - 2020-04-20

### Changed

- [#1854] Exclude breaking slevomat rules from "types" set
- [#1855] lock to last working slevomat/coding-standard 6.2

### Fixed

- [#1856] use slevomat 6.3.2 fixed

## [v7.2.14] - 2020-04-19

#### CodingStandard

- [#1852] Remove deprecated SprintfOverContactSniff, fix excludes to dir-paths in phar

## [v7.2.13] - 2020-04-19

### Added

#### MonorepoBuilder

- [#1850] Add release keyword for ReleaseCommand

### Changed

#### EasyCodingStandard

- [#1851] Building phar improvements, refactoring to bundles over relative configs

#### MonorepoBuilder

- [#1848] Skip update of YAML file

#### SymfonyStaticDumper

- [#1847] Improve progress reporting
- [#1844] Improve naming
- [#1849] Decouple info method

### Fixed

- [#1841] Move PackageBuilder dependency to require-dev, Thanks to [@TiGR]

## [v7.2.12] - 2020-04-10

### Added

#### ComposerJsonManipulator

- [#1839] Add ComposerJsonManipulatorBundle

### Changed

#### MonorepoBuilder

- [#1837] Improve validate command to include config

## [v7.2.11] - 2020-04-09

### Added

- [#1835] Add bundles

## [v7.2.10] - 2020-04-08

### Added

#### EasyHydrator

- [#1833] Add monorepo split
- [#1832] Init new package

#### SymfonyStaticDumper

- [#1826] [Static Dumper] Add compiler pass in kernel, Thanks to [@Jibbarth]

#### MonorepoBuilder

- [#1821] Add composer exclude folders dynamicly, Thanks to [@bennsel]

### Changed

#### ComposerJsonManipulator

- [#1822] Init new package

#### MonorepoBuilder

- [#1820] Allow inline sections to be empty for 3rd party use

#### PHPStanExtensions

- [#1818] Decouple symplify error formatter config

### Fixed

- [#1829] Fix command in README.md, Thanks to [@natepage]
- [#1824] fix [#1712], Thanks to [@calvera]

### Removed

- [#1817] Remove typo referencing not-relevant file, Thanks to [@GenieTim]

## [v7.2.8] - 2020-03-19

### Fixed

#### MonorepoBuilder

- [#1816] ISS_1814 Fixing the merge command of the monorepo builder tool, Thanks to [@cgaube]

## [v7.2.6] - 2020-03-18

#### SymfonyStaticDumper

- [#1811] Fix route name

## [v7.2.5] - 2020-03-17

### Added

- [#1808] Add dot file test case

### Changed

- [#1806] Improve dumping of argument-based controllers

## [v7.2.4] - 2020-03-12

### Added

- [#1805] Add new package

### Changed

- [#1798] Update README.md, Thanks to [@Great-Antique]

### Deprecated

#### Statie

- [#1803] Deprecate package

## [v7.2.3] - 2020-02-27

### Added

#### CodingStandard

- [#1776] Add ClassCognitiveComplexitySniff

#### MonorepoBuilder

- [#1781] Add `SortAutoloadNamespaceCommand`
- [#1755] Add "After split" testing
- [#1756] Add ComposerJson value object
- [#1760] Add application test

### Changed

#### CodingStandard

- [#1748] Make import paths phar friendly

#### EasyCodingStandard

- [#1747] Skip scoping of php cs fixer and code sniffer
- [#1762] add ecs.phar prefix build to travis

#### FlexLoader

- [#1782] Priority of loaded configs, Thanks to [@vrbata]

#### LatteToTwigConverter

- [#1751] decouple to [@migrify]

#### MonorepoBuilder

- [#1759] Decopule ComposerKeyMerger
- [#1794] Revert split --branch feature
- [#1772] SplitCommand default branch option to current branch, Thanks to [@natepage]
- [#1767] Specific branch for split, Thanks to [@natepage]

#### PHPStanExtensions

- [#1779] Make BoolishClassMethodPrefixRule skip parent interface required methods
- [#1757] Merge pull request [#1757] from symplify/phsptan-reonce

#### SOLID

- [#1786] Move constant variables/propeties to constants

### Deprecated

#### CodingStandard

- [#1773] Deprecate SprintfOverContactSniff

### Fixed

- [#1753] Merge pull request [#1753] from symplify/drop-vendor-dir
- [#1768] fix: replace non exist site link to blog post, Thanks to [@ondraondra81]
- [#1774] use fixed phpstan rector
- [#1785] fix static

### Removed

#### MonorepoBuilder

- [#1771] Remove unused InvalidBranchException, Thanks to [@natepage]

#### Statie

- [#1777] Remove MigratorSculpin, Remove MigratorJekyll, unused

## [v7.2.2] - 2020-01-20

### Changed

- [#1745] use pcov for coverage
- [#1740] move Travis from subpackages to Github Action
- [#1739] Working with git on Github Actions tests

#### ChangelogLinker

- [#1741] Skip test that yield different values on after split and are already tested

## [v7.2.1] - 2020-01-11

### Added

#### EasyCodingStandard

- [#1735] Add paths parameter to ECS
- [#1734] Add ecs.phar

### Changed

- [#1731] Travis to Github Actions
- [#1737] Improve Github Actions

## [v7.2.0] - 2020-01-06

- [#1716] Decouple new **AutoBindParameter** package
- [#1715] Decouple new **AutowireArrayParameter** package

### Added

#### PHPStanExtension

- [#1723] add missing deps

#### PHPStanExtensions

- [#1722] Add BoolishClassMethodPrefixRule

### Changed

- [#1728] travis: speedup coverage with pcov

#### EasyCodingStandard

- [#1724] show reported by rule, so its easier to recognize from message
- [#1726] Update GrumpPHP tool to use core task, Thanks to [@schrapel]

#### CodingStandard

- [#1720] Improve cognitive complexity nesting, Thanks to [@Rarst]

### Fixed

#### EasyCodingStandard

- [#1717] Fix default cache directory documentation, Thanks to [@ltribolet]
- [#1718] fix `SetNotFoundException` namespace

## [v7.1.3] - 2019-12-18

### Fixed

#### EasyCodingStandard

- [#1713] fix config loading regression

## [v7.1.2] - 2019-12-18

### Changed

#### CodingStandard

- [#1711] Use Data provider in tests

#### EasyCodingStandard

- [#1708] Allow config to override sets

### Fixed

#### CodingStandard

- [#1710] Fix PropertyNameMatchingTypeFixer for intersection

## [v7.1.1] - 2019-12-18

### Changed

- [#1695] open to PHPStan 0.12 and Rector supporting it

#### ChangelogLinker

- [#1692] Honouring final new line in changelog (for real), Thanks to [@jawira]

#### EasyCodingStandard

- [#1704] Prefer IncludeFixer over LanguageConstructSpacingSniff, Thanks to [@leofeyer]

#### PackageBuilder

- [#1705] Handle single characters in the StringFormatConverter::camelCaseToGlue() method, Thanks to [@leofeyer]

### Removed

#### EasyCodingStandard

- [#1707] remove unary + not opreator non-existing conflict

#### PHPStanExtensions

- [#1706] Drop confusing path ignore

#### PackageBuilder

- [#1698] Remove dependency on symfony/debug, Thanks to [@enumag]

## [v7.1] - 2019-12-09

### Added

#### EasyCodingStandard

- [#1690] Bump to Slevomat 6, add `dead-code` set

#### SetConfigResolver

- [#1694] add relative path for phar

### Changed

#### ChangelogLinker

- [#1687] Making URL resolver more generic, Thanks to [@jawira]

#### PHPStanExtensions

- [#1693] Upgrade to PHPStan 0.12

## [v7.0.2] - 2019-11-23

### Added

#### MonorepoBuilder

- [#1684] Add optional `--tag` input to set tag manually, Thanks to [@DayS]

### Changed

#### PHPStanExtensions

- [#1686] Make extension future compatible with prefixed everything

### Fixed

- [#1682] Windows text fixing

#### MonorepoBuilder

- [#1685] Fix option value regression

#### PackageBuilder

- [#1681] Fix kernel shutdown

## [v7.0.1] - 2019-11-23

### Added

- [#1677] Add missing composer dependency on the new package SetConfigResolver, Thanks to [@sustmi]

### Fixed

- [#1676] Test fixes

## [v7.0.0] - 2019-11-23

- [#1670] New package **SetConfigResolver**
- [#1643] New package **SmartFileSystem**

### Added

- [#1656] Add `--xdebug` option

#### ChangelogLinker

- [#1662] Make changed category as default fallback, add deprecated category

#### EasyCodingStandard

- [#1669] Drop overcomplicated `CustomSourceProviderInterface`, add `parameters > file_extensions` instead

#### MonorepoBuilder

- [#1671] Add `file://` option for repository, Thanks to [@fchris82]

### Changed

- [#1674] Allow Symfony 5
- [#1668] Bump to Symfony 4.3+
- [#1629] Allow Symfony 5 + bump to PHP 7.2 + add Rector CI run
- [#1630] Add Rector CI run
- [#1650] Travis Windows + composer paths, Thanks to [@orklah]

#### EasyCodingStandard

- [#1663] Change `--level` option to `--set`

### Removed

#### CodingStandard

- [#1667] Remove deprecated `NoClassInstantiationSniff`
- [#1675] remove deprecated classes

### Removed

#### CodingStandard

- [#1666] Remove `BlockPropertyCommentFixer` and use `PhpdocLineSpanFixer` instead

#### EasyCodingStandard

- [#1655] Add "sets" parameter for shorter imports of native configs

#### SmartFileSystem

- [#1649] Add `FileSystemGuard` and its exceptions

### Changed

- [#1650] Travis Windows + composer paths, Thanks to [@orklah]
- [#1644] travis: change to jobs

#### ChangelogLinker

- [#1645] Skip tests

#### CodingStandard

- [#1616] Improve `NoClassInstantiationSniff` + improve code complexity in ignored cases

### EasyCodingStanard

- [#1637] Only print metadata for console output, Thanks to [@ruudk]
- [#1635] Autowire `OutputFormatterInterface`, Thanks to [@ruudk]

### Fixed

- [#1623] Fix reading GIT tags in Windows OS, Thanks to [@SerafimArts]
- [#1622] Apply lowercase to compose dependencies, Thanks to [@SerafimArts]

### Removed

#### Statie

- [#1641] Drop `Latte` support to lower the complexity [BC break]
- [#1642] Change `FilterProviderInterface` to `TwigExtension` [BC break]

### Deprecated

#### CodingStandard

- [#1627] Deprecate `NoClassInstantiation` for inpractical and bloated usage

[@natepage]: https://github.com/natepage
[@jawira]: https://github.com/jawira
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
[#1756]: https://github.com/symplify/symplify/pull/1756
[#1755]: https://github.com/symplify/symplify/pull/1755
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
[#1776]: https://github.com/symplify/symplify/pull/1776
[#1794]: https://github.com/symplify/symplify/pull/1794
[#1786]: https://github.com/symplify/symplify/pull/1786
[#1785]: https://github.com/symplify/symplify/pull/1785
[#1782]: https://github.com/symplify/symplify/pull/1782
[#1779]: https://github.com/symplify/symplify/pull/1779
[#1777]: https://github.com/symplify/symplify/pull/1777
[@vrbata]: https://github.com/vrbata
[#1774]: https://github.com/symplify/symplify/pull/1774
[#1757]: https://github.com/symplify/symplify/pull/1757
[#1753]: https://github.com/symplify/symplify/pull/1753
[#1781]: https://github.com/symplify/symplify/pull/1781
[#1816]: https://github.com/symplify/symplify/pull/1816
[#1811]: https://github.com/symplify/symplify/pull/1811
[#1808]: https://github.com/symplify/symplify/pull/1808
[#1806]: https://github.com/symplify/symplify/pull/1806
[#1805]: https://github.com/symplify/symplify/pull/1805
[#1803]: https://github.com/symplify/symplify/pull/1803
[#1798]: https://github.com/symplify/symplify/pull/1798
[v7.2.6]: https://github.com/symplify/symplify/compare/v7.2.5...v7.2.6
[v7.2.5]: https://github.com/symplify/symplify/compare/v7.2.4...v7.2.5
[v7.2.4]: https://github.com/symplify/symplify/compare/v7.2.3...v7.2.4
[v7.2.3]: https://github.com/symplify/symplify/compare/v7.2.2...v7.2.3
[@cgaube]: https://github.com/cgaube
[@Great-Antique]: https://github.com/Great-Antique
[#1829]: https://github.com/symplify/symplify/pull/1829
[#1826]: https://github.com/symplify/symplify/pull/1826
[#1824]: https://github.com/symplify/symplify/pull/1824
[#1822]: https://github.com/symplify/symplify/pull/1822
[#1821]: https://github.com/symplify/symplify/pull/1821
[#1820]: https://github.com/symplify/symplify/pull/1820
[#1818]: https://github.com/symplify/symplify/pull/1818
[#1817]: https://github.com/symplify/symplify/pull/1817
[#1712]: https://github.com/symplify/symplify/pull/1712
[v7.2.8]: https://github.com/symplify/symplify/compare/v7.2.6...v7.2.8
[@calvera]: https://github.com/calvera
[@bennsel]: https://github.com/bennsel
[@Jibbarth]: https://github.com/Jibbarth
[@GenieTim]: https://github.com/GenieTim
[#1833]: https://github.com/symplify/symplify/pull/1833
[#1832]: https://github.com/symplify/symplify/pull/1832
[#1893]: https://github.com/symplify/symplify/pull/1893
[#1892]: https://github.com/symplify/symplify/pull/1892
[#1891]: https://github.com/symplify/symplify/pull/1891
[#1890]: https://github.com/symplify/symplify/pull/1890
[#1889]: https://github.com/symplify/symplify/pull/1889
[#1888]: https://github.com/symplify/symplify/pull/1888
[#1886]: https://github.com/symplify/symplify/pull/1886
[#1885]: https://github.com/symplify/symplify/pull/1885
[#1884]: https://github.com/symplify/symplify/pull/1884
[#1883]: https://github.com/symplify/symplify/pull/1883
[#1882]: https://github.com/symplify/symplify/pull/1882
[#1881]: https://github.com/symplify/symplify/pull/1881
[#1880]: https://github.com/symplify/symplify/pull/1880
[#1879]: https://github.com/symplify/symplify/pull/1879
[#1878]: https://github.com/symplify/symplify/pull/1878
[#1877]: https://github.com/symplify/symplify/pull/1877
[#1876]: https://github.com/symplify/symplify/pull/1876
[#1875]: https://github.com/symplify/symplify/pull/1875
[#1873]: https://github.com/symplify/symplify/pull/1873
[#1869]: https://github.com/symplify/symplify/pull/1869
[#1866]: https://github.com/symplify/symplify/pull/1866
[#1865]: https://github.com/symplify/symplify/pull/1865
[#1864]: https://github.com/symplify/symplify/pull/1864
[#1863]: https://github.com/symplify/symplify/pull/1863
[#1862]: https://github.com/symplify/symplify/pull/1862
[#1861]: https://github.com/symplify/symplify/pull/1861
[#1860]: https://github.com/symplify/symplify/pull/1860
[#1859]: https://github.com/symplify/symplify/pull/1859
[#1857]: https://github.com/symplify/symplify/pull/1857
[#1856]: https://github.com/symplify/symplify/pull/1856
[#1855]: https://github.com/symplify/symplify/pull/1855
[#1854]: https://github.com/symplify/symplify/pull/1854
[#1852]: https://github.com/symplify/symplify/pull/1852
[#1851]: https://github.com/symplify/symplify/pull/1851
[#1850]: https://github.com/symplify/symplify/pull/1850
[#1849]: https://github.com/symplify/symplify/pull/1849
[#1848]: https://github.com/symplify/symplify/pull/1848
[#1847]: https://github.com/symplify/symplify/pull/1847
[#1844]: https://github.com/symplify/symplify/pull/1844
[#1841]: https://github.com/symplify/symplify/pull/1841
[#1839]: https://github.com/symplify/symplify/pull/1839
[#1837]: https://github.com/symplify/symplify/pull/1837
[#1836]: https://github.com/symplify/symplify/pull/1836
[#1835]: https://github.com/symplify/symplify/pull/1835
[v7.3.3]: https://github.com/symplify/symplify/compare/v7.3.2...v7.3.3
[v7.3.2]: https://github.com/symplify/symplify/compare/v7.3.1...v7.3.2
[v7.3.1]: https://github.com/symplify/symplify/compare/v7.3.0...v7.3.1
[v7.3.0]: https://github.com/symplify/symplify/compare/v7.2.20...v7.3.0
[v7.2.20]: https://github.com/symplify/symplify/compare/v7.2.19...v7.2.20
[v7.2.19]: https://github.com/symplify/symplify/compare/v7.2.18...v7.2.19
[v7.2.18]: https://github.com/symplify/symplify/compare/v7.2.17...v7.2.18
[v7.2.17]: https://github.com/symplify/symplify/compare/v7.2.16...v7.2.17
[v7.2.16]: https://github.com/symplify/symplify/compare/v7.2.14...v7.2.16
[v7.2.14]: https://github.com/symplify/symplify/compare/v7.2.13...v7.2.14
[v7.2.13]: https://github.com/symplify/symplify/compare/v7.2.12...v7.2.13
[v7.2.12]: https://github.com/symplify/symplify/compare/v7.2.11...v7.2.12
[v7.2.11]: https://github.com/symplify/symplify/compare/v7.2.10...v7.2.11
[v7.2.10]: https://github.com/symplify/symplify/compare/v7.2.8...v7.2.10
[@liarco]: https://github.com/liarco
[@TiGR]: https://github.com/TiGR
[@Agares]: https://github.com/Agares
[#1904]: https://github.com/symplify/symplify/pull/1904
[#1903]: https://github.com/symplify/symplify/pull/1903
[#1901]: https://github.com/symplify/symplify/pull/1901
[#1900]: https://github.com/symplify/symplify/pull/1900
[#1899]: https://github.com/symplify/symplify/pull/1899
[#1898]: https://github.com/symplify/symplify/pull/1898
[#1897]: https://github.com/symplify/symplify/pull/1897
[#1896]: https://github.com/symplify/symplify/pull/1896
[#1895]: https://github.com/symplify/symplify/pull/1895
[#1894]: https://github.com/symplify/symplify/pull/1894
[#1968]: https://github.com/symplify/symplify/pull/1968
[#1967]: https://github.com/symplify/symplify/pull/1967
[#1966]: https://github.com/symplify/symplify/pull/1966
[#1965]: https://github.com/symplify/symplify/pull/1965
[#1964]: https://github.com/symplify/symplify/pull/1964
[#1959]: https://github.com/symplify/symplify/pull/1959
[#1957]: https://github.com/symplify/symplify/pull/1957
[#1956]: https://github.com/symplify/symplify/pull/1956
[#1955]: https://github.com/symplify/symplify/pull/1955
[#1951]: https://github.com/symplify/symplify/pull/1951
[#1945]: https://github.com/symplify/symplify/pull/1945
[#1944]: https://github.com/symplify/symplify/pull/1944
[#1943]: https://github.com/symplify/symplify/pull/1943
[#1942]: https://github.com/symplify/symplify/pull/1942
[#1941]: https://github.com/symplify/symplify/pull/1941
[#1938]: https://github.com/symplify/symplify/pull/1938
[#1936]: https://github.com/symplify/symplify/pull/1936
[#1935]: https://github.com/symplify/symplify/pull/1935
[#1934]: https://github.com/symplify/symplify/pull/1934
[#1933]: https://github.com/symplify/symplify/pull/1933
[#1932]: https://github.com/symplify/symplify/pull/1932
[#1931]: https://github.com/symplify/symplify/pull/1931
[#1930]: https://github.com/symplify/symplify/pull/1930
[#1929]: https://github.com/symplify/symplify/pull/1929
[#1927]: https://github.com/symplify/symplify/pull/1927
[#1920]: https://github.com/symplify/symplify/pull/1920
[#1914]: https://github.com/symplify/symplify/pull/1914
[#1913]: https://github.com/symplify/symplify/pull/1913
[#1912]: https://github.com/symplify/symplify/pull/1912
[#1911]: https://github.com/symplify/symplify/pull/1911
[#1910]: https://github.com/symplify/symplify/pull/1910
[#1909]: https://github.com/symplify/symplify/pull/1909
[#1908]: https://github.com/symplify/symplify/pull/1908
[#1906]: https://github.com/symplify/symplify/pull/1906
[#1905]: https://github.com/symplify/symplify/pull/1905
[#1902]: https://github.com/symplify/symplify/pull/1902
[v8.0.0-beta3]: https://github.com/symplify/symplify/compare/v8.0.0-beta2...v8.0.0-beta3
[v8.0.0-beta2]: https://github.com/symplify/symplify/compare/v8.0.0-beta1...v8.0.0-beta2
[v8.0.0-beta1]: https://github.com/symplify/symplify/compare/v7.3.11...v8.0.0-beta1
[v7.3.9]: https://github.com/symplify/symplify/compare/v7.3.7...v7.3.9
[v7.3.7]: https://github.com/symplify/symplify/compare/v7.3.6...v7.3.7
[v7.3.6]: https://github.com/symplify/symplify/compare/v7.3.5...v7.3.6
[v7.3.5]: https://github.com/symplify/symplify/compare/v7.3.4...v7.3.5
[v7.3.4]: https://github.com/symplify/symplify/compare/v7.3.3...v7.3.4
[v7.3.11]: https://github.com/symplify/symplify/compare/v7.3.10...v7.3.11
[v7.3.10]: https://github.com/symplify/symplify/compare/v7.3.9...v7.3.10
[@staabm]: https://github.com/staabm
[@see]: https://github.com/see
[@pesektomas]: https://github.com/pesektomas
[@drupol]: https://github.com/drupol
[@c33s]: https://github.com/c33s
