# Vendor Patches

[![Downloads total](https://img.shields.io/packagist/dt/symplify/vendor-patches.svg?style=flat-square)](https://packagist.org/packages/symplify/vendor-patches/stats)

Generate vendor patches for packages with single command.

## Install

```bash
composer require symplify/vendor-patches --dev
```

## Usage

How to create [a patch for a file in `/vendor`](https://tomasvotruba.com/blog/2020/07/02/how-to-patch-package-in-vendor-yet-allow-its-updates/)?

### 1. Create a Copy of `/vendor` file you Want To Change with `*.old` Suffix

For example, if you edit:

```bash
vendor/nette/di/src/DI/Extensions/InjectExtension.php
# copy of the file
vendor/nette/di/src/DI/Extensions/InjectExtension.php.old
```

### 2. Open the original file and change the lines you need:

```diff
 			if (DI\Helpers::parseAnnotation($rp, 'inject') !== null) {
-				if ($type = DI\Helpers::parseAnnotation($rp, 'var')) {
+				if ($type = \Amateri\Reflection\Helper\StaticReflectionHelper::getPropertyType($rp)) {
+				} elseif ($type = DI\Helpers::parseAnnotation($rp, 'var')) {
 					$type = Reflection::expandClassName($type, Reflection::getPropertyDeclaringClass($rp));
```

Only `*.php` file is loaded, not the `*.php.old` one. This way you can **be sure the new code** is working before you generate patches.

### 3. Run `generate` command ~~for every single file changed this way~~ once for all files 🎆

```bash
vendor/bin/vendor-patches generate
```

This tool will generate patch files for all files created this way in `/patches` directory:

```bash
/patches/nette-di-di-extensions-injectextension.php.patch
```

The patch path is based on original file path, so **the patch name is always unique**.

Also, it will add configuration for `cweagans/composer-patches` to your `composer.json`:

```json
{
    "extra": {
        "patches": {
            "nette/di": [
                "patches/nette_di_di_extensions_injectextension.patch"
            ]
        }
    }
}
```

That's it!

Now all you need to do is run composer:

```bash
composer install
```

And your patches are applied to your code!

<br>

If not, get more information from composer to find out why:

```bash
composer install --verbose
```

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [migrify monorepo issue tracker](https://github.com/migrify/migrify/issues)

## Contribute

The sources of this package are contained in the migrify monorepo. We welcome contributions for this package on [migrify/migrify](https://github.com/migrify/migrify).
