# 1 Rules Overview

## ComposerJsonAwareRector

Some change

- class: [`Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Fixture\Rector\ComposerJsonAware\ComposerJsonAwareRector`](packages/rule-doc-generator/tests/DirectoryToMarkdownPrinter/Fixture/Rector/ComposerJsonAware/ComposerJsonAwareRector.php)

- with `composer.json`:

```json
{
    "name": "some-project"
}
```

↓

```diff
-before
+after
```

<br>
