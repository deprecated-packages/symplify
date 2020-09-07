# 32+ PHPStan Rules

## Keep Low Property Count in a Class

- class: [`TooManyFieldsRule`](../src/Rules/TooManyFieldsRule.php)
- **configuration allowed**

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\TooManyFieldsRule

parameters:
    symplify:
        # [default: 15]
        max_property_count: 10
```

```php
<?php declare(strict_types=1);

final class SomeClass
{
    public $value;

    public $value2;

    private $value3;

    private $value4;

    private $value5;

    private $value6;

    // ...
}
```

:x:

## Keep Low Public Elements in a Class

- class: [`ExcessivePublicCountRule`](../src/Rules/ExcessivePublicCountRule.php)
- **configuration allowed**

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\ExcessivePublicCountRule

parameters:
    symplify:
        # [default: 45]
        max_public_class_element_count: 30
```

```php
<?php declare(strict_types=1);

final class SomeClass
{
    public const NAME = 'value';

    public $value;

    public function run(): void
    {
    }

    // ...
}
```

:x:

<br>

## Prefer Static Call over specific Function

- class: [`PrefferedStaticCallOverFuncCallRule`](../src/Rules/PrefferedStaticCallOverFuncCallRule.php)
- **configuration required**

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\PrefferedStaticCallOverFuncCallRule

parameters:
    symplify:
        func_call_to_preffered_static_calls:
            'preg_match': ['Nette\Utils\Strings', 'match']
```

```php
<?php declare(strict_types=1);

final class SomeClass
{
    public function run()
    {
        return preg_match('#\d+#', 'content 1');
    }
}
```

:x:

<br>

## Keep Low Parameter Count in Methods and Functions

- class: [`ExcessiveParameterListRule`](../src/Rules/ExcessiveParameterListRule.php)
- **configuration allowed**

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\ExcessiveParameterListRule

parameters:
    symplify:
        # [default: 10]
        max_parameter_count: 5
```

```php
<?php declare(strict_types=1);

final class SomeClass
{
    public function run($one, $two, $three, $four, $five, $six): void
    {
    }
}
```

:x:

<br>

## No Static Calls

- class: [`NoStaticCallRule`](../src/Rules/NoStaticCallRule.php)
- **configuration allowed**

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\NoStaticCallRule

parameters:
    symplify:
        # for \Symplify\CodingStandard\Rules\NoStaticCallRule
        allowed_static_call_classes:
            - 'Nette\Utils\DateTime'
```

```php
<?php declare(strict_types=1);

final class SomeClass
{
    public function run()
    {
        return StaticClass::staticMethod();
    }
}
```

:x:

<br>

## Use Value Object over Return of Values

- class: [`NoReturnArrayVariableList`](../src/Rules/NoReturnArrayVariableList.php)

```php
<?php declare(strict_types=1);

final class ReturnVariables
{
    /**
     * @return mixed[a
     */
    public function run($value, $value2): array
    {
        return [$value, $value2];
    }
}
```

:x:

```php
<?php declare(strict_types=1);

final class ReturnVariables
{
    public function run($value, $value2): ValueObject
    {
        return new ValueObject($value, $value2);
    }
}
```

:+1:

<br>

## Use Explicit String over ::class Reference on Specific Method Call Position

Useful for PHAR prefixing with [php-scoper](https://github.com/humbug/php-scoper) and [box](https://github.com/humbug/box). This allows you to keep configurable string-classes unprefixed. If `::class` is used, they would be prefixed with `Prefix30281...`, so the original class would never be found.

- **configuration required**
- class: [`RequireStringArgumentInMethodCallRule`](../src/Rules/RequireStringArgumentInMethodCallRule.php)

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\RequireStringArgumentInMethodCallRule

parameters:
    symplify:
        string_arg_by_method_by_type:
            SomeObject:
                someMethod: [1]
```

```php
<?php declare(strict_types=1);

class SomeClass
{
    public function run(SomeObject $someObject): void
    {
        $this->someObject->someMethod('yes', Another::class);
    }
}
```

