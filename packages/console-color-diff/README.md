# Console Color Diff

[![Downloads total](https://img.shields.io/packagist/dt/symplify/console-color-diff.svg?style=flat-square)](https://packagist.org/packages/symplify/console-color-diff/stats)

## Install

```bash
composer require symplify/console-color-diff
```

Add to `config/bundles.php`:

```php
return [
    Symplify\ConsoleColorDiff\ConsoleColorDiffBundle::class => ['all' => true],
];
```

## Usage

```php
<?php

declare(strict_types=1);

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

    public function run()
    {
        // prints colored diff to the console output
        $this->consoleDiffer->diff('oldContent','newContent');
    }
}
```

## Contributing

Open an [issue](https://github.com/symplify/symplify/issues) or send a [pull-request](https://github.com/symplify/symplify/pulls) to main repository.
