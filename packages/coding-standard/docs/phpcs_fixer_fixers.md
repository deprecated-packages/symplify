# 10+ PHP CS Fixer Fixers

## Make Newline on Chain Method Call

- class: [`MethodChainingNewlineFixer`](../src/Fixer/Spacing/MethodChainingNewlineFixer.php)

```diff
 class SkipMultiLineChain
 {
     public function run()
     {
-        return $this->one()->two();
+        return $this->one()
+->two();
     }

     public function one()
     {
         return $this;
     }

     public function two()
     {
         return $this;
     }
 }
```

<br>

## Keep Array List Items on separated lines

- class: [`ArrayListItemNewlineFixer`](../src/Fixer/ArrayNotation/ArrayListItemNewlineFixer.php)

```diff
 final class SimpleTwoItems
 {
     public function run()
     {
-        $value = ['simple' => 1, 'easy' => 2];
+        $value = ['simple' => 1,
+'easy' => 2];
     }
}
```

<br>

## Remove "Created by PHPStorm" Fixers

- class: [`RemovePHPStormAnnotationFixer`](../src/Fixer/Naming/RemovePHPStormAnnotationFixer.php)

```diff
-/**
- * Created by PhpStorm.
- * User: ...
- * Date: 17/10/17
- * Time: 8:50 AM
- */

 final class SimpleAnnotation
 {
 }
```

<br>

## Add Space After here/now doc To make Compatible with PHP 7.2

- class: [`SpaceAfterCommaHereNowDocFixer`](../src/Fixer/Naming/SpaceAfterCommaHereNowDocFixer.php)
- see [Flexible Heredoc and Nowdoc Coming to PHP 7.3](https://laravel-news.com/flexible-heredoc-and-nowdoc-coming-to-php-7-3)

```diff
 class SomeClass
 {
     public function run()
     {
         $values = [<<<'RECTIFY'
 Some code
 Text
-RECTIFY,
+RECTIFY
+,
            1000];
     }
 }
```

<br>

## Use Configured nowdoc and heredoc keyword

- class: [`StandardizeHereNowDocKeywordFixer`](../src/Fixer/Naming/StandardizeHereNowDocKeywordFixer.php)

```php
// ecs.php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\Naming\StandardizeHereNowDocKeywordFixer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(StandardizeHereNowDocKeywordFixer::class)
        ->call('configure', [[
            // default: CODE_SAMPLE
            StandardizeHereNowDocKeywordFixer::KEYWORD => 'SAMPLE',
        ]]);
};
```

```diff
 class SomeClass
 {
-    public const SOME_EXAMPLE = <<<'RECTIFY'
+    public const SOME_EXAMPLE = <<<'SAMPLE'
 Some code
 Text
-RECTIFY;
+SAMPLE;
}
```

<br>

## Add Newline Before and After array opener with Keys

- class: [`ArrayOpenerAndCloserNewlineFixer`](../src/Fixer/ArrayNotation/ArrayOpenerAndCloserNewlineFixer.php)

```diff
-$items = [$item => 1,
-    $item => 2];
+$items = [
+    $item,
+     $item2
+];
```

<br>

## Add Doctrine Annotations

- class: [`DoctrineAnnotationNewlineInNestedAnnotationFixer`](../src/Fixer/Annotation/DoctrineAnnotationNewlineInNestedAnnotationFixer.php)

```diff
 /**
- * @ORM\Table(name="table_name", indexes={@ORM\Index(name="...", columns={"..."}), @ORM\Index(name="...", columns={"..."})})
+ * @ORM\Table(name="table_name", indexes={
+ *     @ORM\Index(name="...", columns={"..."}),
+ *     @ORM\Index(name="...", columns={"..."})
+ * })
  */
class SomeEntity
{
}
```

The left side indent is handled by teaming up with `DoctrineAnnotationIndentationFixer`.

<br>

## Strict Types Declaration has to be Followed by Empty Line

- class: [`BlankLineAfterStrictTypesFixer`](../src/Fixer/Strict/BlankLineAfterStrictTypesFixer.php)

```diff
 <?php

 declare(strict_types=1);
+
 namespace SomeNamespace;
```

<br>

## Parameters, Arguments and Array items should be on the same/standalone line to fit Line Length

- class: [`LineLengthFixer`](../src/Fixer/LineLength/LineLengthFixer.php)

```php
// ecs.php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(LineLengthFixer::class)
        ->call('configure', [[
            LineLengthFixer::LINE_LENGTH => 120,
            LineLengthFixer::BREAK_LONG_LINES => true,
            LineLengthFixer::INLINE_SHORT_LINES => true,
        ]]);
};
```

```diff
 class SomeClass
 {
-    public function someMethod(SuperLongArguments $superLongArguments, AnotherLongArguments $anotherLongArguments, $oneMore)
+    public function someMethod(
+        SuperLongArguments $superLongArguments,
+        AnotherLongArguments $anotherLongArguments,
+        $oneMore
+    )
     {
     }

-    public function someOtherMethod(
-        ShortArgument $shortArgument,
-        $oneMore
-    ) {
+    public function someOtherMethod(ShortArgument $shortArgument, $oneMore) {
     }
 }
```

<br>

## Block comment should not have 2 empty lines in a row

- class: [`RemoveSuperfluousDocBlockWhitespaceFixer`](../src/Fixer/Commenting/RemoveSuperfluousDocBlockWhitespaceFixer.php)

```diff
 /**
  * @param int $value
  *
- *
  * @return array
  */
 public function setCount($value)
 {
 }
```

<br>

## Indexed PHP arrays should have 1 item per line

- class: [`StandaloneLineInMultilineArrayFixer`](../src/Fixer/ArrayNotation/StandaloneLineInMultilineArrayFixer.php)

```diff
-$friends = [1 => 'Peter', 2 => 'Paul'];
+$friends = [
+    1 => 'Peter',
+    2 => 'Paul'
+];
```

<br>

## Make `@param`, `@return` and `@var` Format United

- class: [`ParamReturnAndVarTagMalformsFixer`](../src/Fixer/Commenting/ParamReturnAndVarTagMalformsFixer.php)

```diff
 <?php

 /**
- * @param $name string
+ * @param string $name
  *
- * @return int $value
+ * @return int
  */
 function someFunction($name)
 {
 }
```

```diff
 <?php

 class SomeClass
 {
     /**
-     * @var int $property
+     * @var int
      */
     private $property;
 }
```

```diff
-/* @var int $value */
+/** @var int $value */
 $value = 5;

-/** @var $value int */
+/** @var int $value */
 $value = 5;
```

<br>

## Remove Extra Spaces around Property and Constants Modifiers

- class: [`RemoveSpacingAroundModifierAndConstFixer`](packages/coding-standard/src/Fixer/Spacing/RemoveSpacingAroundModifierAndConstFixer.php)

```diff
 class SomeClass
 {
-    protected     static     $value;
+    protected static $value;
}
```
