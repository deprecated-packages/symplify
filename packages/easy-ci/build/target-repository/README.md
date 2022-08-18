# Easy CI

[![Downloads total](https://img.shields.io/packagist/dt/symplify/easy-ci.svg?style=flat-square)](https://packagist.org/packages/symplify/easy-ci/stats)

Tools that make easy to setup CI.

- Check git conflicts in CI
- Check TWIG and Latte templates for missing classes, non-existing static calls and constant fetches
- Check YAML and NEON configs for the same
- Extract Latte filters from static calls in templates

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

Supported types are YAML and NEON.

<br>

### 3. Check Templates for Non-Existing Classes

```bash
vendor/bin/easy-ci check-latte-template templates
```

<br>

### 4. Check Twig Controller Paths

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

### 5. Avoid Static Calls in Latte Templates and use FilterProvider Instead

Static calls in Latte templates [are a code smell](https://tomasvotruba.com/blog/2020/08/17/how-to-get-rid-of-magic-static-and-chaos-from-latte-filters). Make your code more decoupled and use a Latte filter instead:

```diff
 # any latte file
-{\App\SomeClass::someStaticMethod($value)}
+{$value|someStaticMethod}
```

Filter provider can look like this:

```php
use App\Contract\Latte\FilterProviderInterface;
use App\SomeClass;

final class SomeMethodFilterProvider implements FilterProviderInterface
{
    public function __invoke(string $name): int
    {
        return SomeClass::someStaticMethod($name);
    }

    public function getName(): string
    {
        return 'someMethod';
    }
}
```

<br>

### 6. Detect Static Calls in Your Code

```bash
vendor/bin/easy-ci detect-static src
```

<br>

### 7. Detect Commented Code

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

### 8. Short File === Class Name

Does short file name matches the class name?

```bash
vendor/bin/easy-ci check-file-class-name src
```

### 9. Avoid 2 classes in 1 File

What files have 2 and more classes?

```bash
vendor/bin/easy-ci find-multi-classes tests
```

<br>

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [Symplify monorepo issue tracker](https://github.com/symplify/symplify/issues)

## Contribute

The sources of this package are contained in the Symplify monorepo. We welcome contributions for this package on [symplify/symplify](https://github.com/symplify/symplify).
