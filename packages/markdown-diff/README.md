# Markdown Diff

[![Downloads total](https://img.shields.io/packagist/dt/symplify/markdown-diff.svg?style=flat-square)](https://packagist.org/packages/symplify/markdown-diff/stats)

## Install

```bash
composer require symplify/markdown-diff
```

Add to `config/bundles.php`:

```php
<?php return [
    Symplify\MarkdownDiff\MarkdownDiffBundle::class => [
        'all' => true,
    ],
];
```

## Usage

```php
<?php namespace App;

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

## Contribute

The sources of this package are contained in the symplify monorepo. We welcome contributions for this package at [symplify/symplify](https://github.com/symplify/symplify).
