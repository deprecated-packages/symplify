# Changelog for Symplify 8.x

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

PRs and issues are linked, so you can find more about it. Thanks to [ChangelogLinker](https://github.com/symplify/changelog-linker).

## [8.3.5] - 2020-09-17

### Added

#### CodingStandard

- [#2188] Fix [#2173] : Add No static properties rule for PHPStan, Thanks to [@samsonasik]

#### Unknown Package

- [#2189] Add NoStaticPropertyRule to symplify.neon, Thanks to [@samsonasik]

### Changed

#### CodingStandard

- [#2186] Make AnnotateRegexClassConstWithRegexLinkRule look only for _REGEX|_PATTERN suffix

#### Unknown Package

- [#2196] static removal

### Removed

- [#2190] remove static properties where possible

## [8.3.3] - 2020-09-16

### Changed

#### CodingStandard

- [#2182] Rename NoDebugFuncCallRule to ForbiddenFuncCallRule and make generic

#### Unknown Package

- [#2183] Correct when to pluralise word, Thanks to [@u01jmg3]

### Removed

#### EasyCodingStandard

- [#2184] Remove unary/not-operator conflicts, false positive

## [8.3.2] - 2020-09-15

### Added

#### CodingStandard

- [#2180] Add RemovePHPStormAnnotationFixer

#### EasyCodingStandard

- [#2178] Add markdown command gif to README

## [8.3.0] - 2020-09-14

### Changed

- [#2174] Decouple sub-package SnippetFormatter

## [8.3.6] - 2020-09-17

### Added

#### SmartFileSystem

- [#2198] Add JsonFileSystem

### Deprecated

#### AutoBindParameter

- [#2201] Deprecated compiler pass

### Removed

#### EasyCodingStandard

- [#2200] Drop autobind, use ParameterProvider with pre-defined constants instead

<!-- dumped content end -->

<!-- dumped content start -->
## [8.3.0]

### Added

#### CodingStandard

- [#2151] Add NoInlineStringRegexRule
- [#2164] Add CheckDirPathExistanceRule
- [#2156] Add StandardizeHereNowDocKeywordFixer and SpaceAfterCommaHereNowDocFixer
- [#2160] Add NoNewOutsideFactoryRule
- [#2150] Add NoPostIncPostDecRule
- [#2161] Add RequireDataProviderTestMethodRule
- [#2165] Add parent if check to NoMissingDirPathRule

#### EasyCodingStandard

- [#2170] Add multiple files/directories support to check-markdown command

#### PHPStanExtensions

- [#2155] Add argument swap check

#### SmartFileSystem

- [#2162] Add SmartFinder

#### rector

- [#2171] Add naming set

### Changed

#### CodingStandard

- [#2154] Make fixture dir always Fixture
- [#2153] 3 new PHPStan rules

#### EasyCodingStandard

- [#2174] Decouple sub-package SnippetFormatter
- [#2157] Report conflicting unary

#### Unknown Package

- [#2163] composer: be open about PHP 8 and beyond

### Fixed

#### ChangelogLinker

- [#2175] Fix url resolving for repos in SSH format, Thanks to [@jawira]

#### CodingStandard

- [#2166] Fix AnnotateRegexClassConstWithRegexLinkRule for letter

#### Unknown Package

- [#2159] Typo fix: packakges to packages, Thanks to [@samsonasik]

## [8.2.27] - 2020-09-09

### Added

- [#2143] Add --fix option to formatter markdown/heredoc-nowdoc command, Thanks to [@samsonasik]

### Changed

- [#2146] Failure test case for regex bug in heredoc-nowdoc formatter on multi snippet, Thanks to [@samsonasik]

### Fixed

- [#2147] Fix heredoc/nowdoc formatter regex for multiple code snippet in single php file, Thanks to [@samsonasik]

## [8.2.26] - 2020-09-08

### Added

#### CodingStandard

- [#2144] Add NoAbstractMethodRule

#### Unknown Package

- [#2141] Added --no-strict-types-declaration option to Formatter, Thanks to [@samsonasik]
- [#2140] Formatter: don't add <?php open tag if not exists in code snippet, Thanks to [@samsonasik]

## [8.2.25] - 2020-09-08

#### CodingStandard

- [#2138] Add various Object Calisthenics rules

## [8.2.24] - 2020-09-07

#### Unknown Package

- [#2137] Add HeredocNowdocPHPCodeFormatter to format php code inside heredoc, Thanks to [@samsonasik]

## [8.2.22] - 2020-09-07

#### CodingStandard

- [#2122] Add NoStaticCall rule
- [#2136] Add UppercaseConstantRule
- [#2135] Add TooLongVariableRule
- [#2132] Add TooManyFieldsRule
- [#2131] Add ExcessivePublicCountRule
- [#2128] Add PrefferedStaticCallOverFuncCallRule
- [#2127] Add ExcessiveParameterListRule

#### Unknown Package

- [#2118] Fixes [#2055] add MarkdownCodeFormatter to format markdown code, Thanks to [@samsonasik]

### Changed

#### CodingStandard

- [#2124] Rename max_cognitive_complexity to max_method_cognitive_complexity

#### EasyCodingStandard

- [#2116] Simplify README
- [#2125] Align rule-sets with PHP-CS-Fixer sets, Thanks to [@ckrack]

### Deprecated

- [#2129] Drop deprecated find command, move to ecs.php

### Fixed

#### CodingStandard

- [#2123] Fix preffered class rule for static calls

#### EasyCodingStandard

- [#2133] Fix spacing in MarkdownPHPCodeFormatter

#### EasyTesting

- [#2117] Fix StaticFixtureUpdater

## [8.2.20] - 2020-09-02

#### SetConfigResolver

- [#2112] Fix set loading in realpath phar

## [8.2.18] - 2020-09-01

### Changed

#### PHPStanExtensions

- [#2107] Show files if multiple per message

## [8.2.17] - 2020-08-31

### Added

#### ChangelogLinker

- [#2103] Add config constants REPOSITORY_URL, Thanks to [@zingimmick]

### Changed

#### Unknown Package

- [#2106] Allow PHP 8.0
- [#2104] Allow PHP 8.0

<!-- dumped content end -->

<!-- dumped content start -->
## [v8.2.15] - 2020-08-28

### Added

#### PHPStanExtensions

- [#2099] Add NoReturnArrayVariableList

### Changed

#### CodingStandard

- [#2097] Skip parent-enforced reference in NoReferenceRule

#### Unknown Package

- [#2101] restore slevomat, finally working with new phpdoc-parser
- [#2098] From arrays to value objects

## [v8.2.14] - 2020-08-26

### Added

#### CodingStandard

- [#2094] Add ForbiddenComplexArrayConfigInSetRule, ForbiddenArrayDestructRule, ForbiddenArrayWithStringKeysRule, RequireStringArgumentInMethodCallRule

### Changed

- [#2095] Skip closure use in NoReferenceRule

## [v8.2.12] - 2020-08-24

- [#2093] Improve array list indents

#### Unknown Package

- [#2091] Move dependencies to require-dev, Thanks to [@enumag]

## [v8.2.10] - 2020-08-22

### Added

#### CodingStandard

- [#2086] Add NoEntityManagerInControllerRule, NoGetRepositoryOutsideConstructorRule

### Changed

- [#2089] README update with coding-standard registrations

### Fixed

#### EasyCodingStandard

- [#2088] Fix config example showing usage of CACHE_DIRECTORY option as array, Thanks to [@nclsHart]

## [v8.2.8] - 2020-08-18

### Changed

#### CodingStandard

- [#2084] Various NewlineInNestedAnnotationFixer improvements

## [v8.2.6] - 2020-08-18

#### Unknown Package

- [#2082] do not show output if not needed

## [v8.2.5] - 2020-08-18

#### CodingStandard

- [#2081] Improve nested array annotations

#### static

- [#2080] Stricter params

<!-- dumped content end -->

<!-- dumped content start -->
## [v8.2.4] - 2020-08-18

### Added

#### CI

- [#2060] Add Rector CI

#### CodingStandard

- [#2078] Add anntotation new-line indent rule

#### EasyCodingStandard

- [#2071] Add missing require in scoper config, Thanks to [@nclsHart]
- [#2069] Add doctrine annotations set, switch set strings to constants

#### MonorepoBuilder

- [#2073] Add asterisk split support

#### Unknown Package

- [#2070] add constant dashes string method

### Changed

#### ChangelogLinker

- [#2074] YAML to PHP

#### CodingStandard

- [#2079] Improving README

#### PHPStanExtensions

- [#2072] Clear trait path in report

### Fixed

#### EasyCodingStandard

- [#2075] Fix common.php, Thanks to [@enumag]

## [v8.2.3] - 2020-08-14

### Added

#### CodingStandard

- [#2066] Add no array access rule to README and set
- [#2062] Add NoArrayAccessOnObjectRule

### Changed

#### Unknown Package

- [#2057] Update symfony-risky.php, Thanks to [@seb-jean]

<!-- dumped content end -->

<!-- dumped content start -->
## [v8.2.2]

### Added

#### CodingStandard

- [#2054] Add PreventParentMethodVisibilityOverrideRule

### Changed

#### Unknown Package

- [#2051] Update php-cs-fixer-psr2.php, Thanks to [@seb-jean]
- [#2050] Update symfony.php, Thanks to [@seb-jean]
- [#2049] Update symfony-risky.php, Thanks to [@seb-jean]

### Deprecated

#### ParameterNameGuard

- [#2056] Deprecated for PHP config with constants

## [v8.1.21] - 2020-08-07

### Added

#### EasyCodingStandard

- [#2044] Add rest of config constants

#### Unknown Package

- [#2045] Add PHP syntax to README
- [#2042] add "strict" set to EasyCodingStandardSetProvider, Thanks to [@hustlahusky]

### Changed

- [#2043] Update ecs.php, Thanks to [@cafferata]

### Deprecated

#### EasyCodingStandard

- [#2046] Warn about deprecated YAML syntax
- [#2040] Deprecate "find" command

## [v8.1.20] - 2020-08-06

### Removed

- [#2039] remove slevomat cs, breaking build for too many months

## [v8.1.19] - 2020-07-30

### Added

#### EasyTesting

- [#2035] Add directory compare assertion and fixture updater

<!-- dumped content end -->

<!-- dumped content start -->
## [v8.1.18] - 2020-07-29

### Changed

#### CodingStandard

- [#2030] Make NoFuncCallInMethodCallRule skip fqn names

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

- [#1990] Add NoFuncCallInMethodCallRule
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
[#2056]: https://github.com/symplify/symplify/pull/2056
[#2054]: https://github.com/symplify/symplify/pull/2054
[#2051]: https://github.com/symplify/symplify/pull/2051
[#2050]: https://github.com/symplify/symplify/pull/2050
[#2049]: https://github.com/symplify/symplify/pull/2049
[#2046]: https://github.com/symplify/symplify/pull/2046
[#2045]: https://github.com/symplify/symplify/pull/2045
[#2044]: https://github.com/symplify/symplify/pull/2044
[#2043]: https://github.com/symplify/symplify/pull/2043
[#2042]: https://github.com/symplify/symplify/pull/2042
[#2040]: https://github.com/symplify/symplify/pull/2040
[#2039]: https://github.com/symplify/symplify/pull/2039
[#2035]: https://github.com/symplify/symplify/pull/2035
[v8.2.2]: https://github.com/symplify/symplify/compare/v8.1.21...v8.2.2
[v8.1.21]: https://github.com/symplify/symplify/compare/v8.1.20...v8.1.21
[v8.1.20]: https://github.com/symplify/symplify/compare/v8.1.19...v8.1.20
[v8.1.19]: https://github.com/symplify/symplify/compare/v8.1.18...v8.1.19
[v8.1.18]: https://github.com/symplify/symplify/compare/v8.1.15...v8.1.18
[@seb-jean]: https://github.com/seb-jean
[@hustlahusky]: https://github.com/hustlahusky
[@cafferata]: https://github.com/cafferata
[#2079]: https://github.com/symplify/symplify/pull/2079
[#2078]: https://github.com/symplify/symplify/pull/2078
[#2075]: https://github.com/symplify/symplify/pull/2075
[#2074]: https://github.com/symplify/symplify/pull/2074
[#2073]: https://github.com/symplify/symplify/pull/2073
[#2072]: https://github.com/symplify/symplify/pull/2072
[#2071]: https://github.com/symplify/symplify/pull/2071
[#2070]: https://github.com/symplify/symplify/pull/2070
[#2069]: https://github.com/symplify/symplify/pull/2069
[#2066]: https://github.com/symplify/symplify/pull/2066
[#2062]: https://github.com/symplify/symplify/pull/2062
[#2060]: https://github.com/symplify/symplify/pull/2060
[#2057]: https://github.com/symplify/symplify/pull/2057
[v8.2.3]: https://github.com/symplify/symplify/compare/v8.2.2...v8.2.3
[@nclsHart]: https://github.com/nclsHart
[@enumag]: https://github.com/enumag
[#2101]: https://github.com/symplify/symplify/pull/2101
[#2099]: https://github.com/symplify/symplify/pull/2099
[#2098]: https://github.com/symplify/symplify/pull/2098
[#2097]: https://github.com/symplify/symplify/pull/2097
[#2095]: https://github.com/symplify/symplify/pull/2095
[#2094]: https://github.com/symplify/symplify/pull/2094
[#2093]: https://github.com/symplify/symplify/pull/2093
[#2091]: https://github.com/symplify/symplify/pull/2091
[#2089]: https://github.com/symplify/symplify/pull/2089
[#2088]: https://github.com/symplify/symplify/pull/2088
[#2086]: https://github.com/symplify/symplify/pull/2086
[#2084]: https://github.com/symplify/symplify/pull/2084
[#2082]: https://github.com/symplify/symplify/pull/2082
[#2081]: https://github.com/symplify/symplify/pull/2081
[#2080]: https://github.com/symplify/symplify/pull/2080
[v8.2.8]: https://github.com/symplify/symplify/compare/v8.2.6...v8.2.8
[v8.2.6]: https://github.com/symplify/symplify/compare/v8.2.5...v8.2.6
[v8.2.5]: https://github.com/symplify/symplify/compare/v8.2.4...v8.2.5
[v8.2.4]: https://github.com/symplify/symplify/compare/v8.2.3...v8.2.4
[v8.2.14]: https://github.com/symplify/symplify/compare/v8.2.12...v8.2.14
[v8.2.12]: https://github.com/symplify/symplify/compare/v8.2.10...v8.2.12
[v8.2.10]: https://github.com/symplify/symplify/compare/v8.2.8...v8.2.10
[#2175]: https://github.com/symplify/symplify/pull/2175
[#2174]: https://github.com/symplify/symplify/pull/2174
[#2171]: https://github.com/symplify/symplify/pull/2171
[#2170]: https://github.com/symplify/symplify/pull/2170
[#2166]: https://github.com/symplify/symplify/pull/2166
[#2165]: https://github.com/symplify/symplify/pull/2165
[#2164]: https://github.com/symplify/symplify/pull/2164
[#2163]: https://github.com/symplify/symplify/pull/2163
[#2162]: https://github.com/symplify/symplify/pull/2162
[#2161]: https://github.com/symplify/symplify/pull/2161
[#2160]: https://github.com/symplify/symplify/pull/2160
[#2159]: https://github.com/symplify/symplify/pull/2159
[#2157]: https://github.com/symplify/symplify/pull/2157
[#2156]: https://github.com/symplify/symplify/pull/2156
[#2155]: https://github.com/symplify/symplify/pull/2155
[#2154]: https://github.com/symplify/symplify/pull/2154
[#2153]: https://github.com/symplify/symplify/pull/2153
[#2151]: https://github.com/symplify/symplify/pull/2151
[#2150]: https://github.com/symplify/symplify/pull/2150
[#2147]: https://github.com/symplify/symplify/pull/2147
[#2146]: https://github.com/symplify/symplify/pull/2146
[#2144]: https://github.com/symplify/symplify/pull/2144
[#2143]: https://github.com/symplify/symplify/pull/2143
[#2141]: https://github.com/symplify/symplify/pull/2141
[#2140]: https://github.com/symplify/symplify/pull/2140
[#2138]: https://github.com/symplify/symplify/pull/2138
[#2137]: https://github.com/symplify/symplify/pull/2137
[#2136]: https://github.com/symplify/symplify/pull/2136
[#2135]: https://github.com/symplify/symplify/pull/2135
[#2133]: https://github.com/symplify/symplify/pull/2133
[#2132]: https://github.com/symplify/symplify/pull/2132
[#2131]: https://github.com/symplify/symplify/pull/2131
[#2129]: https://github.com/symplify/symplify/pull/2129
[#2128]: https://github.com/symplify/symplify/pull/2128
[#2127]: https://github.com/symplify/symplify/pull/2127
[#2125]: https://github.com/symplify/symplify/pull/2125
[#2124]: https://github.com/symplify/symplify/pull/2124
[#2123]: https://github.com/symplify/symplify/pull/2123
[#2122]: https://github.com/symplify/symplify/pull/2122
[#2118]: https://github.com/symplify/symplify/pull/2118
[#2117]: https://github.com/symplify/symplify/pull/2117
[#2116]: https://github.com/symplify/symplify/pull/2116
[#2112]: https://github.com/symplify/symplify/pull/2112
[#2107]: https://github.com/symplify/symplify/pull/2107
[#2106]: https://github.com/symplify/symplify/pull/2106
[#2104]: https://github.com/symplify/symplify/pull/2104
[#2103]: https://github.com/symplify/symplify/pull/2103
[#2055]: https://github.com/symplify/symplify/pull/2055
[v8.2.15]: https://github.com/symplify/symplify/compare/v8.2.14...v8.2.15
[@samsonasik]: https://github.com/samsonasik
[@jawira]: https://github.com/jawira
[@ckrack]: https://github.com/ckrack
[8.3.0]: https://github.com/symplify/symplify/compare/8.2.27...8.3.0
[8.2.27]: https://github.com/symplify/symplify/compare/8.2.26...8.2.27
[8.2.26]: https://github.com/symplify/symplify/compare/8.2.25...8.2.26
[8.2.25]: https://github.com/symplify/symplify/compare/8.2.24...8.2.25
[8.2.24]: https://github.com/symplify/symplify/compare/8.2.22...8.2.24
[8.2.22]: https://github.com/symplify/symplify/compare/8.2.20...8.2.22
[8.2.20]: https://github.com/symplify/symplify/compare/8.2.18...8.2.20
[8.2.18]: https://github.com/symplify/symplify/compare/8.2.17...8.2.18
[8.2.17]: https://github.com/symplify/symplify/compare/v8.2.15...8.2.17
[#2201]: https://github.com/symplify/symplify/pull/2201
[#2200]: https://github.com/symplify/symplify/pull/2200
[#2198]: https://github.com/symplify/symplify/pull/2198
[#2196]: https://github.com/symplify/symplify/pull/2196
[#2190]: https://github.com/symplify/symplify/pull/2190
[#2189]: https://github.com/symplify/symplify/pull/2189
[#2188]: https://github.com/symplify/symplify/pull/2188
[#2186]: https://github.com/symplify/symplify/pull/2186
[#2184]: https://github.com/symplify/symplify/pull/2184
[#2183]: https://github.com/symplify/symplify/pull/2183
[#2182]: https://github.com/symplify/symplify/pull/2182
[#2180]: https://github.com/symplify/symplify/pull/2180
[#2178]: https://github.com/symplify/symplify/pull/2178
[#2173]: https://github.com/symplify/symplify/pull/2173
[8.3.5]: https://github.com/symplify/symplify/compare/8.3.3...8.3.5
[8.3.3]: https://github.com/symplify/symplify/compare/8.3.2...8.3.3
[8.3.2]: https://github.com/symplify/symplify/compare/8.3.0...8.3.2
[#2447]: https://github.com/symplify/symplify/pull/2447
[#2446]: https://github.com/symplify/symplify/pull/2446
[#2445]: https://github.com/symplify/symplify/pull/2445
[#2444]: https://github.com/symplify/symplify/pull/2444
[#2443]: https://github.com/symplify/symplify/pull/2443
[#2441]: https://github.com/symplify/symplify/pull/2441
[#2439]: https://github.com/symplify/symplify/pull/2439
[#2438]: https://github.com/symplify/symplify/pull/2438
[#2437]: https://github.com/symplify/symplify/pull/2437
[#2436]: https://github.com/symplify/symplify/pull/2436
[#2435]: https://github.com/symplify/symplify/pull/2435
[#2433]: https://github.com/symplify/symplify/pull/2433
[#2432]: https://github.com/symplify/symplify/pull/2432
[#2431]: https://github.com/symplify/symplify/pull/2431
[#2430]: https://github.com/symplify/symplify/pull/2430
[#2429]: https://github.com/symplify/symplify/pull/2429
[#2428]: https://github.com/symplify/symplify/pull/2428
[#2427]: https://github.com/symplify/symplify/pull/2427
[#2426]: https://github.com/symplify/symplify/pull/2426
[#2424]: https://github.com/symplify/symplify/pull/2424
[#2423]: https://github.com/symplify/symplify/pull/2423
[#2422]: https://github.com/symplify/symplify/pull/2422
[#2420]: https://github.com/symplify/symplify/pull/2420
[#2419]: https://github.com/symplify/symplify/pull/2419
[#2418]: https://github.com/symplify/symplify/pull/2418
[#2417]: https://github.com/symplify/symplify/pull/2417
[#2416]: https://github.com/symplify/symplify/pull/2416
[#2413]: https://github.com/symplify/symplify/pull/2413
[#2412]: https://github.com/symplify/symplify/pull/2412
[#2411]: https://github.com/symplify/symplify/pull/2411
[#2410]: https://github.com/symplify/symplify/pull/2410
[#2409]: https://github.com/symplify/symplify/pull/2409
[#2408]: https://github.com/symplify/symplify/pull/2408
[#2407]: https://github.com/symplify/symplify/pull/2407
[#2406]: https://github.com/symplify/symplify/pull/2406
[#2403]: https://github.com/symplify/symplify/pull/2403
[#2402]: https://github.com/symplify/symplify/pull/2402
[#2401]: https://github.com/symplify/symplify/pull/2401
[#2399]: https://github.com/symplify/symplify/pull/2399
[#2398]: https://github.com/symplify/symplify/pull/2398
[#2397]: https://github.com/symplify/symplify/pull/2397
[#2396]: https://github.com/symplify/symplify/pull/2396
[#2395]: https://github.com/symplify/symplify/pull/2395
[#2394]: https://github.com/symplify/symplify/pull/2394
[#2393]: https://github.com/symplify/symplify/pull/2393
[#2392]: https://github.com/symplify/symplify/pull/2392
[#2391]: https://github.com/symplify/symplify/pull/2391
[#2389]: https://github.com/symplify/symplify/pull/2389
[#2388]: https://github.com/symplify/symplify/pull/2388
[#2387]: https://github.com/symplify/symplify/pull/2387
[#2386]: https://github.com/symplify/symplify/pull/2386
[#2385]: https://github.com/symplify/symplify/pull/2385
[#2384]: https://github.com/symplify/symplify/pull/2384
[#2383]: https://github.com/symplify/symplify/pull/2383
[#2381]: https://github.com/symplify/symplify/pull/2381
[#2380]: https://github.com/symplify/symplify/pull/2380
[#2379]: https://github.com/symplify/symplify/pull/2379
[#2378]: https://github.com/symplify/symplify/pull/2378
[#2377]: https://github.com/symplify/symplify/pull/2377
[#2375]: https://github.com/symplify/symplify/pull/2375
[#2374]: https://github.com/symplify/symplify/pull/2374
[#2373]: https://github.com/symplify/symplify/pull/2373
[#2372]: https://github.com/symplify/symplify/pull/2372
[#2371]: https://github.com/symplify/symplify/pull/2371
[#2370]: https://github.com/symplify/symplify/pull/2370
[#2369]: https://github.com/symplify/symplify/pull/2369
[#2368]: https://github.com/symplify/symplify/pull/2368
[#2366]: https://github.com/symplify/symplify/pull/2366
[#2365]: https://github.com/symplify/symplify/pull/2365
[#2364]: https://github.com/symplify/symplify/pull/2364
[#2362]: https://github.com/symplify/symplify/pull/2362
[#2361]: https://github.com/symplify/symplify/pull/2361
[#2360]: https://github.com/symplify/symplify/pull/2360
[#2359]: https://github.com/symplify/symplify/pull/2359
[#2358]: https://github.com/symplify/symplify/pull/2358
[#2357]: https://github.com/symplify/symplify/pull/2357
[#2356]: https://github.com/symplify/symplify/pull/2356
[#2355]: https://github.com/symplify/symplify/pull/2355
[#2353]: https://github.com/symplify/symplify/pull/2353
[#2352]: https://github.com/symplify/symplify/pull/2352
[#2351]: https://github.com/symplify/symplify/pull/2351
[#2350]: https://github.com/symplify/symplify/pull/2350
[#2349]: https://github.com/symplify/symplify/pull/2349
[#2348]: https://github.com/symplify/symplify/pull/2348
[#2347]: https://github.com/symplify/symplify/pull/2347
[#2344]: https://github.com/symplify/symplify/pull/2344
[#2343]: https://github.com/symplify/symplify/pull/2343
[#2342]: https://github.com/symplify/symplify/pull/2342
[#2341]: https://github.com/symplify/symplify/pull/2341
[#2340]: https://github.com/symplify/symplify/pull/2340
[#2339]: https://github.com/symplify/symplify/pull/2339
[#2337]: https://github.com/symplify/symplify/pull/2337
[#2336]: https://github.com/symplify/symplify/pull/2336
[#2335]: https://github.com/symplify/symplify/pull/2335
[#2333]: https://github.com/symplify/symplify/pull/2333
[#2332]: https://github.com/symplify/symplify/pull/2332
[#2331]: https://github.com/symplify/symplify/pull/2331
[#2329]: https://github.com/symplify/symplify/pull/2329
[#2328]: https://github.com/symplify/symplify/pull/2328
[#2325]: https://github.com/symplify/symplify/pull/2325
[#2324]: https://github.com/symplify/symplify/pull/2324
[#2323]: https://github.com/symplify/symplify/pull/2323
[#2320]: https://github.com/symplify/symplify/pull/2320
[#2319]: https://github.com/symplify/symplify/pull/2319
[#2318]: https://github.com/symplify/symplify/pull/2318
[#2317]: https://github.com/symplify/symplify/pull/2317
[#2316]: https://github.com/symplify/symplify/pull/2316
[#2315]: https://github.com/symplify/symplify/pull/2315
[#2314]: https://github.com/symplify/symplify/pull/2314
[#2313]: https://github.com/symplify/symplify/pull/2313
[#2311]: https://github.com/symplify/symplify/pull/2311
[#2310]: https://github.com/symplify/symplify/pull/2310
[#2308]: https://github.com/symplify/symplify/pull/2308
[#2307]: https://github.com/symplify/symplify/pull/2307
[#2305]: https://github.com/symplify/symplify/pull/2305
[#2304]: https://github.com/symplify/symplify/pull/2304
[#2303]: https://github.com/symplify/symplify/pull/2303
[#2302]: https://github.com/symplify/symplify/pull/2302
[#2301]: https://github.com/symplify/symplify/pull/2301
[#2300]: https://github.com/symplify/symplify/pull/2300
[#2299]: https://github.com/symplify/symplify/pull/2299
[#2298]: https://github.com/symplify/symplify/pull/2298
[#2297]: https://github.com/symplify/symplify/pull/2297
[#2294]: https://github.com/symplify/symplify/pull/2294
[#2293]: https://github.com/symplify/symplify/pull/2293
[#2292]: https://github.com/symplify/symplify/pull/2292
[#2291]: https://github.com/symplify/symplify/pull/2291
[#2290]: https://github.com/symplify/symplify/pull/2290
[#2289]: https://github.com/symplify/symplify/pull/2289
[#2288]: https://github.com/symplify/symplify/pull/2288
[#2287]: https://github.com/symplify/symplify/pull/2287
[#2286]: https://github.com/symplify/symplify/pull/2286
[#2285]: https://github.com/symplify/symplify/pull/2285
[#2284]: https://github.com/symplify/symplify/pull/2284
[#2282]: https://github.com/symplify/symplify/pull/2282
[#2281]: https://github.com/symplify/symplify/pull/2281
[#2280]: https://github.com/symplify/symplify/pull/2280
[#2279]: https://github.com/symplify/symplify/pull/2279
[#2278]: https://github.com/symplify/symplify/pull/2278
[#2277]: https://github.com/symplify/symplify/pull/2277
[#2276]: https://github.com/symplify/symplify/pull/2276
[#2275]: https://github.com/symplify/symplify/pull/2275
[#2274]: https://github.com/symplify/symplify/pull/2274
[#2272]: https://github.com/symplify/symplify/pull/2272
[#2271]: https://github.com/symplify/symplify/pull/2271
[#2270]: https://github.com/symplify/symplify/pull/2270
[#2269]: https://github.com/symplify/symplify/pull/2269
[#2267]: https://github.com/symplify/symplify/pull/2267
[#2266]: https://github.com/symplify/symplify/pull/2266
[#2265]: https://github.com/symplify/symplify/pull/2265
[#2264]: https://github.com/symplify/symplify/pull/2264
[#2261]: https://github.com/symplify/symplify/pull/2261
[#2259]: https://github.com/symplify/symplify/pull/2259
[#2257]: https://github.com/symplify/symplify/pull/2257
[#2255]: https://github.com/symplify/symplify/pull/2255
[#2254]: https://github.com/symplify/symplify/pull/2254
[#2253]: https://github.com/symplify/symplify/pull/2253
[#2252]: https://github.com/symplify/symplify/pull/2252
[#2250]: https://github.com/symplify/symplify/pull/2250
[#2249]: https://github.com/symplify/symplify/pull/2249
[#2248]: https://github.com/symplify/symplify/pull/2248
[#2247]: https://github.com/symplify/symplify/pull/2247
[#2246]: https://github.com/symplify/symplify/pull/2246
[#2245]: https://github.com/symplify/symplify/pull/2245
[#2243]: https://github.com/symplify/symplify/pull/2243
[#2241]: https://github.com/symplify/symplify/pull/2241
[#2239]: https://github.com/symplify/symplify/pull/2239
[#2238]: https://github.com/symplify/symplify/pull/2238
[#2237]: https://github.com/symplify/symplify/pull/2237
[#2235]: https://github.com/symplify/symplify/pull/2235
[#2234]: https://github.com/symplify/symplify/pull/2234
[#2232]: https://github.com/symplify/symplify/pull/2232
[#2231]: https://github.com/symplify/symplify/pull/2231
[#2229]: https://github.com/symplify/symplify/pull/2229
[#2227]: https://github.com/symplify/symplify/pull/2227
[#2226]: https://github.com/symplify/symplify/pull/2226
[#2225]: https://github.com/symplify/symplify/pull/2225
[#2224]: https://github.com/symplify/symplify/pull/2224
[#2223]: https://github.com/symplify/symplify/pull/2223
[#2222]: https://github.com/symplify/symplify/pull/2222
[#2219]: https://github.com/symplify/symplify/pull/2219
[#2216]: https://github.com/symplify/symplify/pull/2216
[#2215]: https://github.com/symplify/symplify/pull/2215
[#2214]: https://github.com/symplify/symplify/pull/2214
[#2213]: https://github.com/symplify/symplify/pull/2213
[#2212]: https://github.com/symplify/symplify/pull/2212
[#2211]: https://github.com/symplify/symplify/pull/2211
[#2210]: https://github.com/symplify/symplify/pull/2210
[#2209]: https://github.com/symplify/symplify/pull/2209
[#2207]: https://github.com/symplify/symplify/pull/2207
[#2206]: https://github.com/symplify/symplify/pull/2206
[#2204]: https://github.com/symplify/symplify/pull/2204
[#2203]: https://github.com/symplify/symplify/pull/2203
[#2202]: https://github.com/symplify/symplify/pull/2202
[#2199]: https://github.com/symplify/symplify/pull/2199
[#2195]: https://github.com/symplify/symplify/pull/2195
[#2177]: https://github.com/symplify/symplify/pull/2177
[#2176]: https://github.com/symplify/symplify/pull/2176
[#2172]: https://github.com/symplify/symplify/pull/2172
[#2167]: https://github.com/symplify/symplify/pull/2167
[#2139]: https://github.com/symplify/symplify/pull/2139
[#2013]: https://github.com/symplify/symplify/pull/2013
[@vyacheslav-startsev]: https://github.com/vyacheslav-startsev
[@required]: https://github.com/required
[@janatjak]: https://github.com/janatjak
[@glensc]: https://github.com/glensc
[@ThomasLandauer]: https://github.com/ThomasLandauer
[@NoorAdiana]: https://github.com/NoorAdiana
[@Kerrialn]: https://github.com/Kerrialn
[@JanMikes]: https://github.com/JanMikes
[@ComiR]: https://github.com/ComiR
[@BoGnY]: https://github.com/BoGnY
[8.3.6]: https://github.com/symplify/symplify/compare/8.3.0...8.3.6
[#2490]: https://github.com/symplify/symplify/pull/2490
[#2486]: https://github.com/symplify/symplify/pull/2486
[#2485]: https://github.com/symplify/symplify/pull/2485
[#2483]: https://github.com/symplify/symplify/pull/2483
[#2482]: https://github.com/symplify/symplify/pull/2482
[#2481]: https://github.com/symplify/symplify/pull/2481
[#2480]: https://github.com/symplify/symplify/pull/2480
[#2478]: https://github.com/symplify/symplify/pull/2478
[#2477]: https://github.com/symplify/symplify/pull/2477
[#2476]: https://github.com/symplify/symplify/pull/2476
[#2475]: https://github.com/symplify/symplify/pull/2475
[#2474]: https://github.com/symplify/symplify/pull/2474
[#2473]: https://github.com/symplify/symplify/pull/2473
[#2470]: https://github.com/symplify/symplify/pull/2470
[#2469]: https://github.com/symplify/symplify/pull/2469
[#2468]: https://github.com/symplify/symplify/pull/2468
[#2467]: https://github.com/symplify/symplify/pull/2467
[#2466]: https://github.com/symplify/symplify/pull/2466
[#2464]: https://github.com/symplify/symplify/pull/2464
[#2462]: https://github.com/symplify/symplify/pull/2462
[#2461]: https://github.com/symplify/symplify/pull/2461
[#2460]: https://github.com/symplify/symplify/pull/2460
[#2459]: https://github.com/symplify/symplify/pull/2459
[#2458]: https://github.com/symplify/symplify/pull/2458
[#2457]: https://github.com/symplify/symplify/pull/2457
[#2456]: https://github.com/symplify/symplify/pull/2456
[#2455]: https://github.com/symplify/symplify/pull/2455
[#2454]: https://github.com/symplify/symplify/pull/2454
[#2453]: https://github.com/symplify/symplify/pull/2453
[#2452]: https://github.com/symplify/symplify/pull/2452
[#2451]: https://github.com/symplify/symplify/pull/2451
[#2449]: https://github.com/symplify/symplify/pull/2449
[#2448]: https://github.com/symplify/symplify/pull/2448
[#2442]: https://github.com/symplify/symplify/pull/2442
[#2425]: https://github.com/symplify/symplify/pull/2425
[#2405]: https://github.com/symplify/symplify/pull/2405
[#2404]: https://github.com/symplify/symplify/pull/2404
[#2208]: https://github.com/symplify/symplify/pull/2208
[@github-actions]: https://github.com/github-actions
[9.0.0-BETA1]: https://github.com/symplify/symplify/compare/8.3.5...9.0.0-BETA1
[#2568]: https://github.com/symplify/symplify/pull/2568
[#2567]: https://github.com/symplify/symplify/pull/2567
[#2566]: https://github.com/symplify/symplify/pull/2566
[#2564]: https://github.com/symplify/symplify/pull/2564
[#2563]: https://github.com/symplify/symplify/pull/2563
[#2562]: https://github.com/symplify/symplify/pull/2562
[#2561]: https://github.com/symplify/symplify/pull/2561
[#2559]: https://github.com/symplify/symplify/pull/2559
[#2558]: https://github.com/symplify/symplify/pull/2558
[#2557]: https://github.com/symplify/symplify/pull/2557
[#2556]: https://github.com/symplify/symplify/pull/2556
[#2555]: https://github.com/symplify/symplify/pull/2555
[#2554]: https://github.com/symplify/symplify/pull/2554
[#2553]: https://github.com/symplify/symplify/pull/2553
[#2552]: https://github.com/symplify/symplify/pull/2552
[#2551]: https://github.com/symplify/symplify/pull/2551
[#2550]: https://github.com/symplify/symplify/pull/2550
[#2549]: https://github.com/symplify/symplify/pull/2549
[#2548]: https://github.com/symplify/symplify/pull/2548
[#2547]: https://github.com/symplify/symplify/pull/2547
[#2546]: https://github.com/symplify/symplify/pull/2546
[#2545]: https://github.com/symplify/symplify/pull/2545
[#2544]: https://github.com/symplify/symplify/pull/2544
[#2543]: https://github.com/symplify/symplify/pull/2543
[#2542]: https://github.com/symplify/symplify/pull/2542
[#2541]: https://github.com/symplify/symplify/pull/2541
[#2540]: https://github.com/symplify/symplify/pull/2540
[#2539]: https://github.com/symplify/symplify/pull/2539
[#2538]: https://github.com/symplify/symplify/pull/2538
[#2537]: https://github.com/symplify/symplify/pull/2537
[#2536]: https://github.com/symplify/symplify/pull/2536
[#2535]: https://github.com/symplify/symplify/pull/2535
[#2534]: https://github.com/symplify/symplify/pull/2534
[#2533]: https://github.com/symplify/symplify/pull/2533
[#2532]: https://github.com/symplify/symplify/pull/2532
[#2531]: https://github.com/symplify/symplify/pull/2531
[#2529]: https://github.com/symplify/symplify/pull/2529
[#2528]: https://github.com/symplify/symplify/pull/2528
[#2527]: https://github.com/symplify/symplify/pull/2527
[#2525]: https://github.com/symplify/symplify/pull/2525
[#2524]: https://github.com/symplify/symplify/pull/2524
[#2522]: https://github.com/symplify/symplify/pull/2522
[#2521]: https://github.com/symplify/symplify/pull/2521
[#2520]: https://github.com/symplify/symplify/pull/2520
[#2519]: https://github.com/symplify/symplify/pull/2519
[#2517]: https://github.com/symplify/symplify/pull/2517
[#2515]: https://github.com/symplify/symplify/pull/2515
[#2514]: https://github.com/symplify/symplify/pull/2514
[#2513]: https://github.com/symplify/symplify/pull/2513
[#2512]: https://github.com/symplify/symplify/pull/2512
[#2511]: https://github.com/symplify/symplify/pull/2511
[#2510]: https://github.com/symplify/symplify/pull/2510
[#2509]: https://github.com/symplify/symplify/pull/2509
[#2506]: https://github.com/symplify/symplify/pull/2506
[#2505]: https://github.com/symplify/symplify/pull/2505
[#2504]: https://github.com/symplify/symplify/pull/2504
[#2503]: https://github.com/symplify/symplify/pull/2503
[#2502]: https://github.com/symplify/symplify/pull/2502
[#2501]: https://github.com/symplify/symplify/pull/2501
[#2500]: https://github.com/symplify/symplify/pull/2500
[#2499]: https://github.com/symplify/symplify/pull/2499
[#2498]: https://github.com/symplify/symplify/pull/2498
[#2497]: https://github.com/symplify/symplify/pull/2497
[#2496]: https://github.com/symplify/symplify/pull/2496
[#2495]: https://github.com/symplify/symplify/pull/2495
[#2494]: https://github.com/symplify/symplify/pull/2494
[#2493]: https://github.com/symplify/symplify/pull/2493
[#2492]: https://github.com/symplify/symplify/pull/2492
[#2330]: https://github.com/symplify/symplify/pull/2330
[#2327]: https://github.com/symplify/symplify/pull/2327
[#2108]: https://github.com/symplify/symplify/pull/2108
[@tomasnorre]: https://github.com/tomasnorre
[@szepeviktor]: https://github.com/szepeviktor
[@alexcutts]: https://github.com/alexcutts
[9.0.0-BETA2]: https://github.com/symplify/symplify/compare/9.0.0-BETA1...9.0.0-BETA2
