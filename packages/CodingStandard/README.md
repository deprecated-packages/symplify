# Coding Standard

[![Build Status](https://img.shields.io/travis/Symplify/CodingStandard/master.svg?style=flat-square)](https://travis-ci.org/Symplify/CodingStandard)
[![Downloads](https://img.shields.io/packagist/dt/symplify/coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/coding-standard/stats)
[![Subscribe](https://img.shields.io/badge/subscribe-to--releases-green.svg?style=flat-square)](https://libraries.io/packagist/symplify%2Fcoding-standard)

Set of PHP_CodeSniffer Sniffs and PHP-CS-Fixer Fixers used by Symplify projects.

**They run best with [EasyCodingStandard](https://github.com/Symplify/EasyCodingStandard)**.

## Install

```bash
composer require symplify/coding-standard --dev
```

## Rules Overview

- Rules with :wrench: are configurable.

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

### Block comment should only contain useful information about types

- :wrench:
- class: [`Symplify\CodingStandard\Fixer\Commenting\RemoveUselessDocBlockFixer`](src/Fixer/Commenting/RemoveUselessDocBlockFixer.php)

```diff
 /**
- * @param int $value
- * @param $anotherValue
- * @param SomeType $someService
- * @return array
  */
 public function setCount(int $value, $anotherValue, SomeType $someService): array
 {
 }
```

This checker keeps 'mixed' and 'object' and other types by default. But if you need, you can **configure it**:

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\Commenting\RemoveUselessDocBlockFixer:
        useless_types: ['mixed', 'object'] # [] by default
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
 <?php declare(strict_types=1);
+
 namespace SomeNamespace;
```

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

### Block comment should be used instead of one liner

- class: [`Symplify\CodingStandard\Fixer\Commenting\BlockPropertyCommentFixer`](src/Fixer/Commenting/BlockPropertyCommentFixer.php)

```diff
 class SomeClass
 {
-    /** @var int */
+    /**
+     * @var int
+     */
     public $count;
 }
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

### Prefer `sprintf()` over multiple concats ( . ).

- :wrench:
- class: [`Symplify\CodingStandard\Sniffs\ControlStructure\SprintfOverContactSniff`](src/Sniffs/ControlStructure/SprintfOverContactSniff.php)

:x:

```php
<?php

return 'Class ' . $oldClass . ' was removed from ' . $file . '. Use ' . self::class . " instead';
```

:+1:

```php
<?php

return sprintf('Class "%s" was removed from "%s". Use "%s" instead', $oldClass, $file, self::class);
```

Is 2 `.` too strict? Just configure it:

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Sniffs\ControlStructure\SprintfOverContactSniff:
        maxConcatCount: 4 # "3" by default
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

### Use service and constructor injection rather than instantiation with new

- :wrench:
- class: [`Symplify\CodingStandard\Sniffs\DependencyInjection\NoClassInstantiationSniff`](src/Sniffs/DependencyInjection/NoClassInstantiationSniff.php)

:x:

```php
<?php

class SomeController
{
   public function renderEdit(array $data)
   {
        $database = new Database;
        $database->save($data);
   }
}
```

:+1:

```php
<?php

class SomeController
{
   public function renderEdit(array $data)
   {
        $this->database->save($data);
   }
}
```

This checkers ignores by default some classes, see `$allowedClasses` property.

In case want to exclude more classes, you can **configure it** with class or pattern using [`fnmatch`](http://php.net/manual/en/function.fnmatch.php):

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\DependencyInjection\NoClassInstantiationSniff:
        extraAllowedClasses:
            - 'PhpParser\Node\*'
```

Doctrine entities are skipped as well. You can disable that by:

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\DependencyInjection\NoClassInstantiationSniff:
        includeEntities: true
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

## Contributing

Open an [issue](https://github.com/Symplify/Symplify/issues) or send a [pull-request](https://github.com/Symplify/Symplify/pulls) to main repository.
