# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

PRs and issues are linked, so you can find more about it. Thanks to [ChangelogLinker](https://github.com/symplify/changelog-linker).

<!-- changelog-linker -->

<!-- dumped content start -->
## Unreleased

### Changed

#### CodingStandard

- [#2030] Make NoFunctionCallInMethodCallRule skip fqn names

#### Unknown Package

- [#2034] Update guzzlehttp/guzzle requirement from ^6.5 to ^6.5|^7.0, Thanks to [@zingimmick]
- [#2032] Wording, Thanks to [@u01jmg3]

#### cs

- [#2029] apply

## [v8.1.15] - 2020-07-21

#### MonorepoBuilder

- [#2027] Allow monorepo-builder.php

## [v8.1.14] - 2020-07-21

#### EasyCodingStandard

- [#2026] Use static map for sets

#### Unknown Package

- [#2025] switch Rector YAML to PHP

## [v8.1.12] - 2020-07-18

#### SetConfigResolver

- [#2024] Prefer .php version, return instant match

## [v8.1.11] - 2020-07-18

#### Unknown Package

- [#2023] correct config.php namespace

### Removed

- [#2021] Delete null, Thanks to [@shyim]

## [v8.1.10] - 2020-07-16

### Changed

- [#2018] Correct the type of the configuration., Thanks to [@stefangr]

### Fixed

#### EasyCodingStandard

- [#2020] Fix file hash computer for php file

## [v8.1.9] - 2020-07-16

- [#2017] fix YAML config sets parsing in case of atypical fixer/sniff registration

## [v8.1.8] - 2020-07-16

### Changed

- [#2014] Allow ecs.php config
- [#2012] Move sets from YAML to PHP, keep BC configs

## [v8.1.7] - 2020-07-15

### Added

#### SmartFileSystem

- [#2004] Add readFile() method + use PHP config over YAML

#### Unknown Package

- [#2002] Add a new conflict resolution, Thanks to [@u01jmg3]

### Changed

#### CodingStandard

- [#2007] Switch symplify coding standard from YAML to PHP

#### EasyCodingStandard

- [#2008] Move config from YAML to PHP
- [#2010] Prepare sets for switch to PHP

#### MonorepoBuilder

- [#2009] Switch config YAML to PHP

#### Unknown Package

- [#2005] YAML to PHP configs
- [#1997] switch YAML configuration to PHP
- [#1995] Find only docblocks that are parseable by phpdoc-parser, Thanks to [@JarJak]

### Fixed

- [#1998] Fix link to CategoryResolver, Thanks to [@jschaedl]

### Removed

#### ComposerJsonManipulator

- [#2003] Removing empty keys from json content before dumping to file, Thanks to [@liarco]

## [v8.1.6] - 2020-07-08

### Added

#### SmartFileSystem

- [#1993] Add getRealPathWithoutSuffix() method

### Fixed

#### Unknown Package

- [#1992] Fix broken URL in monorepo-builder Readme, Thanks to [@EnCz]

## [v8.1.4] - 2020-07-06

### Added

#### CodingStandard

- [#1990] Add NoFunctionCallInMethodCallRule
- [#1987] Add NoEmptyRule

### Changed

#### CI

- [#1988] Use Github Actions as a matrix - from 11 files to 2 🎉

## [v8.1.3] - 2020-07-04

### Added

#### CodingStandard

- [#1985] Add NoIssetOrEmptyOnObjectRule

## [v8.1.0] - 2020-06-25

### Changed

#### EasyTesting

- [#1984] Init new package

## [v8.0.1] - 2020-06-15

### Added

#### MonorepoBuilder

- [#1979] Add prefixed version

<!-- dumped content end -->

## [v8.0.0-beta3]

### Added

#### ChangelogLinker

- [#1966] added failing test with expected result in ChangelogLinkerTest, Thanks to [@pesektomas]

#### ParamaterNameGuard

- [#1968] Dislocate ParameterNameGuardBundle to prevent auto-adding on ECS install

### Changed

#### ChangelogLinker

- [#1965] Simplify ChangelogLinkerTest

### Fixed

- [#1967] Fix inner-link of words to link

## [v8.0.0-beta2]

#### MonorepoBuilder

- [#1964] Fix pre-release versioning for next version

## [v8.0.0-beta1]

### Added

- [#1944] add config class presence

### Changed

- [#1959] bump Rector 0.7.26

#### CodingStandard

- [#1943] Improve SeeAnnotationToTestRule

#### EasyCodingStandard

- [#1951] improve basic sets with new slevomat rules
- [#1957] Dislocate bundle locations to prevent symfony/flex autoregistration [BC break]

#### MonorepoBuilder

- [#1934] Switch from default workers to manually registered workers

#### PHPStanExtensions

- [#1942] Reduce dependencies

#### SmartFileSystem

- [#1955] Move separateFilesAndDirectories() from FileSystem here [BC break]

### Deprecated

- [#1945] Remove deprecated content
- [#1902] [Symplify 8] Remove deprecated code

### Fixed

- [#1941] Fix typos, Thanks to [@staabm]

### Removed

#### PackageBuilder

- [#1956] Drop too magic AutoReturnFactoryCompilerPass [BC break]

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
[#1934]: https://github.com/symplify/symplify/pull/1934
[#1902]: https://github.com/symplify/symplify/pull/1902
[v8.0.0-beta3]: https://github.com/symplify/symplify/compare/v8.0.0-beta2...v8.0.0-beta3
[v8.0.0-beta2]: https://github.com/symplify/symplify/compare/v8.0.0-beta1...v8.0.0-beta2
[@staabm]: https://github.com/staabm
[@pesektomas]: https://github.com/pesektomas
[#2034]: https://github.com/symplify/symplify/pull/2034
[#2032]: https://github.com/symplify/symplify/pull/2032
[#2030]: https://github.com/symplify/symplify/pull/2030
[#2029]: https://github.com/symplify/symplify/pull/2029
[#2027]: https://github.com/symplify/symplify/pull/2027
[#2026]: https://github.com/symplify/symplify/pull/2026
[#2025]: https://github.com/symplify/symplify/pull/2025
[#2024]: https://github.com/symplify/symplify/pull/2024
[#2023]: https://github.com/symplify/symplify/pull/2023
[#2021]: https://github.com/symplify/symplify/pull/2021
[#2020]: https://github.com/symplify/symplify/pull/2020
[#2018]: https://github.com/symplify/symplify/pull/2018
[#2017]: https://github.com/symplify/symplify/pull/2017
[#2014]: https://github.com/symplify/symplify/pull/2014
[#2012]: https://github.com/symplify/symplify/pull/2012
[#2010]: https://github.com/symplify/symplify/pull/2010
[#2009]: https://github.com/symplify/symplify/pull/2009
[#2008]: https://github.com/symplify/symplify/pull/2008
[#2007]: https://github.com/symplify/symplify/pull/2007
[#2005]: https://github.com/symplify/symplify/pull/2005
[#2004]: https://github.com/symplify/symplify/pull/2004
[#2003]: https://github.com/symplify/symplify/pull/2003
[#2002]: https://github.com/symplify/symplify/pull/2002
[#1998]: https://github.com/symplify/symplify/pull/1998
[#1997]: https://github.com/symplify/symplify/pull/1997
[#1995]: https://github.com/symplify/symplify/pull/1995
[#1993]: https://github.com/symplify/symplify/pull/1993
[#1992]: https://github.com/symplify/symplify/pull/1992
[#1990]: https://github.com/symplify/symplify/pull/1990
[#1988]: https://github.com/symplify/symplify/pull/1988
[#1987]: https://github.com/symplify/symplify/pull/1987
[#1985]: https://github.com/symplify/symplify/pull/1985
[#1984]: https://github.com/symplify/symplify/pull/1984
[#1979]: https://github.com/symplify/symplify/pull/1979
[v8.1.9]: https://github.com/symplify/symplify/compare/v8.1.8...v8.1.9
[v8.1.8]: https://github.com/symplify/symplify/compare/v8.1.7...v8.1.8
[v8.1.7]: https://github.com/symplify/symplify/compare/v8.1.6...v8.1.7
[v8.1.6]: https://github.com/symplify/symplify/compare/v8.1.4...v8.1.6
[v8.1.4]: https://github.com/symplify/symplify/compare/v8.1.3...v8.1.4
[v8.1.3]: https://github.com/symplify/symplify/compare/v8.1.0...v8.1.3
[v8.1.15]: https://github.com/symplify/symplify/compare/v8.1.14...v8.1.15
[v8.1.14]: https://github.com/symplify/symplify/compare/v8.1.12...v8.1.14
[v8.1.12]: https://github.com/symplify/symplify/compare/v8.1.11...v8.1.12
[v8.1.11]: https://github.com/symplify/symplify/compare/v8.1.10...v8.1.11
[v8.1.10]: https://github.com/symplify/symplify/compare/v8.1.9...v8.1.10
[v8.1.0]: https://github.com/symplify/symplify/compare/v8.0.1...v8.1.0
[v8.0.1]: https://github.com/symplify/symplify/compare/v8.0.0-beta3...v8.0.1
[@zingimmick]: https://github.com/zingimmick
[@u01jmg3]: https://github.com/u01jmg3
[@stefangr]: https://github.com/stefangr
[@shyim]: https://github.com/shyim
[@liarco]: https://github.com/liarco
[@jschaedl]: https://github.com/jschaedl
[@JarJak]: https://github.com/JarJak
[@EnCz]: https://github.com/EnCz
