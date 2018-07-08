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
    authors_to_ignore: ['TomasVotruba'] # usually core maintainers; to make external contributors more credit

    # this is detected from "git origin", but you can change it
    repository_url: 'https://github.com/symplify/symplify'
```

The config is autodiscovered in the root directory or by `--config` option.

## A. Dump Merges

```bash
vendor/bin/changelog-linker dump-mergers
```

### Write or Dry-run?

First thing you need to know, it has a `--dry-run` option that only prints the result to the output.

Without that, I looks for `<!-- changelog-linker -->` in the `CHANGELOG.md` to replace with the content.

This command finds the last #ID in the `CHANGELOG.md`, than looks on Github via API and dumps all the merged PRs since the last #ID in nice format.

But that is a mash-up of everything. Not very nice:

```markdown
- [#868] [ChangelogLinker] Add ChangeTree to manage merge messages
- [#867] [ChangelogLinker] Change Worker registration from implicit to explicit
- [#865] Improve Code Complexity
- [#864] [MonorepoBuilder] improve coverage
```

What if we'd have *Added*, *Changed*... and all that standard categories?

```
vendor/bin/changelog-linker dump-merges --in-categories
```

Nice, now everything is nicely grouped:

```markdown
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
vendor/bin/changelog-linker dump-merges --in-packages --in-categories
```

Finally what we needed:

```markdown
### TokenRunner

#### Changed

- [#863] [TokenRunner] anonymous class now returns null on name [fixes #855]
```

Or do you prefer it the other way?

```
vendor/bin/changelog-linker dump-merges --in-packages --in-categories
```

Yes you can:

```markdown
### Fixed

#### EasyCodingStandard

- [#848] [ECS] Fix single file processing
```

### Github API Overload?

In case you cross the API rate limit and get denied, create [new Github Token](https://github.com/settings/tokens) and pass it via `--token` option.

```
vendor/bin/changelog-linker dump-merges --token super-secret-token
```

### Tags Included

Tags are the most important when dealing with changelogs. Let's include them!

```
vendor/bin/changelog-linker dump-merges --in-tags
```

```markdown
## v4.4.1 - 2018-06-07

- [#853] Add test case for #777
```

**Combination with `--in-packages` and `--in-categories`** is allowed and recommended:

```
vendor/bin/changelog-linker dump-merges --in-tags --in-categories --in-packages
```

Will result into this beautiful:

```markdown
## v4.4.0 - 2018-06-03

### Added

#### EasyCodingStandard

- [#852] Add support for line_ending configuration
```

And with `--linkify` option, you'll get all the nice things from B as well.

## B. Decorate `CHANGELOG.md`

```bash
vendor/bin/changelog-linker linkify
```

All these feature can be turned on by adding particular worker to `changelog-linker.yml`:

```yaml
# changelog-linker.yml
services:
    Symplify\ChangelogLinker\Worker\LinksToReferencesWorker:
```

Do you want them all? Just include `config/basic.yml` config.

### 1. Link PR and Issues

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

### 4. Add Links to Specific Words

- `Symplify\ChangelogLinker\Worker\LinkifyWorker`

In Symplify, I need that every `EasyCodingStandard` word leads to `https://github.com/Symplify/EasyCodingStandard/`.

```yaml
parameters:
    names_to_urls:
        EasyCodingStandard: 'https://github.com/Symplify/EasyCodingStandard/'
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
