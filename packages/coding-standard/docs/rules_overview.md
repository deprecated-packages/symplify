# 16 Rules Overview

## ArrayListItemNewlineFixer

Indexed PHP array item has to have one line per item

- class: [`Symplify\CodingStandard\Fixer\ArrayNotation\ArrayListItemNewlineFixer`](/packages/coding-standard/src/Fixer/ArrayNotation/ArrayListItemNewlineFixer.php)

```diff
-$value = ['simple' => 1, 'easy' => 2];
+$value = ['simple' => 1,
+'easy' => 2];
```

<br>

## ArrayOpenerAndCloserNewlineFixer

Indexed PHP array opener [ and closer ] must be on own line

- class: [`Symplify\CodingStandard\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer`](/packages/coding-standard/src/Fixer/ArrayNotation/ArrayOpenerAndCloserNewlineFixer.php)

```diff
-$items = [1 => 'Hey'];
+$items = [
+1 => 'Hey'
+];
```

<br>

## BlankLineAfterStrictTypesFixer

Strict type declaration has to be followed by empty line

- class: [`Symplify\CodingStandard\Fixer\Strict\BlankLineAfterStrictTypesFixer`](/packages/coding-standard/src/Fixer/Strict/BlankLineAfterStrictTypesFixer.php)

```diff
 declare(strict_types=1);
+
 namespace App;
```

<br>

## DocBlockLineLengthFixer

Docblock lenght should fit expected width

:wrench: **configure it!**

- class: [`Symplify\CodingStandard\Fixer\LineLength\DocBlockLineLengthFixer`](/packages/coding-standard/src/Fixer/LineLength/DocBlockLineLengthFixer.php)

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\LineLength\DocBlockLineLengthFixer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(DocBlockLineLengthFixer::class)
        ->call('configure', [[
            DocBlockLineLengthFixer::LINE_LENGTH => 40,
        ]]);
};
```

↓

```diff
 /**
- * Super long doc block description
+ * Super long doc
+ * block description
  */
 function some()
 {
 }
```

<br>

## DoctrineAnnotationNewlineInNestedAnnotationFixer

Nested object annotations should start on a standalone line

- class: [`Symplify\CodingStandard\Fixer\Annotation\DoctrineAnnotationNewlineInNestedAnnotationFixer`](/packages/coding-standard/src/Fixer/Annotation/DoctrineAnnotationNewlineInNestedAnnotationFixer.php)

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

## LineLengthFixer

Array items, method parameters, method call arguments, new arguments should be on same/standalone line to fit line length.

:wrench: **configure it!**

- class: [`Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer`](/packages/coding-standard/src/Fixer/LineLength/LineLengthFixer.php)

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(LineLengthFixer::class)
        ->call('configure', [[
            LineLengthFixer::LINE_LENGTH => 40,
        ]]);
};
```

↓

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

## MethodChainingNewlineFixer

`Each` chain method call must be on own line

- class: [`Symplify\CodingStandard\Fixer\Spacing\MethodChainingNewlineFixer`](/packages/coding-standard/src/Fixer/Spacing/MethodChainingNewlineFixer.php)

```diff
-$someClass->firstCall()->secondCall();
+$someClass->firstCall()
+->secondCall();
```

<br>

## NewlineServiceDefinitionConfigFixer

Add newline for a fluent call on service definition in Symfony config

- class: [`Symplify\CodingStandard\Fixer\Spacing\NewlineServiceDefinitionConfigFixer`](/packages/coding-standard/src/Fixer/Spacing/NewlineServiceDefinitionConfigFixer.php)

```diff
 use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
 use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;

 return static function (ContainerConfigurator $containerConfigurator): void {
     $services = $containerConfigurator->services();
-    $services->set(LineLengthFixer::class)->call('configure', [['values']]);
+    $services->set(LineLengthFixer::class)
+        ->call('configure', [['values']]);
 };
```

<br>

