# Coding Standard

[![Downloads](https://img.shields.io/packagist/dt/symplify/coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/coding-standard/stats)

Set of rules for PHP_CodeSniffer, PHP-CS-Fixer and PHPStan used by Symplify projects.

**They run best with [EasyCodingStandard](https://github.com/symplify/easy-coding-standard)** and **PHPStan**.

## Install

```bash
composer require symplify/coding-standard --dev
composer require symplify/easy-coding-standard --dev
```

1. Run with [ECS](https://github.com/symplify/easy-coding-standard):

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

<br>

### Cognitive Complexity for Method and Class Must be Less than X

- [Why it's the best rule in your coding standard?](https://www.tomasvotruba.com/blog/2018/05/21/is-your-code-readable-by-humans-cognitive-complexity-tells-you/)

**For PHPStan**

- class: [`FunctionLikeCognitiveComplexityRule`](packages/coding-standard/packages/cognitive-complexity/src/Rules/FunctionLikeCognitiveComplexityRule.php)
- class: [`ClassLikeCognitiveComplexityRule`](packages/coding-standard/packages/cognitive-complexity/src/Rules/ClassLikeCognitiveComplexityRule.php)

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

Be honest about static. [Why is static bad?](https://tomasvotruba.com/blog/2019/04/01/removing-static-there-and-back-again/)

Value object static constructors, EventSubscriber and Command classe are excluded.

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

- class: [`ParamReturnAndVarTagMalformsFixer`](src/Fixer/Commenting/ParamReturnAndVarTagMalformsFixer.php)

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

### Prefer Another Class

- class: [`PreferredClassRule`](src/Rules/PreferredClassRule.php)

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

- class: [`StandaloneLineInMultilineArrayFixer`](src/Fixer/ArrayNotation/StandaloneLineInMultilineArrayFixer.php)

```yaml
# ecs.yaml
services:
    Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer: null
```

```diff
-$friends = [1 => 'Peter', 2 => 'Paul'];
+$friends = [
+    1 => 'Peter',
+    2 => 'Paul'
+];
```

<br>

### Block comment should not have 2 empty lines in a row

- class: [`RemoveSuperfluousDocBlockWhitespaceFixer`](src/Fixer/Commenting/RemoveSuperfluousDocBlockWhitespaceFixer.php)

```yaml
# ecs.yaml
services:
    Symplify\CodingStandard\Fixer\Commenting\RemoveSuperfluousDocBlockWhitespaceFixer: null
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

### Parameters, Arguments and Array items should be on the same/standalone line to fit Line Length

- class: [`LineLengthFixer`](src/Fixer/LineLength/LineLengthFixer.php)

```yaml
# ecs.yaml
services:
    Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer:
        # defaults
        max_line_length: 120
        break_long_lines: true
        inline_short_lines: true
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

### Strict types declaration has to be followed by empty line

- class: [`BlankLineAfterStrictTypesFixer`](src/Fixer/Strict/BlankLineAfterStrictTypesFixer.php)

```yaml
# ecs.yaml
services:
    Symplify\CodingStandard\Fixer\Strict\BlankLineAfterStrictTypesFixer: null
```

```diff
 <?php

declare(strict_types=1);
+
 namespace SomeNamespace;
```

<br>

### Use custom exceptions instead of Native Ones

- class: [`NoDefaultExceptionRule`](src/Rules/NoDefaultExceptionRule.php)

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

- class: [`ForbiddenParentClassRule`](src/Rules/ForbiddenParentClassRule.php)

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\ForbiddenParentClassRule

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

- class: [`NoReferenceRule`](src/Rules/NoReferenceRule.php)

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

- class: [`CommentedOutCodeSniff`](src/Sniffs/Debug/CommentedOutCodeSniff.php)

```yaml
# ecs.yaml
services:
    Symplify\CodingStandard\Sniffs\Debug\CommentedOutCodeSniff: null
```

:x:

```php
<?php

// $file = new File;
// $directory = new Diretory([$file]);
```

<br>

### Debug functions Cannot Be left in the Code

- class: [`NoDebugFuncCallRule`](src/Rules/NoDebugFuncCallRule.php)

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

- class: [`ClassNameRespectsParentSuffixRule`](src/Rules/ClassNameRespectsParentSuffixRule.php)

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

<br>

## Object Calisthenics rules

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
