## DoctrineAnnotationNewlineInNestedAnnotationFixer

Nested object annotations should start on a standalone line

- `Symplify\CodingStandard\Fixer\Annotation\DoctrineAnnotationNewlineInNestedAnnotationFixer`

```diff
 use Doctrine\ORM\Mapping as ORM;
 /**
- * @ORM\Table(name="user", indexes={@ORM\Index(name="user_id", columns={"another_id"})})
+ * @ORM\Table(name="user", indexes={
+ * @ORM\Index(name="user_id", columns={"another_id"})
+ * })
  */
 class SomeEntity
 {
 }
```

<br>

## RemovePHPStormAnnotationFixer

Remove "Created by PhpStorm" annotations

- `Symplify\CodingStandard\Fixer\Annotation\RemovePHPStormAnnotationFixer`

```diff
-/**
- * Created by PhpStorm.
- * User: ...
- * Date: 17/10/17
- * Time: 8:50 AM
- */
 class SomeClass
 {
 }
```

<br>

## ArrayListItemNewlineFixer

Indexed PHP array item has to have one line per item

- `Symplify\CodingStandard\Fixer\ArrayNotation\ArrayListItemNewlineFixer`

```diff
-$value = ['simple' => 1, 'easy' => 2];
+$value = ['simple' => 1,
+'easy' => 2];
```

<br>

## ArrayOpenerAndCloserNewlineFixer

Indexed PHP array opener [ and closer ] must be on own line

- `Symplify\CodingStandard\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer`

```diff
-$items = [1 => 'Hey'];
+$items = [
+1 => 'Hey'
+];
```

<br>

## StandaloneLineInMultilineArrayFixer

Indexed arrays must have 1 item per line

- `Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer`

```diff
-$friends = [1 => 'Peter', 2 => 'Paul'];
+$friends = [
+    1 => 'Peter',
+    2 => 'Paul'
+];
```

<br>

## ParamReturnAndVarTagMalformsFixer

Fixes @param, @return, @var and inline @var annotations broken formats

- `Symplify\CodingStandard\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer`

```diff
 /**
- * @param string
+ * @param string $name
  */
 function getPerson($name)
 {
 }
```

<br>

## LineLengthFixer

Array items, method parameters, method call arguments, new arguments should be on same/standalone line to fit line length.

- `Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer`

```diff
-function some($veryLong, $superLong, $oneMoreTime)
-{
+function some(
+    $veryLong,
+    $superLong,
+    $oneMoreTime
+) {
 }

-function another(
-    $short,
-    $now
-) {
+function another($short, $now) {
 }
```

<br>

## StandardizeHereNowDocKeywordFixer

Use configured nowdoc and heredoc keyword

- `Symplify\CodingStandard\Fixer\Naming\StandardizeHereNowDocKeywordFixer`

```diff
-$value = <<<'WHATEVER'
+$value = <<<'CODE_SNIPPET'
 ...
-'WHATEVER'
+'CODE_SNIPPET'
```

<br>

## MethodChainingNewlineFixer

Each chain method call must be on own line

- `Symplify\CodingStandard\Fixer\Spacing\MethodChainingNewlineFixer`

```diff
-$someClass->firstCall()->secondCall();
+$someClass->firstCall()
+->secondCall();
```

<br>

## SpaceAfterCommaHereNowDocFixer

Add space after nowdoc and heredoc keyword, to prevent bugs on PHP 7.2 and lower, see https://laravel-news.com/flexible-heredoc-and-nowdoc-coming-to-php-7-3

- `Symplify\CodingStandard\Fixer\Spacing\SpaceAfterCommaHereNowDocFixer`

```diff
 $values = [
     <<<RECTIFY
 Some content
-RECTIFY,
+RECTIFY
+,
     1000
 ];
```

<br>

## BlankLineAfterStrictTypesFixer

Strict type declaration has to be followed by empty line

- `Symplify\CodingStandard\Fixer\Strict\BlankLineAfterStrictTypesFixer`

```diff
 declare(strict_types=1);
+
 namespace App;
```

<br>

## CommentedOutCodeSniff

There should be no commented code. Git is good enough for versioning

- `Symplify\CodingStandard\Sniffs\Debug\CommentedOutCodeSniff`

```php
// $one = 1;
// $two = 2;
// $three = 3;
```

:x:

```php
// note
```

:+1:

<br>
