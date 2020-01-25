# Smart File System

## Install

```bash
composer require symplify/smart-file-system
```

## Use

### Does `SplFileInfo` exist?

The `SplFileInfo::getRealPath()` method returns absolute path to the file... [or `FALSE`, if the file doesn't exist](https://www.php.net/manual/en/splfileinfo.getrealpath.php). This default PHP behavior forces you to **check all `getRealPath()` calls**:

```php
<?php

$fileInfo = new SplFileInfo('non_existing_file.txt');

if ($fileInfo->getRealPath() === false) {
	// damn, the files doesn't exist
	// throw exception or whatever
    // everytime!
}

$fileRealPath = $fileInfo->getRealPath();
```

While this has a reason - e.g. to be sure the file was not deleted since the construction,
we actually have to call the method to find out the file was removed. Another annoyance is to tell this to static analyzers.

In reality, **it's very rare to work with file that was existing a while ago, but not is gone, without us doing it on purpose**. We usually use `SplFileInfo` to modify files or work with their paths.

**What if:**

- we could remove this problem and make sure **`getRealPath()` method always returns string**?
- get **an exception of non-existing file on SplFileInfo creation**?

### Introducing `SmartFileInfo`

```php
<?php

$fileInfo = new Symplify\SmartFileSystem\SmartFileInfo('non_existing_file.txt');
// throws Symplify\SmartFileSystem\Exception\FileNotFoundException
```

This class also bring new useful methods:

```php
<?php

// current directory (cwd()) is "/var/www"
$smartFileInfo = new Symplify\SmartFileSystem\SmartFileInfo('/var/www/src/ExistingFile.php');

echo $smartFileInfo->getBasenameWithoutSuffix();
// "ExistingFile"

echo $smartFileInfo->getRelativeFilePath();
// "src/ExistingFile.php"

echo $smartFileInfo->getRelativeDirectoryPath();
// "src"

echo $smartFileInfo->getRelativeFilePathFromDirectory('/var');
// "www/src/ExistingFile.php"
```

**It also fixes WTF behavior** of `Symfony\Component\Finder\SplFileInfo`. Which one? When you run e.g. `vendor/bin/ecs check src` and use `Finder`, the `getRelativeFilePath()` in Symfony now returns all the relative paths to `src`. Which is useless, mainly with multiple dirs like: `vendor/bin/ecs check src tests` both containing file `Post.php`.

```php
<?php

$smartFileInfo = new Symplify\SmartFileSystem\SmartFileInfo('/var/www/src/Post.php');

echo $smartFileInfo->getRelativeFilePathFromCwd();
// "src/Post.php"
```

### File name Matching

Last but not least, matching a file comes useful in excluding files (typical for tools like ECS, PHPStan, Psalm, Rector, PHP CS Fixer or PHP_CodeSniffer):

```php
<?php

$smartFileInfo = new Symplify\SmartFileSystem\SmartFileInfo('/var/www/src/PostRepository.php');

echo $smartFileInfo->endsWith('Repository.php');
// true

echo $smartFileInfo->doesFnmatch('*Repo*');
// true
```

### Sanitizer various files to `SmartFileInfo[]`

Do you have multiple file inputs that can mix-up?

```php
<?php

$files = [new SplFileInfo('someFile.php')];

$files = [new Symfony\Component\Finder\SplFileInfo('someFile.php', 'someFile', '')];

// or
$files = (new Symfony\Component\Finder\Finder)->files();

// or
$files = Nette\Utils\Finder::findFiles('*');

// or
$files = ['someFile.php'];
```

Later, you wan to actually work with the files:

```php
<?php

foreach ($files as $file) {
    // what methods do we have here
    // what kind of object?
    // is it even object or a string?
    $file->...
}
```

Use sanitized files, that **have united format you can rely on**:

```php
<?php

$finderSanitizer = new \Symplify\SmartFileSystem\Finder\FinderSanitizer();
$smartFileInfos = $finderSanitizer->sanitize($files);

var_dump($smartFileInfos); // always array of Symplify\SmartFileSystem\SmartFileInfo
```
