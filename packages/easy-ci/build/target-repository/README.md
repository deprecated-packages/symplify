# Easy CI

[![Downloads total](https://img.shields.io/packagist/dt/symplify/easy-ci.svg?style=flat-square)](https://packagist.org/packages/symplify/easy-ci/stats)

Tools that make easy to setup CI.

- Check git conflicts in CI
- Check TWIG templates for missing classes, non-existing static calls and constant fetches
- Check YAML configs for the same

## Install

```bash
composer require symplify/easy-ci --dev
```

## Usage

### 1. Check your Code for Git Merge Conflicts

Do you use Git? Then merge conflicts is not what you want in your code ever to see:

```bash
<<<<<<< HEAD
this is some content to mess with
content to append
=======
totally different content to merge later
````

How to avoid it? Add check to your CI:

```bash
vendor/bin/easy-ci check-conflicts .
```

The `/vendor` directory is excluded by default.

<br>

### 2. Check Configs for Non-Existing Classes

```bash
vendor/bin/easy-ci check-config src
```

Supported types are YAML.

<br>

### 3. Check Twig Controller Paths

```bash
vendor/bin/easy-ci check-twig-render src/Controller
```

```php
final class SomeController
{
    public function index()
    {
        return $this->render('does_path_exist.twig');
    }
}
```

<br>

### 4. Detect Static Calls in Your Code

```bash
vendor/bin/easy-ci detect-static src
```

<br>

### 5. Detect Commented Code

Have you ever forgot commented code in your code?

```php
//      foreach ($matches as $match) {
//           $content = str_replace($match[0], $match[2], $content);
//      }
```

Clutter no more! Add `check-commented-code` command to your CI and don't worry about it:

```bash
vendor/bin/easy-ci check-commented-code <directory>
vendor/bin/easy-ci check-commented-code packages --line-limit 5
```

### 6. Short File === Class Name

Does short file name matches the class name?

```bash
vendor/bin/easy-ci check-file-class-name src
```

### 7. Avoid 2 classes in 1 File

What files have 2 and more classes?

```bash
vendor/bin/easy-ci find-multi-classes tests
```

<br>

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [Symplify monorepo issue tracker](https://github.com/symplify/symplify/issues)

## Contribute

The sources of this package are contained in the Symplify monorepo. We welcome contributions for this package on [symplify/symplify](https://github.com/symplify/symplify).
