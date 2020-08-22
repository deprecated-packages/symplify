# PHPStan Rules

## Use specific Repository over EntityManager in Controller

- class: [`NoEntityManagerInControllerRule`](../src/Rules/NoEntityManagerInControllerRule.php)

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\NoEntityManagerInControllerRule
```

```php
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

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\NoGetRepositoryOutsideConstructorRule
```

```php
use Doctrine\ORM\EntityManager;

final class SomeController
{
    public function someAction(EntityManager $entityManager)
    {
        $someEntityRepository = $entityManager->getRepository(SomeEntity::class);
    }
}
```

:x:

```php
use Doctrine\ORM\EntityManager;

final class SomeRepository
{
    /**
     * @var \Doctrine\ORM\EntityRepository<SomeEntity>
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

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\NoNullableParameterRule
```

```php
class SomeClass
{
    public function run(?string $vaulue = true)
    {
    }
}
```

:x:

<br>

## No Parameter can Have Default Value

- class: [`NoDefaultParameterValueRule`](../src/Rules/NoDefaultParameterValueRule.php)

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\NoDefaultParameterValueRule
```

```php
class SomeClass
{
    public function run($vaulue = true)
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
class Some extends Command // should be "SomeCommand"
{
}
```

<br>

## Debug functions Cannot Be left in the Code

- class: [`NoDebugFuncCallRule`](../src/Rules/NoDebugFuncCallRule.php)

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\NoDebugFuncCallRule
```

:x:

```php
d($value);
dd($value);
dump($value);
var_dump($value);
```

<br>

## Use explicit comparison over `empty()`

- class: [`NoEmptyRule`](../src/Rules/NoEmptyRule.php)

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\NoEmptyRule
```

:x:

```php
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

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\PreventParentMethodVisibilityOverrideRule
```

```php
class ProtectedVisibility
{
    protected function run()
    {
    }
}

final class PublicOverride extends ProtectedVisibility
{
    public function run()
    {
    }
}
```

:x:

<br>

## Use explicit Property Fetch Names over Dynamic

- class: [`NoDynamicPropertyFetchNameRule`](../src/Rules/NoDynamicPropertyFetchNameRule.php)

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\NoDynamicPropertyFetchNameRule
```

```php
final class DynamicPropertyFetchName
{
    public function run($value)
    {
        $this->$value;
    }
}
```

:x:

<br>

## No Function Call on Method Call

- class: [`NoFunctionCallInMethodCallRule`](../src/Rules/NoFunctionCallInMethodCallRule.php)

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\NoFunctionCallInMethodCallRule
```

```php
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

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\NoArrayAccessOnObjectRule
```

```php
final class MagicArrayObject implements ArrayAccess
{
    public function offsetExists($offset)
    {
        // ...
    }

    public function offsetGet($offset)
    {
        // ...
    }

    public function offsetSet($offset,$value)
    {
        // ...
    }

    public function offsetUnset($offset)
    {
        // ...
    }
}
```


```php
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



:+1:

<br>

## No isset on objects

- class: [`NoIssetOnObjectRule`](../src/Rules/NoIssetOnObjectRule.php)

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\NoIssetOnObjectRule
```

```php
final class IssetOnObject
{
    public function run()
    {
        if (mt_rand(0, 1)) {
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
final class IssetOnObject
{
    public function run()
    {
        $object = null;
        if (mt_rand(0, 1)) {
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

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\NoDynamicMethodNameRule
```

```php
final class DynamicMethodCallName
{
    public function run($value)
    {
        $this->$value();
    }
}
```

:x:

<br>

## Use explicit return values over magic "&$variable" reference

- class: [`NoReferenceRule`](../src/Rules/NoReferenceRule.php)

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\NoReferenceRule
```

```php
function someFunction(&$var)
{
    $var + 1;
}
```

:x:

```php
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
use Doctrine\ORM\EntityRepository;

final class ProductRepository extends EntityRepository
{
}
```

:x:

```php
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

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\NoDefaultExceptionRule
```

```php
throw new RuntimeException('...');
```

:x:

```php
use App\Exception\FileNotFoundExceptoin;

throw new FileNotFoundExceptoin('...');
```

:+1:

<br>

## Forbidden return of `require_once()`/`incude_once()`

- class: [`ForbidReturnValueOfIncludeOnceRule`](../src/Rules/ForbidReturnValueOfIncludeOnceRule.php)

```php
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
// ecs.php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Rules\BoolishClassMethodPrefixRule;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(BoolishClassMethodPrefixRule::class);
};
```


```php
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
// ecs.php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(Symplify\CodingStandard\Rules\MatchingTypeConstantRule::class);
};
```

```php
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
use PHPStan\Rules\Rule;

class SomeRule implements Rule
{
    // ...
}
```

:+1:

```php
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
$dateTime = new DateTime('now'); // should be "Nette\Utils\DateTime"
```

<br>

## Classes with Static Methods must have "Static" in the Name

- class: [`Symplify\CodingStandard\Rules\NoClassWithStaticMethodWithoutStaticNameRule`](../src/Rules/NoClassWithStaticMethodWithoutStaticNameRule.php)

Be honest about static. [Why is static bad?](https://tomasvotruba.com/blog/2019/04/01/removing-static-there-and-back-again/)

Value object static constructors, EventSubscriber and Command classe are excluded.

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\NoClassWithStaticMethodWithoutStaticNameRule
```

:x:

```php
class FormatConverter // should be: "StaticFormatConverter"
{
    public static function yamlToJson(array $yaml): array
    {
        // ...
    }
}
```

<br>

## Use Unique Class Short Names

- class: [`Symplify\CodingStandard\Rules\NoDuplicatedShortClassNameRule`](../src/Rules/NoDuplicatedShortClassNameRule.php)

```yaml
# phpstan.neon
rules:
    - Symplify\CodingStandard\Rules\NoDuplicatedShortClassNameRule
```

:x:

```php
namespace App;

class Finder
{
}
```

```php
namespace App\Entity;

class Finder // should be e.g. "EntityFinder"
{
}
```

<br>

## Cognitive Complexity

### Cognitive Complexity for Method and Class Must be Less than X

- [Why it's the best rule in your coding standard?](https://www.tomasvotruba.com/blog/2018/05/21/is-your-code-readable-by-humans-cognitive-complexity-tells-you/)

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

```php
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

- class: [`Symplify\CodingStandard\ObjectCalisthenics\Rules\NoElseAndElseIfRule`](packages/object-calisthenics/src/Rules/NoElseAndElseIfRule.php)

```yaml
# phpstan.neon
rules:
     - Symplify\CodingStandard\ObjectCalisthenics\Rules\NoElseAndElseIfRule
```

```php
<?php

if ($value) {
    return 5;
} else {
    return 10;
}
```

:x:

```php
if ($value) {
    return 5;
}

return 10;
```

:+1:

<br>

### No Names Shorter than 3 Chars

- class: [`Symplify\CodingStandard\ObjectCalisthenics\Rules\NoShortNameRule`](packages/object-calisthenics/src/Rules/NoShortNameRule.php)

```yaml
# phpstan.neon
rules:
     - Symplify\CodingStandard\ObjectCalisthenics\Rules\NoShortNameRule
```

```php
<?php

final class EM
{
}
```

:x:

```php
<?php

final class EverestMule
{
}
```

:+1:

<br>

### No Setter Methods

- class: [`Symplify\CodingStandard\ObjectCalisthenics\Rules\NoSetterClassMethodRule`](packages/coding-standard/src/Rules/ObjectCalisthenics/NoSetterClassMethodRule.php)

```yaml
# phpstan.neon
rules:
     - Symplify\CodingStandard\ObjectCalisthenics\Rules\NoSetterClassMethodRule
```

:x:

```php
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
- Check [Fluent Interfaces are Evil](https://ocramius.github.io/blog/fluent-interfaces-are-evil/) and [Fluent Interfaces Are Bad for Maintainability
](https://www.yegor256.com/2018/03/13/fluent-interfaces.html)

```yaml
# phpstan.neon
rules:
     - Symplify\CodingStandard\ObjectCalisthenics\Rules\NoChainMethodCallRule
```

```php
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