## ParamReturnAndVarTagMalformsFixer

Fixes @param, @return, `@var` and inline `@var` annotations broken formats

- class: [`Symplify\CodingStandard\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer`](/packages/coding-standard/src/Fixer/Commenting/ParamReturnAndVarTagMalformsFixer.php)

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

## RemoveCommentedCodeFixer

Remove commented code like "// `$one` = 1000;"

- class: [`Symplify\CodingStandard\Fixer\Commenting\RemoveCommentedCodeFixer`](/packages/coding-standard/src/Fixer/Commenting/RemoveCommentedCodeFixer.php)

```diff
-// $one = 1;
-// $two = 2;
-// $three = 3;
```

<br>

## RemovePHPStormAnnotationFixer

Remove "Created by PhpStorm" annotations

- class: [`Symplify\CodingStandard\Fixer\Annotation\RemovePHPStormAnnotationFixer`](/packages/coding-standard/src/Fixer/Annotation/RemovePHPStormAnnotationFixer.php)

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

## RemoveUselessDefaultCommentFixer

Remove useless PHPStorm-generated `@todo` comments, redundant "Class XY" or "gets service" comments etc.

- class: [`Symplify\CodingStandard\Fixer\Commenting\RemoveUselessDefaultCommentFixer`](/packages/coding-standard/src/Fixer/Commenting/RemoveUselessDefaultCommentFixer.php)

```diff
-/**
- * class SomeClass
- */
 class SomeClass
 {
-    /**
-     * SomeClass Constructor.
-     */
     public function __construct()
     {
-        // TODO: Change the autogenerated stub
-        // TODO: Implement whatever() method.
     }
 }
```

<br>

## SpaceAfterCommaHereNowDocFixer

Add space after nowdoc and heredoc keyword, to prevent bugs on PHP 7.2 and lower, see https://laravel-news.com/flexible-heredoc-and-nowdoc-coming-to-php-7-3

- class: [`Symplify\CodingStandard\Fixer\Spacing\SpaceAfterCommaHereNowDocFixer`](/packages/coding-standard/src/Fixer/Spacing/SpaceAfterCommaHereNowDocFixer.php)

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

## StandaloneLineInMultilineArrayFixer

Indexed arrays must have 1 item per line

- class: [`Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer`](/packages/coding-standard/src/Fixer/ArrayNotation/StandaloneLineInMultilineArrayFixer.php)

```diff
-$friends = [1 => 'Peter', 2 => 'Paul'];
+$friends = [
+    1 => 'Peter',
+    2 => 'Paul'
+];
```

<br>

## StandaloneLinePromotedPropertyFixer

Promoted property should be on standalone line

- class: [`Symplify\CodingStandard\Fixer\Spacing\StandaloneLinePromotedPropertyFixer`](/packages/coding-standard/src/Fixer/Spacing/StandaloneLinePromotedPropertyFixer.php)

```diff
 final class PromotedProperties
 {
-    public function __construct(public int $age, private string $name)
-    {
+    public function __construct(
+        public int $age,
+        private string $name
+    ) {
     }
 }
```

<br>

## StandardizeHereNowDocKeywordFixer

Use configured nowdoc and heredoc keyword

:wrench: **configure it!**

- class: [`Symplify\CodingStandard\Fixer\Naming\StandardizeHereNowDocKeywordFixer`](/packages/coding-standard/src/Fixer/Naming/StandardizeHereNowDocKeywordFixer.php)

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\Naming\StandardizeHereNowDocKeywordFixer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(StandardizeHereNowDocKeywordFixer::class)
        ->call('configure', [[
            StandardizeHereNowDocKeywordFixer::KEYWORD => 'CODE_SNIPPET',
        ]]);
};
```

↓

```diff
-$value = <<<'WHATEVER'
+$value = <<<'CODE_SNIPPET'
 ...
-'WHATEVER'
+'CODE_SNIPPET'
```

<br>
