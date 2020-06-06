# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

PRs and issues are linked, so you can find more about it. Thanks to [ChangelogLinker](https://github.com/symplify/changelog-linker).

<!-- changelog-linker -->

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
