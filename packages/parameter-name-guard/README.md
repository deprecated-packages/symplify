# Package Builder

[![Downloads total](https://img.shields.io/packagist/dt/symplify/parameter-name-guard.svg?style=flat-square)](https://packagist.org/packages/symplify/parameter-name-guard/stats)

Prevent parameter typos that silently break your app.

## Install

```bash
composer require symplify/parameter-name-guard
```

## Use

### Prevent Parameter Typos

Was it `ignoreFiles`? Or `ignored_files`? Or `ignore_file`? Are you lazy to read every `README.md` to find out the correct name?
Make developers' live happy by helping them.

```yaml
# app/config/services.yaml
parameters:
    correctKey: 'value'

    # you need to get just this one right :D
    correct_to_typos:
        # correct key name
        correct_key:
            # the most common typos that people make
            - 'correctKey'

            # regexp also works!
            - '#correctKey(s)?#i'
```

This way user is informed on every typo he or she makes via exception:

```bash
Parameter "parameters > correctKey" does not exist.
Use "parameters > correct_key" instead.
```

They can focus less on remembering all the keys and more on programming.