:x:

```php
<?php declare(strict_types=1);

class SomeClass
{
    public function run(SomeObject $someObject): void
    {
        $this->someObject->someMethod('yes', 'Another');
    }
}
```

:+1:

<br>

## Array Destruct is not Allowed, use Value Object instead

- class: [`ForbiddenArrayDestructRule`](../src/Rules/ForbiddenArrayDestructRule.php)

```php
<?php declare(strict_types=1);

final class SomeClass
{
    public function run(): void
    {
        [$firstValue, $secondValue] = $this->getRandomData();
    }
}
```

:x:

```php
<?php declare(strict_types=1);

final class SomeClass
{
    public function run(): void
    {
        $resultValueObject = $this->getRandomData();
        $firstValue = $resultValueObject->getFirstValue();
        $secondValue = $resultValueObject->getSecondValue();
    }
}
```

:+1:

<br>

## Array with String Keys is not allowed, Use Value Object instead

- class: [`ForbiddenArrayWithStringKeysRule`](../src/Rules/ForbiddenArrayWithStringKeysRule.php)

```php
<?php declare(strict_types=1);

final class SomeClass
{
    public function run()
    {
        return [
            'name' => 'John',
            'surname' => 'Dope',
        ];
    }
}
```

:x:

```php
<?php declare(strict_types=1);

final class SomeClass
{
    public function run()
    {
        return new Person('John', 'Dope');
    }
}
```

:+1:

<br>

## Use Value Objects over Array in Complex PHP Configs

- class: [`ForbiddenComplexArrayConfigInSetRule`](../src/Rules/ForbiddenComplexArrayConfigInSetRule.php)

```php
<?php declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(NormalToFluentRector::class)
        ->call('configure', [[
            'options' => ['Cake\Network\Response', ['withLocation', 'withHeader']],
        ]]);
};
```

:x:

```php
<?php declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(NormalToFluentRector::class)
        ->call('configure', [[
            'options' => inline_value_objects([
                new SomeValueObject('Cake\Network\Response', ['withLocation', 'withHeader']),
            ]),
        ]]);
};
```

:+1:

<br>

## Use specific Repository over EntityManager in Controller

- class: [`NoEntityManagerInControllerRule`](../src/Rules/NoEntityManagerInControllerRule.php)

```php
<?php declare(strict_types=1);

use Doctrine\ORM\EntityManagerInterface;

final class SomeController
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        // ...
    }
}
```

:x:

```php
<?php declare(strict_types=1);

final class SomeController
{
    public function __construct(SomeEntityRepository $someEntityRepository)
    {
        // ...
    }
}
```

:+1:

<br>

## `getRepository()` is allowed only in Repository constructor

- class: [`NoGetRepositoryOutsideConstructorRule`](../src/Rules/NoGetRepositoryOutsideConstructorRule.php)

```php
<?php declare(strict_types=1);

use Doctrine\ORM\EntityManager;

final class SomeController
{
    public function someAction(EntityManager $entityManager): void
    {
        $someEntityRepository = $entityManager->getRepository(SomeEntity::class);
    }
}
```

:x:

```php
<?php declare(strict_types=1);

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

final class SomeRepository
{
    /**
     * @var EntityRepository<SomeEntity>
     */
    public $someEntityRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->someEntityRepository = $entityManager->getRepository(SomeEntity::class);
    }
}
```

:+1:

<br>

## No Parameter can be Nullable

