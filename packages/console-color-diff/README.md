# Console Color Diff

[![Downloads total](https://img.shields.io/packagist/dt/symplify/console-color-diff.svg?style=flat-square)](https://packagist.org/packages/symplify/console-color-diff/stats)

## Install

```bash
composer require symplify/console-color-diff
```

Add to `config/bundles.php`:

```php
return [
    Symplify\ConsoleColorDiff\Bundle\ConsoleColorDiffBundle::class => [
        'all' => true,
    ],
];
```

## Usage

```php
namespace App;

use Symplify\ConsoleColorDiff\Console\Output\ConsoleDiffer;

class SomeCommand
{
    /**
     * @var ConsoleDiffer
     */
    private $consoleDiffer;

    public function __construct(ConsoleDiffer $consoleDiffer)
    {
        $this->consoleDiffer = $consoleDiffer;
    }

    public function run(): void
    {
        // prints colored diff to the console output
        $this->consoleDiffer->diff('oldContent', 'newContent');
    }
}
```

<br>

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [Symplify monorepo issue tracker](https://github.com/symplify/symplify/issues)

## Contribute

The sources of this package are contained in the Symplify monorepo. We welcome contributions for this package on [symplify/symplify](https://github.com/symplify/symplify).
