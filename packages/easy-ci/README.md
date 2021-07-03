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

### 2. Provide `php-json` for Dynamic GitHub Actions Matrix

[Dynamic Matrix for GitHub Actions](https://tomasvotruba.com/blog/2020/11/16/how-to-make-dynamic-matrix-in-github-actions/) is one of cool way to simplify CI setup.

Instead of providing PHP versions manually one by one:

```yaml
        # ...
        strategy:
            matrix:
                php:
                    - 7.3
                    - 7.4
                    - 8.0
```

Use information from your `composer.json`:

```bash
vendor/bin/easy-ci php-versions-json
# "[7.3, 7.4, 8.0]"
```

Use in GitHub Action Workflow like this:

```yaml
jobs:
    provide_php_versions_json:
        runs-on: ubuntu-latest

        steps:
            # git clone + use PHP + composer install
            -   uses: actions/checkout@v2
            -   uses: shivammathur/setup-php@v2
                with:
                    php-version: 8.0

            -   uses: "ramsey/composer-install@v1"

            # to see the output
            -   run: vendor/bin/easy-ci php-versions-json

            # here we create the json, we need the "id:" so we can use it in "outputs" bellow
            -
                id: output_data
                run: echo "::set-output name=matrix::$(vendor/bin/easy-ci php-versions-json)"

        # here, we save the result of this 1st phase to the "outputs"
        outputs:
            matrix: ${{ steps.output_data.outputs.matrix }}

    unit_tests:
        needs: provide_php_versions_json
        strategy:
            fail-fast: false
            matrix:
                php: ${{ fromJson(needs.provide_php_versions_json.outputs.matrix) }}

        # ...
```

<br>

### 3. Check Configs for Non-Existing Classes

```bash
vendor/bin/easy-ci check-config src
```

Supported types are YAML and NEON.

<br>

### 4. Check Templates for Non-Existing Classes

```bash
vendor/bin/easy-ci check-latte-template templates
```

<br>

### 5. Check Twig Controller Paths

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

### 6. Avoid Static Calls in Latte Templates and use FilterProvider Instead

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

### 7. Detect Static Calls in Your Code

```bash
vendor/bin/static-detector detect-static src
```

<br>

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [Symplify monorepo issue tracker](https://github.com/symplify/symplify/issues)

## Contribute

The sources of this package are contained in the Symplify monorepo. We welcome contributions for this package on [symplify/symplify](https://github.com/symplify/symplify).
