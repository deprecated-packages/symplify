# Markdown Diff

[![Downloads total](https://img.shields.io/packagist/dt/symplify/markdown-diff.svg?style=flat-square)](https://packagist.org/packages/symplify/markdown-diff/stats)

## Install

```bash
composer require symplify/markdown-diff
```

Add to `config/config.php`:

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\MarkdownDiff\ValueObject\MarkdownDiffConfig;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(MarkdownDiffConfig::class);
};
```

## Usage

```php
namespace App;

use Symplify\MarkdownDiff\Differ\MarkdownDiffer;

final class SomeClass
{
    /**
     * @var MarkdownDiffer
     */
    private $markdownDiffer;

    public function __construct(MarkdownDiffer $markdownDiffer)
    {
        $this->markdownDiffer = $markdownDiffer;
    }

    public function run(): void
    {
        $markdownDiff = $this->markdownDiffer->diff('oldContent', 'newContent');
        // ...
    }
}
```

<br>

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [Symplify monorepo issue tracker](https://github.com/symplify/symplify/issues)

## Contribute

The sources of this package are contained in the Symplify monorepo. We welcome contributions for this package on [symplify/symplify](https://github.com/symplify/symplify).
