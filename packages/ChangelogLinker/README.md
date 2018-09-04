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

The `--dry-run` option prints the result to the output. Without that, I looks for `<!-- changelog-linker -->` in the `CHANGELOG.md` to replace with the content.

It finds the last #ID in the `CHANGELOG.md`, than looks on Github via API and dumps all the merged PRs since the last #ID in nice format. In case you want to **specify minimal PR id yourself**, use this:

```bash
vendor/bin/changelog-linker dump-mergers --since-id 125
```

But that is a mash-up of everything. Not very nice:

```markdown
## Unreleased

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
## Unreleased

### Added

- [#828] [ChangelogLinker] Add Unreleased to last tagged version feature
- [#840] [ChangelogLinker] Add LinkifyWorker
```

*(Technical secret: it reacts to *add* and few other keywords.)*

But what about monorepo packages - can we have list grouped by each of them?

```markdown
## Unreleased

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
## Unreleased

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
## Unreleased

### Fixed

#### EasyCodingStandard

- [#848] [ECS] Fix single file processing
```

### Github API Overload?

In case you cross the API rate limit and get denied, create [new Github Token](https://github.com/settings/tokens) and pass it via `--token` option.

```
vendor/bin/changelog-linker dump-merges --token super-secret-token
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

[#852]: https://github.com/symplify/symplify/pull/852
```

You'll get the output with links to PRs and "thanks" by default.

## B. Decorate `CHANGELOG.md`

```bash
vendor/bin/changelog-linker linkify
```

### 1. Link PR and Issues

- `Symplify\ChangelogLinker\Worker\LinksToReferencesWorker`

```diff
 ### Added

-- #123 Cool new without detailed description wanting me to see PR, [closes #234]
+- [#123] Cool new without detailed description wanting me to see PR, [closes [#234]]
+
+[#123]: https://github.com/Symplify/Symplify/pull/123
+[#234]: https://github.com/Symplify/Symplify/pull/234
```

### 2. Link Versions to Diffs

```diff
-## v2.0.0 - 2017-12-31
+## [v2.0.0] - 2017-12-31

 - ...

 ## v1.5.0 - 2017-06-30
+
+[v2.0.0]: https://github.com/Symplify/Symplify/compare/v1.5.0...v2.0.0
```

### 3. Link Thanks to Users

Credit is much more valuable with link to follow:

```diff
 ### [v2.0.0] - 2017-12-31

-- ... thanks @SpacePossum for help
+- ... thanks [@SpacePossum] for help
+
+[@SpacePossum]: https://github.com/SpacePossum
```

### 4. Add Links to Specific Words

In Symplify, I need that every `EasyCodingStandard` word leads to `https://github.com/Symplify/EasyCodingStandard/`.

```yaml
parameters:
    names_to_urls:
        EasyCodingStandard: 'https://github.com/Symplify/EasyCodingStandard/'
```

```diff
 ## Unreleased

 ### Added

-#### EasyCodingStandard
+#### [EasyCodingStandard]
+
+[EasyCodingStandard]: https://github.com/Symplify/EasyCodingStandard/
```
