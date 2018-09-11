# PHPStan Extensions

[![Build Status](https://img.shields.io/travis/Symplify/PHPStanExtensions/master.svg?style=flat-square)](https://travis-ci.org/Symplify/PHPStanExtensions)
[![Downloads total](https://img.shields.io/packagist/dt/symplify/phpstan-extensions.svg?style=flat-square)](https://packagist.org/packages/symplify/phpstan-extensions)
[![Subscribe](https://img.shields.io/badge/subscribe-to--releases-green.svg?style=flat-square)](https://libraries.io/packagist/symplify%2Fphpstan-extensions)

## Install

```yaml
# phpstan.neon
includes:
    - 'packages/PHPStanExtensions/src/config/config.neon'
```

## 1. Stats Formatter - the Best Way to Start with PHPStan

Do you have zillion errors in you project? That's common... and frustrating. Why not start with the most wide-spread errors? **Solve one type of problem to get rid of dozens of errors**.

Run:

```bash
vendor/bin/phpstan analyse src --level max --error-format stats
```

to get this nice overview of **top 5 errors**:

```bash
These are 5 most frequent errors
================================

2 x - "Parameter #1 $filePath of method Symplify\EasyCodingStandard\ChangedFilesDetector\FileHashComputer::compute() expects string, string|false given."
---------------------------------------------------------------------------------------------------------------------------------------------------------

 * packages/EasyCodingStandard/packages/ChangedFilesDetector/src/ChangedFilesDetector.php:50
 * packages/EasyCodingStandard/packages/ChangedFilesDetector/src/ChangedFilesDetector.php:62

2 x - "Parameter #2 $absoluteFilePath of method Symplify\EasyCodingStandard\Skipper::shouldSkipCodeAndFile() expects string, string|false given."
-------------------------------------------------------------------------------------------------------------------------------------------------

 * packages/EasyCodingStandard/packages/SniffRunner/src/File/File.php:132
 * packages/EasyCodingStandard/packages/SniffRunner/src/File/File.php:145
```

## 2. Ignore Formatter

Do you need to ignore few errors but don't want to play with regex? Run:

```bash
vendor/bin/phpstan analyse src --level max --error-format ignore
```

to get it on silver plate, ready for copy-paste:

```bash

Add to "parameters > ignoreErrors" section in "phpstan.neon"
============================================================

# phpstan.neon
parameters:
    ignoreErrors:
        - '#Parameter \#1 \$errors of method Symplify\\PHPStan\\Error\\ErrorGrouper\:\:groupErrorsToMessagesToFrequency\(\) expects array<Symplify\\EasyCodingStandard\\Error\\Error\>, array<PHPStan\\Analyser\\Error\> given#' # found 2x
```
