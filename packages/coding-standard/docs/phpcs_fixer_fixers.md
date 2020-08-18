# PHP CS Fixer Fixers

## Strict Types Declaration has to be Followed by Empty Line

- class: [`BlankLineAfterStrictTypesFixer`](src/Fixer/Strict/BlankLineAfterStrictTypesFixer.php)

```php
<?php

// ecs.php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(Symplify\CodingStandard\Fixer\Strict\BlankLineAfterStrictTypesFixer::class);
};
```

```diff
 <?php

declare(strict_types=1);
+
 namespace SomeNamespace;
```

<br>

## Parameters, Arguments and Array items should be on the same/standalone line to fit Line Length

- class: [`LineLengthFixer`](src/Fixer/LineLength/LineLengthFixer.php)

```php
<?php

// ecs.php

declare(strict_types=1);

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
````

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

- class: [`RemoveSuperfluousDocBlockWhitespaceFixer`](src/Fixer/Commenting/RemoveSuperfluousDocBlockWhitespaceFixer.php)

```php
<?php

// ecs.php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\Commenting\RemoveSuperfluousDocBlockWhitespaceFixer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(RemoveSuperfluousDocBlockWhitespaceFixer::class);
};
```

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

- class: [`StandaloneLineInMultilineArrayFixer`](src/Fixer/ArrayNotation/StandaloneLineInMultilineArrayFixer.php)

```php
<?php

// ecs.php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer::class);
};
```

```diff
-$friends = [1 => 'Peter', 2 => 'Paul'];
+$friends = [
+    1 => 'Peter',
+    2 => 'Paul'
+];
```

<br>

## Make `@param`, `@return` and `@var` Format United

- class: [`ParamReturnAndVarTagMalformsFixer`](src/Fixer/Commenting/ParamReturnAndVarTagMalformsFixer.php)

```php
<?php

// ecs.php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(Symplify\CodingStandard\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer::class);
};
```

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

- class: [`Symplify\CodingStandard\Fixer\Spacing\RemoveSpacingAroundModifierAndConstFixer`](packages/coding-standard/src/Fixer/Spacing/RemoveSpacingAroundModifierAndConstFixer.php)

```php
<?php

// ecs.php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(Symplify\CodingStandard\Fixer\Spacing\RemoveSpacingAroundModifierAndConstFixer::class);
};
```

```diff
 class SomeClass
 {
-    protected     static     $value;
+    protected static $value;
}
```

<br>

## Indent nested Annotations to Newline

- class: [`Symplify\CodingStandard\Fixer\Annotation\NewlineInNestedAnnotationFixer`](packages/coding-standard/src/Fixer/Annotation/NewlineInNestedAnnotationFixer.php)

```php
<?php

// ecs.php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(Symplify\CodingStandard\Fixer\Annotation\NewlineInNestedAnnotationFixer::class);
};
```

```diff
 use Doctrine\ORM\Mapping as ORM;

 /**
- * @ORM\Table(name="user", indexes={@ORM\Index(name="user_id", columns={"another_id"})})
+ * @ORM\Table(name="user", indexes={
+ *     @ORM\Index(name="user_id", columns={"another_id"})
+ * })
  */
 class SomeEntity
 {
 }
```

<br>
