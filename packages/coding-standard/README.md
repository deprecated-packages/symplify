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

class FormatConverter // should be: "StaticFormatConverter"
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

class Finder // should be e.g. "EntityFinder"
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

$dateTime = new DateTime('now'); // should be "Nette\Utils\DateTime"
```

<br>

### Require @see annotation to class Test case by Type

- class: [`SeeAnnotationToTestRule`](src/Rules/SeeAnnotationToTestRule.php)

```yaml
# phpstan.neon
parameters:
    symplify:
        required_see_types:
            - PHPStan\Rules\Rule

rules:
    - Symplify\CodingStandard\Rules\SeeAnnotationToTestRule
```

:x:

```php
<?php

use PHPStan\Rules\Rule;

class SomeRule implements Rule
{
    // ...
}
```

:+1:

```php
<?php

use PHPStan\Rules\Rule;

/**
 * @see SomeRuleTest
 */
class SomeRule implements Rule
{
    // ...
}
```

<br>

### Defined Method Argument should be Always Constant Value

- class: [`ForceMethodCallArgumentConstantRule`](src/Rules/ForceMethodCallArgumentConstantRule.php)

```yaml
# phpstan.neon
parameters:
    symplify:
        constant_arg_by_method_by_type:
            AlwaysCallMeWithConstant:
                some_type: [0] # positions

rules:
    - Symplify\CodingStandard\Rules\ForceMethodCallArgumentConstantRule
```

:x:

```php
<?php

class SomeClass
{
    public function run()
    {
        $alwaysCallMeWithConstant = new AlwaysCallMeWithConstant();
        $alwaysCallMeWithConstant->call('someValue');
        // should be: $alwaysCallMeWithConstant->call(TypeList::SOME);

    }
}
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

### Constant type Must Match its Value

- class: [`MatchingTypeConstantRule`](src/Rules/MatchingTypeConstantRule.php)

```yaml
# ecs.yaml
services:
    Symplify\CodingStandard\Rules\MatchingTypeConstantRule: null
```

:x:

```php
<?php

class SomeClass
{
    /**
     * @var int
     */
    private const LIMIT = 'max';
}
```

:+1:

```php
<?php

class SomeClass
{
    /**
     * @var string
     */
    private const LIMIT = 'max';
}
```

<br>

### Boolish Methods has to have is/has/was Name

- class: [`BoolishClassMethodPrefixRule`](src/Rules/BoolishClassMethodPrefixRule.php)

```yaml
# ecs.yaml
services:
    Symplify\CodingStandard\Rules\BoolishClassMethodPrefixRule: null
```

:x:

```php
<?php

class SomeClass
{
    public function old(): bool
    {
        return $this->age > 100;
    }
}
```

:+1:

```php
<?php

class SomeClass
{
    public function isOld(): bool
    {
        return $this->age > 100;
    }
}
```

<br>

### Forbidden return of `require_once()`/`incude_once()`

- class: [`ForbidReturnValueOfIncludeOnceRule`](src/Rules/ForbidReturnValueOfIncludeOnceRule.php)

```yaml
# ecs.yaml
services:
    Symplify\CodingStandard\Rules\ForbidReturnValueOfIncludeOnceRule: null
```

:x:

```php
<?php

class SomeClass
{
    public function run()
    {
        return require_once 'Test.php';
    }
}
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

throw new RuntimeException('...'); // should be e.g. "App\Exception\FileNotFoundException"
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

### Use explicit Method Names over Dynamic

- class: [`NoDynamicMethodNameRule`](src/Rules/NoDynamicMethodNameRule.php)

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\NoDynamicMethodNameRule
```

:x:

```php
<?php

final class DynamicMethodCallName
{
    public function run($value)
    {
        $this->$value();
    }
}
```

<br>

### Use explicit Property Fetch Names over Dynamic

- class: [`NoDynamicPropertyFetchNameRule`](src/Rules/NoDynamicPropertyFetchNameRule.php)

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\NoDynamicPropertyFetchNameRule
```

:x:

```php
<?php

final class DynamicPropertyFetchName
{
    public function run($value)
    {
        $this->$value;
    }
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

class Some extends Command // should be "SomeCommand"
{
}
```

<br>

### No Parameter can Have Default Value

- class: [`NoDefaultParameterValueRule`](src/Rules/NoDefaultParameterValueRule.php)

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\NoDefaultParameterValueRule
```

:x:

```php
<?php

class SomeClass
{
    public function run($vaulue = true)
    {
    }
}
```

<br>

### No Parameter can be Nullable

Inspired by [Null Hell](https://afilina.com/null-hell) by @afilina

- class: [`NoNullableParameterRule`](src/Rules/NoNullableParameterRule.php)

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\NoNullableParameterRule
```

:x:

```php
<?php

class SomeClass
{
    public function run(?string $vaulue = true)
    {
    }
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

class EM // should be e.g. "EntityManager"
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

    public function setName(string $name) // should be "__construct"
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
