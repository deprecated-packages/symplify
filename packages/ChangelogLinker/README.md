# Changelog Linker

[![Build Status](https://img.shields.io/travis/Symplify/ChangelogLinker/master.svg?style=flat-square)](https://travis-ci.org/Symplify/ChangelogLinker)
[![Downloads](https://img.shields.io/packagist/dt/symplify/changelog-linker.svg?style=flat-square)](https://packagist.org/packages/symplify/changelog-linker)
[![Subscribe](https://img.shields.io/badge/subscribe-to--releases-green.svg?style=flat-square)](https://libraries.io/packagist/symplify%2Fchangelog-linker)

Turn your `CHANGELOG.md` from a machine readable text to a **rich text that makes every programmer happy**.

## Install

```bash
composer require symplify/changelog-linker --dev
```

## Usage

Pass the file as argument and the repository as base for links via `--repository` option:

```bash
vendor/bin/changelog-linker CHANGELOG.md --repository https://github.com/symplify/symplify
```

## All The Cool Features

### 1. Link issues and PRs

:x:

```markdown
### Added

- #123 Cool new without detailed description wanting me to see PR
```

:+1:

```markdown
### Added

- [#123] Cool new without detailed description wanting me to see PR

[#123]: https://github.com/Symplify/Symplify/pull/123
```

### 2. Link to Commits

Sometimes PR is too big and 1 commit is so important to mention. Make it likable!

:x:

```markdown
### Added

- 9b154d9b6e88075e14b6812613bce7c1a2a79daa this was great change
```

:+1:

```markdown
### Added

- [9b154d] this was great change

[9b154d9]: https://github.com/Symplify/Symplify/commit/9b154d9b6e88075e14b6812613bce7c1a2a79daa
```

### 3. Link Versions to Diffs

:x:

```markdown
## v2.0.0 - 2017-12-31

- ...

## v1.5.0 - 2017-06-30
```

:+1:

```markdown
### [v2.0.0] - 2017-12-31

- ...

### v1.5.0 - 2017-06-30

[v2.0.0]: https://github.com/Symplify/Symplify/compare/v1.5.0...v2.0.0
```

### 4. Link Thanks to Users

Credit is much more valuable with link to follow

:x:

```markdown
### [v2.0.0] - 2017-12-31

- ... thanks @SpacePossum for help
```

:+1:

```markdown
### [v2.0.0] - 2017-12-31

- ... thanks [@SpacePossum] for help

[@SpacePossum]: https://github.com/SpacePossum
```

### 5. Turn "Unreleased" to The Last Release

:x:

```markdown
## Unreleased

Includes the version and the date. Executes on every new tag, that is not already added in `CHANGELOG.md`.

### Added

...
```

:+1:

```markdown
## v2.0.0 - 2017-12-31

### Added

...
```
