# Coding Standard

[![Downloads](https://img.shields.io/packagist/dt/symplify/coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/coding-standard/stats)

Set of rules for PHP_CodeSniffer, PHP-CS-Fixer and PHPStan used by Symplify projects.

**They run best with [EasyCodingStandard](https://github.com/symplify/easy-coding-standard)** and **PHPStan**.

## Install

```bash
composer require symplify/coding-standard --dev
composer require symplify/easy-coding-standard --dev
```

1. Run with ECS:

```bash
vendor/bin/ecs process src --set symplify
```

2. Register rules for PHPStan:

```yaml
# phpstan.neon
includes:
    - vendor/symplify/coding-standard/config/symplify-rules.neon
```

## Rules Overview

- Jump to [Object Calisthenics rules](#object-calisthenics-rules)
- Rules with :wrench: are configurable.

<br>

### Cognitive Complexity for Method and Class Must be Less than X

- :wrench:
- [Why it's the best rule in your coding standard?](https://www.tomasvotruba.com/blog/2018/05/21/is-your-code-readable-by-humans-cognitive-complexity-tells-you/)

**For PHPStan**

- class: [`Symplify\CodingStandard\CognitiveComplexity\Rules\FunctionLikeCognitiveComplexityRule`](packages/coding-standard/packages/cognitive-complexity/src/Rules/FunctionLikeCognitiveComplexityRule.php)
- class: [`Symplify\CodingStandard\CognitiveComplexity\Rules\ClassLikeCognitiveComplexityRule`](packages/coding-standard/packages/cognitive-complexity/src/Rules/ClassLikeCognitiveComplexityRule.php)

```yaml
# phpstan.neon
includes:
    - vendor/symplify/coding-standard/packages/cognitive-complexity/config/cognitive-complexity-rules.neon

parameters:
    symplify:
        max_cognitive_complexity: 8 # default
        max_class_cognitive_complexity: 50 # default
```

:x:

```php
<?php

class SomeClass
{
    public function simple($value)
    {
        if ($value !== 1) {
            if ($value !== 2) {
                if ($value !== 3) {
                    return false;
                }
            }
        }

        return true;
    }
}
```

:+1:

```php
<?php

class SomeClass
{
    public function simple($value)
    {
        if ($value === 1) {
            return true;
        }

        if ($value === 2) {
            return true;
        }

        return $value === 3;
    }
}
```

<br>

### Classes with Static Methods must have "Static" in the Name

- class: [`Symplify\CodingStandard\Rules\NoClassWithStaticMethodWithoutStaticNameRule`](src/Rules/NoClassWithStaticMethodWithoutStaticNameRule.php)

- [Why is static bad?](https://tomasvotruba.com/blog/2019/04/01/removing-static-there-and-back-again/)
- be honest about static
- value object static constructor methods are excluded
- EventSubscriber and Command classes are excluded

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\NoClassWithStaticMethodWithoutStaticNameRule
```

:x:

```php
<?php

class FormatConverter
{
    public static function yamlToJson(array $yaml): array
    {
        // ...
    }
}
```

:+1:

```php
<?php

class StaticFormatConverter
{
    public static function yamlToJson(array $yaml): array
    {
        // ...
    }
}
```

<br>

### Remove Extra Spaces around Property and Constants Modifiers

- class: [`Symplify\CodingStandard\Fixer\Spacing\RemoveSpacingAroundModifierAndConstFixer`](packages/coding-standard/src/Fixer/Spacing/RemoveSpacingAroundModifierAndConstFixer.php)

```yaml
# ecs.yaml
services:
    Symplify\CodingStandard\Fixer\Spacing\RemoveSpacingAroundModifierAndConstFixer: null
```

```diff
 class SomeClass
 {
-    protected     static     $value;
+    protected static $value;
}
```

<br>

### Use Unique Class Short Names

- class: [`Symplify\CodingStandard\Rules\NoDuplicatedShortClassNameRule`](src/Rules/NoDuplicatedShortClassNameRule.php)

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\NoDuplicatedShortClassNameRule
```

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

<br>

### Make `@param`, `@return` and `@var` Format United

- class: [`Symplify\CodingStandard\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer`](src/Fixer/Commenting/ParamReturnAndVarTagMalformsFixer.php)

```yaml
# ecs.yaml
services:
    Symplify\CodingStandard\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer: null
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

### Order Private Methods by Their Use Order

- class: [`Symplify\CodingStandard\Fixer\Order\PrivateMethodOrderByUseFixer`](src/Fixer/Order/PrivateMethodOrderByUseFixer.php)

```yaml
# ecs.yml
services:
    Symplify\CodingStandard\Fixer\Order\PrivateMethodOrderByUseFixer: null
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
    Symplify\CodingStandard\Fixer\Order\PropertyOrderByComplexityFixer: null
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
- class: [`Symplify\CodingStandard\Rules\PreferredClassRule`](src/Rules/PreferredClassRule.php)

```yaml
# phpstan.neon
parameters:
    symplify:
        old_to_preffered_classes:
            DateTime: 'Nette\Utils\DateTime'

rules:
    - Symplify\CodingStandard\Rules\PreferredClassRule
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

### Strict types declaration has to be followed by empty line

- class: [`Symplify\CodingStandard\Fixer\Strict\BlankLineAfterStrictTypesFixer`](src/Fixer/Strict/BlankLineAfterStrictTypesFixer.php)

```diff
 <?php

declare(strict_types=1);
+
 namespace SomeNamespace;
```

<br>

### Use custom exceptions instead of Native Ones

- class: [`Symplify\CodingStandard\Rules\NoDefaultExceptionRule`](src/Rules/NoDefaultExceptionRule.php)

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\NoDefaultExceptionRule
```

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

### Class "%s" inherits from forbidden parent class "%s". Use composition over inheritance instead

- class: [`Symplify\CodingStandard\Rules\ForbiddenParentClassRule`](src/Rules/ForbiddenParentClassRule.php)

```yaml
# phpstan.neon
includes:
    - vendor/symplify/coding-standard/config/symplify-rules.neon

# phpstan.neon
parameters:
    symplify:
        forbidden_parent_classes:
            - 'Doctrine\ORM\EntityRepository'
            # you can use fnmatch() pattern
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

- class: [`Symplify\CodingStandard\Rules\NoReferenceRule`](src/Rules/NoReferenceRule.php)

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\NoReferenceRule
```

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

### There should not be comments with valid code

- class: [`Symplify\CodingStandard\Sniffs\Debug\CommentedOutCodeSniff`](src/Sniffs/Debug/CommentedOutCodeSniff.php)

:x:

```php
<?php

// $file = new File;
// $directory = new Diretory([$file]);
```

<br>

### Debug functions Cannot Be left in the Code

- class: [`Symplify\CodingStandard\Rules\NoDebugFuncCallRule`](src/Rules/NoDebugFuncCallRule.php)

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\NoDebugFuncCallRule
```

:x:

```php
<?php

d($value);
dd($value);
dump($value);
var_dump($value);
```

<br>

### Class should have suffix by parent class/interface

Covers `Interface` suffix as well, e.g `EventSubscriber` checks for `EventSubscriberInterface` as well.

- :wrench:
- class: [`Symplify\CodingStandard\Rules\ClassNameRespectsParentSuffixRule`](src/Rules/ClassNameRespectsParentSuffixRule.php)

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\ClassNameRespectsParentSuffixRule

parameters:
    symplify:
        parent_classes:
            - Rector
            - Rule
```

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

### Object Calisthenics rules

- From [Object Calisthenics](https://tomasvotruba.com/blog/2017/06/26/php-object-calisthenics-rules-made-simple-version-3-0-is-out-now/)
- [Original source for PHPStan rules](https://github.com/object-calisthenics/phpcs-calisthenics-rules/)

### No `else` And `elseif`

- class: [`Symplify\CodingStandard\ObjectCalisthenics\Rules\NoElseAndElseIfRule`](packages/object-calisthenics/src/Rules/NoElseAndElseIfRule.php)

```yaml
# phpstan.neon
rules:
     - Symplify\CodingStandard\ObjectCalisthenics\Rules\NoElseAndElseIfRule
```

:x:

```php
<?php

if ($value) {
    return 5;
} else {
    return 10;
}
```

:+1:

```php
if ($value) {
    return 5;
}

return 10;
```

<br>

### No Names Shorter than 3 Chars

- class: [`Symplify\CodingStandard\ObjectCalisthenics\Rules\NoShortNameRule`](packages/object-calisthenics/src/Rules/NoShortNameRule.php)

```yaml
# phpstan.neon
rules:
     - Symplify\CodingStandard\ObjectCalisthenics\Rules\NoShortNameRule
```

:x:

```php
<?php

class EM
{
}
```

:+1:

```php
<?php

class EntityManager
{
}
```

<br>

### No setter methods

- class: [`Symplify\CodingStandard\ObjectCalisthenics\Rules\NoSetterClassMethodRule`](packages/coding-standard/src/Rules/ObjectCalisthenics/NoSetterClassMethodRule.php)

```yaml
# phpstan.neon
rules:
     - Symplify\CodingStandard\ObjectCalisthenics\Rules\NoSetterClassMethodRule
```

:x:

```php
<?php

final class Person
{
    private string $name;

    public function setName(string $name)
    {
        $this->name = $name;
    }
}
```

:+1:

```php
final class Person
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
```

<br>

### No Chain Method Call

- class: [`Symplify\CodingStandard\ObjectCalisthenics\Rules\NoChainMethodCallRule`](packages/coding-standard/src/Rules/ObjectCalisthenics/NoChainMethodCallRule.php)
- Check [Fluent Interfaces are Evil](https://ocramius.github.io/blog/fluent-interfaces-are-evil/)

```yaml
# phpstan.neon
rules:
     - Symplify\CodingStandard\ObjectCalisthenics\Rules\NoChainMethodCallRule
```

:x:

```php
<?php

class SomeClass
{
    public function run()
    {
        return $this->create()->modify()->save();
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
        $object = $this->create();
        $object->modify();
        $object->save();

        return $object;
    }
}
```

<br>

## Contributing

Open an [issue](https://github.com/symplify/symplify/issues) or send a [pull-request](https://github.com/symplify/symplify/pulls) to main repository.