Inspired by [Null Hell](https://afilina.com/null-hell) by @afilina

- class: [`NoNullableParameterRule`](../src/Rules/NoNullableParameterRule.php)

```php
<?php declare(strict_types=1);

class SomeClass
{
    public function run(?string $vaulue = true): void
    {
    }
}
```

:x:

<br>

## No Parameter can Have Default Value

- class: [`NoDefaultParameterValueRule`](../src/Rules/NoDefaultParameterValueRule.php)

```php
<?php declare(strict_types=1);

class SomeClass
{
    public function run($vaulue = true): void
    {
    }
}
```

:x:

<br>

## Class should have suffix by parent class/interface

Covers `Interface` suffix as well, e.g `EventSubscriber` checks for `EventSubscriberInterface` as well.

- class: [`ClassNameRespectsParentSuffixRule`](../src/Rules/ClassNameRespectsParentSuffixRule.php)

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
<?php declare(strict_types=1);

// should be "SomeCommand"
class Some extends Command
{
}
```

<br>

## Debug functions Cannot Be left in the Code

- class: [`NoDebugFuncCallRule`](../src/Rules/NoDebugFuncCallRule.php)

:x:

```php
<?php declare(strict_types=1);

d($value);
dd($value);
dump($value);
var_dump($value);
```

<br>

## Use explicit comparison over `empty()`

- class: [`NoEmptyRule`](../src/Rules/NoEmptyRule.php)

:x:

```php
<?php declare(strict_types=1);

final class SomeClass
{
    public function run($value)
    {
        return empty($value);
    }
}
```

:+1:

```php
<?php declare(strict_types=1);

final class SomeClass
{
    public function run(array $value)
    {
        return $value === [];
    }
}
```

<br>

## Prevent Override of Parent Method Visbility

- class: [`PreventParentMethodVisibilityOverrideRule`](../src/Rules/PreventParentMethodVisibilityOverrideRule.php)

```php
<?php declare(strict_types=1);

class ProtectedVisibility
{
    protected function run(): void
    {
    }
}

final class PublicOverride extends ProtectedVisibility
{
    public function run(): void
    {
    }
}
```

:x:

<br>

## Use explicit Property Fetch Names over Dynamic

- class: [`NoDynamicPropertyFetchNameRule`](../src/Rules/NoDynamicPropertyFetchNameRule.php)

```php
<?php declare(strict_types=1);

final class DynamicPropertyFetchName
{
    public function run($value): void
    {
        $this->{$value};
    }
}
```

:x:

<br>

## No Function Call on Method Call

- class: [`NoFunctionCallInMethodCallRule`](../src/Rules/NoFunctionCallInMethodCallRule.php)

```php
<?php declare(strict_types=1);

final class SomeClass
{
    public function run($value): void
    {
        $this->someMethod(strlen('fooo'));
    }

    private function someMethod($value)
    {
        return $value;
    }
}
```

:x:

```php
<?php declare(strict_types=1);

final class SomeClass
{
    public function run($value): void
    {
        $fooSize = strlen('fooo');
        $this->someMethod($fooSize);
    }

    private function someMethod($value)
    {
        return $value;
    }
}
```

:+1:

<br>

## No Array Access on Object

- class: [`NoArrayAccessOnObjectRule`](../src/Rules/NoArrayAccessOnObjectRule.php)

```php
<?php declare(strict_types=1);

final class MagicArrayObject implements ArrayAccess
{
    public function offsetExists($offset): void
    {
        // ...
    }

    public function offsetGet($offset): void
    {
        // ...
    }

    public function offsetSet($offset, $value): void
    {
        // ...
    }

    public function offsetUnset($offset): void
    {
        // ...
    }
}
```

```php
<?php declare(strict_types=1);

final class SomeClass
{
    public function run(MagicArrayObject $magicArrayObject)
    {
        return $magicArrayObject['more_magic'];
    }
}
```

:x:

<br>

## No `isset()` on objects

- class: [`NoIssetOnObjectRule`](../src/Rules/NoIssetOnObjectRule.php)

```php
<?php declare(strict_types=1);

final class IssetOnObject
{
    public function run()
    {
        if (random_int(0, 1)) {
            $object = new SomeClass();
        }

        if (isset($object)) {
            return $object;
        }
    }
}
```

:x:

```php
<?php declare(strict_types=1);

final class IssetOnObject
{
    public function run()
    {
        $object = null;
        if (random_int(0, 1)) {
            $object = new SomeClass();
        }

        if ($object !== null) {
            return $object;
        }
    }
}
```

:+1:

<br>

## Use explicit Method Names over Dynamic

- class: [`NoDynamicMethodNameRule`](../src/Rules/NoDynamicMethodNameRule.php)

```php
<?php declare(strict_types=1);

final class DynamicMethodCallName
{
    public function run($value): void
    {
        $this->{$value}();
    }
}
```

:x:

<br>

## Use explicit return values over magic "&$variable" reference

- class: [`NoReferenceRule`](../src/Rules/NoReferenceRule.php)

```php
<?php declare(strict_types=1);

function someFunction(&$var): void
{
    $var + 1;
}
```

:x:

```php
<?php declare(strict_types=1);

function someFunction($var)
{
    return $var + 1;
}
```

:+1:

<br>

## Class "%s" inherits from forbidden parent class "%s". Use Composition over Inheritance instead

- class: [`ForbiddenParentClassRule`](../src/Rules/ForbiddenParentClassRule.php)

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

```php
<?php declare(strict_types=1);

use Doctrine\ORM\EntityRepository;

final class ProductRepository extends EntityRepository
{
}
```

:x:

```php
<?php declare(strict_types=1);

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

:+1:

<br>

## Use custom exceptions instead of Native Ones

- class: [`NoDefaultExceptionRule`](../src/Rules/NoDefaultExceptionRule.php)

```php
<?php declare(strict_types=1);

throw new RuntimeException('...');
```

:x:

```php
<?php declare(strict_types=1);

use App\Exception\FileNotFoundExceptoin;

throw new FileNotFoundExceptoin('...');
```

:+1:

<br>

## Forbidden return of `require_once()`/`incude_once()`

- class: [`ForbidReturnValueOfIncludeOnceRule`](../src/Rules/ForbidReturnValueOfIncludeOnceRule.php)

```php
<?php

// ecs.php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Rules\ForbidReturnValueOfIncludeOnceRule;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(ForbidReturnValueOfIncludeOnceRule::class);
};
```

:x:

```php
<?php declare(strict_types=1);

class SomeClass
{
    public function run()
    {
        return require_once 'Test.php';
    }
}
```

<br>

## Boolish Methods has to have is/has/was Name

- class: [`BoolishClassMethodPrefixRule`](../src/Rules/BoolishClassMethodPrefixRule.php)

```php
<?php declare(strict_types=1);

class SomeClass
{
    public function old(): bool
    {
        return $this->age > 100;
    }
}
```

:x:

```php
<?php declare(strict_types=1);

class SomeClass
{
    public function isOld(): bool
    {
        return $this->age > 100;
    }
}
```

:+1:

<br>

## Constant type Must Match its Value

- class: [`MatchingTypeConstantRule`](../src/Rules/MatchingTypeConstantRule.php)

```php
<?php declare(strict_types=1);

class SomeClass
{
    /**
     * @var int
     */
    private const LIMIT = 'max';
}
```

:x:


```php
<?php declare(strict_types=1);

class SomeClass
{
    /**
     * @var string
     */
    private const LIMIT = 'max';
}
```

:+1:

<br>

## Defined Method Argument should be Always Constant Value

- class: [`ForceMethodCallArgumentConstantRule`](../src/Rules/ForceMethodCallArgumentConstantRule.php)

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
<?php declare(strict_types=1);

class SomeClass
{
    public function run(): void
    {
        $alwaysCallMeWithConstant = new AlwaysCallMeWithConstant();
        $alwaysCallMeWithConstant->call('someValue');
        // should be: $alwaysCallMeWithConstant->call(TypeList::SOME);
    }
}
```

<br>

## Require @see annotation to class Test case by Type

- class: [`SeeAnnotationToTestRule`](../src/Rules/SeeAnnotationToTestRule.php)

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
<?php declare(strict_types=1);

use PHPStan\Rules\Rule;

class SomeRule implements Rule
{
    // ...
}
```

:+1:

```php
<?php declare(strict_types=1);

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

## Prefer Another Class

- class: [`PreferredClassRule`](../src/Rules/PreferredClassRule.php)

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
<?php declare(strict_types=1);

// should be "Nette\Utils\DateTime"
$dateTime = new DateTime('now');
```

<br>

## Classes with Static Methods must have "Static" in the Name

- class: [`NoClassWithStaticMethodWithoutStaticNameRule`](../src/Rules/NoClassWithStaticMethodWithoutStaticNameRule.php)

Be honest about static. [Why is static bad?](https://tomasvotruba.com/blog/2019/04/01/removing-static-there-and-back-again/)

Value object static constructors, EventSubscriber and Command classe are excluded.

:x:

```php
<?php declare(strict_types=1);

// should be: "StaticFormatConverter"
class FormatConverter
{
    public static function yamlToJson(array $yaml): array
    {
        // ...
    }
}
```

<br>

## Use Unique Class Short Names

- class: [`NoDuplicatedShortClassNameRule`](../src/Rules/NoDuplicatedShortClassNameRule.php)

:x:

```php
<?php declare(strict_types=1);

namespace App;

class Finder
{
}
```

```php
<?php declare(strict_types=1);

namespace App\Entity;

// should be e.g. "EntityFinder"
class Finder
{
}
```

<br>

## Cognitive Complexity

### Cognitive Complexity for Method and Class Must be Less than X

- [Why it's the best rule in your coding standard?](https://www.tomasvotruba.com/blog/2018/05/21/is-your-code-readable-by-humans-cognitive-complexity-tells-you/)

- class: [`FunctionLikeCognitiveComplexityRule`](../packages/cognitive-complexity/src/Rules/FunctionLikeCognitiveComplexityRule.php)
- class: [`ClassLikeCognitiveComplexityRule`](../packages/cognitive-complexity/src/Rules/ClassLikeCognitiveComplexityRule.php)

```yaml
# phpstan.neon
includes:
    - vendor/symplify/coding-standard/packages/cognitive-complexity/config/cognitive-complexity-rules.neon

parameters:
    symplify:
        max_method_cognitive_complexity: 8 # default
        max_class_cognitive_complexity: 50 # default
```

```php
<?php declare(strict_types=1);

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

:x:

```php
<?php declare(strict_types=1);

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

:+1:

<br>

## Object Calisthenics Rules

### No `else` And `elseif`

- class: [`NoElseAndElseIfRule`](../packages/object-calisthenics/src/Rules/NoElseAndElseIfRule.php)

```php
<?php declare(strict_types=1);

if ($value) {
    return 5;
} else {
    return 10;
}
```

:x:

```php
<?php declare(strict_types=1);

if ($value) {
    return 5;
}

return 10;
```

:+1:

<br>

### No Names Shorter than 3 Chars

- class: [`NoShortNameRule`](../packages/object-calisthenics/src/Rules/NoShortNameRule.php)

```php
<?php declare(strict_types=1);

final class EM
{
}
```

:x:

```php
<?php declare(strict_types=1);

final class EverestMule
{
}
```

:+1:

<br>

### No Setter Methods

- class: [`NoSetterClassMethodRule`](../packages/object-calisthenics/src/Rules/NoSetterClassMethodRule.php)

:x:

```php
<?php declare(strict_types=1);

final class Person
{
    private string $name;

    // should be "__construct"
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
```

<br>

### No Chain Method Call

- class: [`NoChainMethodCallRule`](../packages/object-calisthenics/src/Rules/NoChainMethodCallRule.php)
- Check [Fluent Interfaces are Evil](https://ocramius.github.io/blog/fluent-interfaces-are-evil/) and [Fluent Interfaces Are Bad for Maintainability
](https://www.yegor256.com/2018/03/13/fluent-interfaces.html)

```php
<?php declare(strict_types=1);

class SomeClass
{
    public function run()
    {
        return $this->create()->modify()->save();
    }
}
```

:x:

```php
<?php declare(strict_types=1);

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

:+1:
