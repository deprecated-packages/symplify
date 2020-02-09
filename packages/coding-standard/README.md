# Coding Standard

[![Downloads](https://img.shields.io/packagist/dt/symplify/coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/coding-standard/stats)

Set of PHP_CodeSniffer Sniffs and PHP-CS-Fixer Fixers used by Symplify projects.

**They run best with [EasyCodingStandard](https://github.com/symplify/easy-coding-standard)**.

## Install

```bash
composer require symplify/coding-standard --dev
```

## Rules Overview

- Rules with :wrench: are configurable.

<br>

### Make sure That `@param`, `@var`, `@return` and `@throw` Types Exist

- class: [`Symplify\CodingStandard\Sniffs\Commenting\AnnotationTypeExistsSniff`](src/Sniffs/Commenting/AnnotationTypeExistsSniff.php)

```yaml
services:
    Symplify\CodingStandard\Sniffs\Commenting\AnnotationTypeExistsSniff: ~
```

:x:

```php
<?php

class SomeClass
{
    /**
     * @var NonExistingClass
     */
    private $property;
}
```

:+1:

```php
<?php

class SomeClass
{
    /**
     * @var ExistingClass
     */
    private $property;
}
```

<br>

### Use Unique Class Short Names

- :wrench:
- class: [`Symplify\CodingStandard\Sniffs\Architecture\DuplicatedClassShortNameSniff`](/src/Sniffs/Architecture/DuplicatedClassShortNameSniff.php)

:x:

```php
<?php

namespace App;

class Finder
{
}
```

```php
<?php

namespace App\Entity;

class Finder
{
}
```

:+1:

```diff
 <?php

 namespace App\Entity;

-class Finder
+class EntityFinder
 {
 }
```

Do you want skip some classes? Configure it:

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Sniffs\Architecture\DuplicatedClassShortNameSniff:
        allowed_class_names:
            - 'Request'
            - 'Response'
```

<br>

### Make `@param`, `@return` and `@var` Format United

- class: [`Symplify\CodingStandard\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer`](src/Fixer/Commenting/ParamReturnAndVarTagMalformsFixer.php)

```yaml
services:
    Symplify\CodingStandard\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer: ~
```

```diff
 <?php

 /**
- * @param $name string
+ * @param string $name
- * @return int $value
+ * @return int
  */
 function someFunction($name)
 {
 }

 class SomeClass
 {
     /**
-     * @var int $property
+     * @var int
      */
     private $property;
 }

-/* @var int $value */
+/** @var int $value */
 $value = 5;

-/** @var $value int */
+/** @var int $value */
 $value = 5;
```

<br>

### Remove // end of ... Legacy Comments

- class: [`\Symplify\CodingStandard\Fixer\Commenting\RemoveEndOfFunctionCommentFixer`](src/Fixer/Commenting/RemoveEndOfFunctionCommentFixer.php)

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\Commenting\RemoveEndOfFunctionCommentFixer: ~
```

```diff
 <?php

 function someFunction()
 {

-} // end of someFunction
+}
```

<br>

### Order Private Methods by Their Use Order

- class: [`Symplify\CodingStandard\Fixer\Order\PrivateMethodOrderByUseFixer`](src/Fixer/Order/PrivateMethodOrderByUseFixer.php)

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\Order\PrivateMethodOrderByUseFixer: ~
```

:x:

```php
<?php

class SomeClass
{
    public function run()
    {
        $this->call1();
        $this->call2();
    }

    private function call2()
    {
    }

    private function call1()
    {
    }
}
```

:+1:

```php
<?php

class SomeClass
{
    public function run()
    {
        $this->call1();
        $this->call2();
    }

    private function call1()
    {
    }

    private function call2()
    {
    }
}
```

<br>

### Order Properties From Simple to Complex

Properties are ordered by visibility first, then by complexity.

- class: [`Symplify\CodingStandard\Fixer\Order\PropertyOrderByComplexityFixer`](src/Fixer/Order/PropertyOrderByComplexityFixer.php)

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\Order\PropertyOrderByComplexityFixer: ~
```

:x:

```php
<?php

final class SomeFixer
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Type
     */
    private $service;

    /**
     * @var int
     */
    private $price;
}
```

:+1:

```php
<?php

final class SomeFixer
{
    /**
     * @var int
     */
    private $price;

    /**
     * @var string
     */
    private $name;

    /**
     * @var Type
     */
    private $service;
}
```

<br>

### Prefer Another Class

- :wrench:
- class: [`Symplify\CodingStandard\Sniffs\Architecture\PreferredClassSniff`](src/Sniffs/Architecture/PreferredClassSniff.php)

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Sniffs\Architecture\PreferredClassSniff:
        oldToPreferredClasses:
            DateTime: 'Nette\Utils\DateTime'
```

:x:

```php
<?php

$dateTime = new DateTime('now');
```

:+1:

```php
<?php

$dateTime = new Nette\Utils\DateTime('now');
```

<br>

### Indexed PHP arrays should have 1 item per line

- class: [`Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer`](src/Fixer/ArrayNotation/StandaloneLineInMultilineArrayFixer.php)

```diff
-$friends = [1 => 'Peter', 2 => 'Paul'];
+$friends = [
+    1 => 'Peter',
+    2 => 'Paul'
+];
```

<br>

### There should not be empty PHPDoc blocks

Just like `PhpCsFixer\Fixer\Phpdoc\NoEmptyPhpdocFixer`, but this one removes all doc block lines.

- class: [`Symplify\CodingStandard\Fixer\Commenting\RemoveEmptyDocBlockFixer`](src/Fixer/Commenting/RemoveEmptyDocBlockFixer.php)

```diff
-/**
- */
 public function someMethod()
 {
 }
```

<br>

### Block comment should not have 2 empty lines in a row

- class: [`Symplify\CodingStandard\Fixer\Commenting\RemoveSuperfluousDocBlockWhitespaceFixer`](src/Fixer/Commenting/RemoveSuperfluousDocBlockWhitespaceFixer.php)

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

### Consistent Delimiter in Regular Expression

- :wrench:
- class: [`Symplify\CodingStandard\Fixer\ControlStructure\PregDelimiterFixer`](src/Fixer/ControlStructure/PregDelimiterFixer.php)

```diff
-preg_match('~pattern~', $value);
+preg_match('#pattern#', $value);

-preg_match("~pattern~d", $value);
+preg_match("#pattern#d", $value);

-Nette\Utils\Strings::match($value, '/pattern/');
+Nette\Utils\Strings::match($value, '#pattern#');
```

Do you want another char than `#`? Configure it:

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\ControlStructure\PregDelimiterFixer:
        delimiter: '_' # default "#"
```

<br>

### Include/Require should be followed by absolute path

- class: [`Symplify\CodingStandard\Fixer\ControlStructure\RequireFollowedByAbsolutePathFixer`](src/Fixer/ControlStructure/RequireFollowedByAbsolutePathFixer.php)

```diff
-require 'vendor/autoload.php';
+require __DIR__.'/vendor/autoload.php';
```

<br>

### Parameters, arguments and array items should be on the same/standalone line to fit line length

- :wrench:
- class: [`Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer`](src/Fixer/LineLength/LineLengthFixer.php)

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

- Are 120 characters too long for you?
- Do you want to break longs lines but not inline short lines or vice versa?

**Change it**:

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer:
        max_line_length: 100 # default: 120
        break_long_lines: true # default: true
        inline_short_lines: false # default: true
```

<br>

### Property name should match its key, if possible

- :wrench:
- class: [`Symplify\CodingStandard\Fixer\Naming\PropertyNameMatchingTypeFixer`](src/Fixer/Naming/PropertyNameMatchingTypeFixer.php)

```diff
-public function __construct(EntityManagerInterface $eventManager)
+public function __construct(EntityManagerInterface $entityManager)
 {
-    $this->eventManager = $eventManager;
+    $this->entityManager = $entityManager;
 }
```

This checker ignores few **system classes like `std*` or `Spl*` by default**. In case want to skip more classes, you can **configure it**:

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\Naming\PropertyNameMatchingTypeFixer:
        extra_skipped_classes:
            - 'MyApp*' # accepts anything like fnmatch
```

<br>

### Exception name should match its type, if possible

- class: [`Symplify\CodingStandard\Fixer\Naming\CatchExceptionNameMatchingTypeFixer`](src/Fixer/Naming/CatchExceptionNameMatchingTypeFixer.php)

```diff
 try {
-} catch (SomeException $typoException) {
+} catch (SomeException $someException) {
-    $typeException->getMessage();
+    $someException->getMessage();
 }
```

<br>

### Public Methods Should have Specific Order by Interface/Parent Class

- :wrench:
- class: [`Symplify\CodingStandard\Fixer\Order\MethodOrderByTypeFixer`](src/Fixer/Order/MethodOrderByTypeFixer.php)

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\Order\MethodOrderByTypeFixer:
        method_order_by_type:
            Rector\Contract\Rector\PhpRectorInterface:
                - 'getNodeTypes'
                - 'refactor'
```

↓

```diff
 final class SomeRector implements PhpRectorInterface
 {
-    public function refactor()
+    public function getNodeTypes()
     {
-        // refactoring
+        return ['SomeType'];
     }
-
-    public function getNodeTypes()
+    public function refactor(): void
     {
-        return ['SomeType'];
+        // refactoring
     }
 }
```

<br>

### `::class` references should be used over string for classes and interfaces

- :wrench:
- class: [`Symplify\CodingStandard\Fixer\Php\ClassStringToClassConstantFixer`](src/Fixer/Php/ClassStringToClassConstantFixer.php)

```diff
-$className = 'DateTime';
+$className = DateTime::class;
```

This checker takes **only existing classes by default**. In case want to check another code not loaded by local composer, you can **configure it**:

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\Php\ClassStringToClassConstantFixer:
        class_must_exist: false # true by default
```

Do you want to allow some classes to be in string format?

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\Php\ClassStringToClassConstantFixer:
        allow_classes:
            - 'SomeClass'
```

<br>

### Array property should have default value, to prevent undefined array issues

- class: [`Symplify\CodingStandard\Fixer\Property\ArrayPropertyDefaultValueFixer`](src/Fixer/Property/ArrayPropertyDefaultValueFixer.php)

```diff
 class SomeClass
 {
     /**
      * @var string[]
      */
-    public $apples;
+    public $apples = [];

     public function run()
     {
         foreach ($this->apples as $mac) {
             // ...
         }
     }
 }
```

<br>

### Strict types declaration has to be followed by empty line

- class: [`Symplify\CodingStandard\Fixer\Strict\BlankLineAfterStrictTypesFixer`](src/Fixer/Strict/BlankLineAfterStrictTypesFixer.php)

```diff
 <?php

declare(strict_types=1);
+
 namespace SomeNamespace;
```

<br>

### Non-abstract class that implements interface should be final

*Except for Doctrine entities, they cannot be final.*

- :wrench:
- class: [`Symplify\CodingStandard\Fixer\Solid\FinalInterfaceFixer`](src/Fixer/Solid/FinalInterfaceFixer.php)

```diff
-class SomeClass implements SomeInterface
+final class SomeClass implements SomeInterface
 {
 }
```

In case want check this only for specific interfaces, you can **configure them**:

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\Solid\FinalInterfaceFixer:
        only_interfaces:
            - 'Symfony\Component\EventDispatcher\EventSubscriberInterface'
            - 'Nette\Application\IPresenter'
```

<br>

### Use explicit and informative exception names over generic ones

- class: [`Symplify\CodingStandard\Sniffs\Architecture\ExplicitExceptionSniff`](src/Sniffs/Architecture/ExplicitExceptionSniff.php)

:x:

```php
<?php

throw new RuntimeException('...');
```

:+1:

```php
<?php

throw new FileNotFoundException('...');
```

<br>

### Class "X" cannot be parent class. Use composition over inheritance instead.

- class: [`Symplify\CodingStandard\Sniffs\CleanCode\ForbiddenParentClassSniff`](src/Sniffs/CleanCode/ForbiddenParentClassSniff.php)

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Sniffs\CleanCode\ForbiddenParentClassSniff:
        forbiddenParentClasses:
            - 'Doctrine\ORM\EntityRepository'
            # again, you can use fnmatch() pattern
            - '*\AbstractController'
```

:x:

```php
<?php

use Doctrine\ORM\EntityRepository;

final class ProductRepository extends EntityRepository
{
}
```

:+1:

```php
<?php

use Doctrine\ORM\EntityRepository;

final class ProductRepository
{
    /**
     * @var EntityRepository
     */
    private $entityRepository;

    public function __construct(EntityRepository $entityRepository)
    {
        $this->entityRepository = $entityRepository;
    }
}
```

<br>

### Use explicit return values over magic "&$variable" reference

- class: [`Symplify\CodingStandard\Sniffs\CleanCode\ForbiddenReferenceSniff`](src/Sniffs/CleanCode/ForbiddenReferenceSniff.php)

:x:

```php
<?php

function someFunction(&$var)
{
    $var + 1;
}
```

:+1:

```php
<?php

function someFunction($var)
{
    return $var + 1;
}
```

<br>

### Use services and constructor injection over static method

- class: [`Symplify\CodingStandard\Sniffs\CleanCode\ForbiddenStaticFunctionSniff`](src/Sniffs/CleanCode/ForbiddenStaticFunctionSniff.php)

:x:

```php
<?php

class SomeClass
{
    public static function someFunction()
    {
    }
}
```

:+1:

```php
<?php

class SomeClass
{
    public function someFunction()
    {
    }
}
```

<br>

### Constant should have docblock comment

- class: [`Symplify\CodingStandard\Sniffs\Commenting\VarConstantCommentSniff`](src/Sniffs/Commenting/VarConstantCommentSniff.php)

```php
class SomeClass
{
    private const EMPATH_LEVEL = 55;
}
```

:+1:

```php
<?php

class SomeClass
{
    /**
     * @var int
     */
    private const EMPATH_LEVEL = 55;
}
```

<br>

### Use per line assign instead of multiple ones

- class: [`Symplify\CodingStandard\Sniffs\ControlStructure\ForbiddenDoubleAssignSniff`](src/Sniffs/ControlStructure/ForbiddenDoubleAssignSniff.php)

:x:

```php
<?php

$value = $anotherValue = [];
```

:+1:

```php
<?php

$value = [];
$anotherValue = [];
```

<br>

### There should not be comments with valid code

- class: [`Symplify\CodingStandard\Sniffs\Debug\CommentedOutCodeSniff`](src/Sniffs/Debug/CommentedOutCodeSniff.php)

:x:

```php
<?php

// $file = new File;
// $directory = new Diretory([$file]);
```

<br>

### Debug functions should not be left in the code

- class: [`Symplify\CodingStandard\Sniffs\Debug\DebugFunctionCallSniff`](src/Sniffs/Debug/DebugFunctionCallSniff.php)

:x:

```php
<?php

d($value);
dd($value);
dump($value);
var_dump($value);
```

<br>

### Abstract class should have prefix "Abstract"

- class: [`Symplify\CodingStandard\Sniffs\Naming\AbstractClassNameSniff`](src/Sniffs/Naming/AbstractClassNameSniff.php)

:x:

```php
<?php

abstract class SomeClass
{
}
```

:+1:

```php
<?php

abstract class AbstractSomeClass
{
}
```

<br>

### Class should have suffix by parent class/interface

- :wrench:
- class: [`Symplify\CodingStandard\Sniffs\Naming\ClassNameSuffixByParentSniff`](src/Sniffs/Naming/ClassNameSuffixByParentSniff.php)

:x:

```php
<?php

class Some extends Command
{
}
```

:+1:

```php
<?php

class SomeCommand extends Command
{
}
```

This checker check few names by default. But if you need, you can **configure it**:

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Sniffs\Naming\ClassNameSuffixByParentSniff:
        parentTypesToSuffixes:
            # defaults
            - 'Command'
            - 'Controller'
            - 'Repository'
            - 'Presenter'
            - 'Request'
            - 'Response'
            - 'EventSubscriber'
            - 'FixerInterface'
            - 'Sniff'
            - 'Exception'
            - 'Handler'
```

Or keep all defaults values by using `extra_parent_types_to_suffixes`:

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Sniffs\Naming\ClassNameSuffixByParentSniff:
        extraParentTypesToSuffixes:
            - 'ProviderInterface'
```

It also covers `Interface` suffix as well, e.g `EventSubscriber` checks for `EventSubscriberInterface` as well.

<br>

### Interface should have suffix "Interface"

- class: [`Symplify\CodingStandard\Sniffs\Naming\InterfaceNameSniff`](src/Sniffs/Naming/InterfaceNameSniff.php)

:x:

```php
<?php

interface Some
{
}
```

:+1:

```php
<?php

interface SomeInterface
{
}
```

<br>

### Trait should have suffix "Trait"

- class: [`Symplify\CodingStandard\Sniffs\Naming\TraitNameSniff`](src/Sniffs/Naming/TraitNameSniff.php)

:x:

```php
<?php

trait Some
{
}
```

:+1:

```php
<?php

trait SomeTrait
{
}
```

<br>

## Brave Checkers

### Possible Unused Public Method

- :wrench:
- class: [`Symplify\CodingStandard\Sniffs\DeadCode\UnusedPublicMethodSniff`](src/Sniffs/DeadCode/UnusedPublicMethodSniff.php)
- **Requires ECS due *double run* feature**, use with `--clear-cache` so all files are included.

:x:

```php
<?php

class SomeClass
{
    public function usedMethod()
    {
    }

    public function unusedMethod()
    {
    }
}

$someObject = new SomeClass;
$someObject->usedMethod();
```

:+1:

```php
<?php

class SomeClass
{
    public function usedMethod()
    {
    }
}

$someObject = new SomeClass;
$someObject->usedMethod();
```

Do you have public methods used only byr 3rd party? **Just skip them**:

```yaml
# ecs.yaml
services:
    Symplify\CodingStandard\Sniffs\DeadCode\UnusedPublicMethodSniff:
        allow_classes:
            - 'Symplify\Autodiscovery\Discovery'
            - 'Symplify\FlexLoader\Flex\FlexLoader'
```

## Contributing

Open an [issue](https://github.com/symplify/symplify/issues) or send a [pull-request](https://github.com/symplify/symplify/pulls) to main repository.
