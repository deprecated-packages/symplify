# PHAR Compiler for MonorepoBuilder

## Compile the PHAR

```bash
composer install
bin/compile
```

The compiled PHAR will be in `tmp/monorepo-builder.phar`. Test it:

```bash
php ../tmp/monorepo-builder.phar
```

Please note that running the compiler will change the contents of `composer.json` file and `vendor` directory. Revert those changes after running it.

## Notes

This section is needed in `composer.json`, because it was causing autolaoding bugs.
Box aliases existing Symfony stubs to php, see https://ayesh.me/composer-replace-polyfills.

```json
{
    "replace": {
        "symfony/polyfill-php70": "*"
    }
}
```
