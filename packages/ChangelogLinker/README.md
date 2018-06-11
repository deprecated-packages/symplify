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

Create `changelog-linker.yml` with configuration:

```yaml
# changelog-linker.yml:
parameters:
    authors_to_ignore: ["TomasVotruba"] # usually core maintainers; to make external contributors more credit
    repository_url: "https://github.com/symplify/symplify"
    repository_name: "symplify/symplify"
```

The config is autodiscovered in the root directory or by `--config` option.

## A. Dump Merges

```bash
vendor/bin/changelog-linker dump-mergers
```

This command finds the last #ID in the `CHANGELOG.md`, than looks on Github via API and dumps all the merged PRs since the last #ID in nice format.

But that is a mash-up of everything. Not very nice:

```
- [#868] [ChangelogLinker] Add ChangeTree to manage merge messages
- [#867] [ChangelogLinker] Change Worker registration from implicit to explicit
- [#865] Improve Code Complexity
- [#864] [MonorepoBuilder] improve coverage
```

What if we'd have *Added*, *Changed*... and all that standard categories?

```
vendor/bin/changelog-linker dump:merges --in-categories
```

Nice, now everything is nicely grouped:

```
### Added

- [#828] [ChangelogLinker] Add Unreleased to last tagged version feature
- [#840] [ChangelogLinker] Add LinkifyWorker
```

*(Technical secret: it reacts to *add* and few other keywords.)*

But what about monorepo packages - can we have list grouped by each of them?

```php
### CodingStandard

- [#851] [CodingStandard] Add _ support to PropertyNameMatchingTypeFixer
- [#860] [CS] Add test case for #855, Thanks to @OndraM
```

*(Technical secret: it reacts to *[Package]* in PR title.)*

But that's kind of useless without combination, right? Let's join them together:

```
vendor/bin/changelog-linker dump:merges --in-packages --in-categories
```

Finally what we needed:

```
### TokenRunner

#### Changed

- [#863] [TokenRunner] anonymous class now returns null on name [fixes #855]
```

Or do you prefer it the other way?

```
vendor/bin/changelog-linker dump:merges --in-packages --in-categories
```

Yes you can:

```
### Fixed

#### EasyCodingStandard

- [#848] [ECS] Fix single file processing
```

## B. Decorate `CHANGELOG.md`

```bash
vendor/bin/changelog-linker run
```

All these feature can be turned on by adding particular worker to `changelog-linker.yml`:

```yaml
# changelog-linker.yml
services:
    Symplify\ChangelogLinker\Worker\LinksToReferencesWorker:
```

Do you want them all? Just include `config/basic.yml` config.

### 1. Link PR, Issues and Commits

- `Symplify\ChangelogLinker\Worker\LinksToReferencesWorker`

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

### 2. Link Versions to Diffs

- `Symplify\ChangelogLinker\Worker\DiffLinksToVersionsWorker`

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

### 3. Link Thanks to Users

- `Symplify\ChangelogLinker\Worker\UserReferencesWorker`

Credit is much more valuable with link to follow:

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

### 4. Turn "Unreleased" to The Last Release

- `Symplify\ChangelogLinker\Worker\ReleaseReferencesWorker`

Includes the version and the date. Executes on every new tag, that is not already added in `CHANGELOG.md`.

:x:

```markdown
## Unreleased

### Added

...
```

:+1:

```markdown
## v2.0.0 - 2017-12-31

### Added

...
```

### 5. Add Links to Specific Words

- `Symplify\ChangelogLinker\Worker\LinkifyWorker`

In Symplify, I need that every `EasyCodingStandard` word leads to `https://github.com/Symplify/EasyCodingStandard/`.

```yaml
parameters:
    linkify:
        EasyCodingStandard: "https://github.com/Symplify/EasyCodingStandard/"
```

:x:

```markdown
## Unreleased

### Added

#### EasyCodingStandard

...
```

:+1:

```markdown
## Unreleased

### Added

#### [EasyCodingStandard]

...

[EasyCodingStandard]: https://github.com/Symplify/EasyCodingStandard/
```
