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
return [
    Symplify\ComposerJsonManipulator\ComposerJsonManipulatorBundle::class => [
        'all' => true,
    ],
];
```

## Usage

```php
namespace App;

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

<br>

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [Symplify monorepo issue tracker](https://github.com/symplify/symplify/issues)

## Contribute

The sources of this package are contained in the Symplify monorepo. We welcome contributions for this package on [symplify/symplify](https://github.com/symplify/symplify).
