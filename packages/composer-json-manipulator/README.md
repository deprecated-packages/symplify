# Manipulate composer.json with Beautiful Object API

[![Downloads total](https://img.shields.io/packagist/dt/symplify/composer-json-manipulator.svg?style=flat-square)](https://packagist.org/packages/symplify/composer-json-manipulator/stats)

- load to `composer.json` to an object
- use handful methods
- merge it with others
- print it back to `composer.json` in human-like format

## Install

```bash
composer require symplify/composer-json-manipulator
```

Add to `config/bundles.php`:

```php
<?php return [
    Symplify\ComposerJsonManipulator\ComposerJsonManipulatorBundle::class => [
        'all' => true,
    ],
];
```

## Usage

```php
<?php namespace App;

use Symplify\ComposerJsonManipulator\ComposerJsonFactory;

class SomeClass
{
    /**
     * @var ComposerJsonFactory
     */
    private $composerJsonFactory;

    public function __construct(ComposerJsonFactory $composerJsonFactory)
    {
        $this->composerJsonFactory = $composerJsonFactory;
    }

    public function run(): void
    {
        // ↓ instance of \Symplify\ComposerJsonManipulator\ValueObject\ComposerJson
        $composerJson = $this->composerJsonFactory->createFromFilePath(getcwd() . '/composer.json');
        // ...
    }
}
```

## Contribute

The sources of this package are contained in the symplify monorepo. We welcome contributions for this package at [symplify/symplify](https://github.com/symplify/symplify).
