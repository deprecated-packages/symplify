# 111 Rules Overview

## AnnotateRegexClassConstWithRegexLinkRule

Add regex101.com link to that shows the regex in practise, so it will be easier to maintain in case of bug/extension in the future

- class: [`Symplify\PHPStanRules\Rules\AnnotateRegexClassConstWithRegexLinkRule`](../src/Rules/AnnotateRegexClassConstWithRegexLinkRule.php)

```php
class SomeClass
{
    private const COMPLICATED_REGEX = '#some_complicated_stu|ff#';
}
```

:x:

<br>

```php
class SomeClass
{
    /**
     * @see https://regex101.com/r/SZr0X5/12
     */
    private const COMPLICATED_REGEX = '#some_complicated_stu|ff#';
}
```

:+1:

<br>

## BoolishClassMethodPrefixRule

Method `"%s()"` returns bool type, so the name should start with is/has/was...

- class: [`Symplify\PHPStanRules\Rules\BoolishClassMethodPrefixRule`](../src/Rules/BoolishClassMethodPrefixRule.php)

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

<br>

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

## CheckAttributteArgumentClassExistsRule

Class was not found

- class: [`Symplify\PHPStanRules\Rules\CheckAttributteArgumentClassExistsRule`](../src/Rules/CheckAttributteArgumentClassExistsRule.php)

```php
#[SomeAttribute(firstName: 'MissingClass::class')]
class SomeClass
{
}
```

:x:

<br>

```php
#[SomeAttribute(firstName: ExistingClass::class)]
class SomeClass
{
}
```

:+1:

<br>

## CheckClassNamespaceFollowPsr4Rule

Class like namespace "%s" does not follow PSR-4 configuration in `composer.json`

- class: [`Symplify\PHPStanRules\Rules\CheckClassNamespaceFollowPsr4Rule`](../src/Rules/CheckClassNamespaceFollowPsr4Rule.php)

```php
// defined "Foo\Bar" namespace in composer.json > autoload > psr-4
namespace Foo;

class Baz
{
}
```

:x:

<br>

```php
// defined "Foo\Bar" namespace in composer.json > autoload > psr-4
namespace Foo\Bar;

class Baz
{
}
```

:+1:

<br>

## CheckConstantExpressionDefinedInConstructOrSetupRule

Move constant expression to `__construct()`, `setUp()` method or constant

- class: [`Symplify\PHPStanRules\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule`](../src/Rules/CheckConstantExpressionDefinedInConstructOrSetupRule.php)

```php
class SomeClass
{
    public function someMethod()
    {
        $mainPath = getcwd() . '/absolute_path';
        return __DIR__ . $mainPath;
    }
}
```

:x:

<br>

```php
class SomeClass
{
    private $mainPath;

    public function __construct()
    {
        $this->mainPath = getcwd() . '/absolute_path';
    }

    public function someMethod()
    {
        return $this->mainPath;
    }
}
```

:+1:

<br>

## CheckNotTestsNamespaceOutsideTestsDirectoryRule

"*Test.php" file cannot be located outside "Tests" namespace

- class: [`Symplify\PHPStanRules\Rules\CheckNotTestsNamespaceOutsideTestsDirectoryRule`](../src/Rules/CheckNotTestsNamespaceOutsideTestsDirectoryRule.php)

```php
// file: "SomeTest.php
namespace App;

class SomeTest
{
}
```

:x:

<br>

```php
// file: "SomeTest.php
namespace App\Tests;

class SomeTest
{
}
```

:+1:

<br>

## CheckRequiredInterfaceInContractNamespaceRule

Interface must be located in "Contract" namespace

- class: [`Symplify\PHPStanRules\Rules\CheckRequiredInterfaceInContractNamespaceRule`](../src/Rules/CheckRequiredInterfaceInContractNamespaceRule.php)

```php
namespace App\Repository;

interface ProductRepositoryInterface
{
}
```

:x:

<br>

```php
namespace App\Contract\Repository;

interface ProductRepositoryInterface
{
}
```

:+1:

<br>

## CheckSprinfMatchingTypesRule

`sprintf()` call mask types does not match provided arguments types

- class: [`Symplify\PHPStanRules\Rules\Missing\CheckSprinfMatchingTypesRule`](../src/Rules/Missing/CheckSprinfMatchingTypesRule.php)

```php
echo sprintf('My name is %s and I have %d children', 10, 'Tomas');
```

:x:

<br>

```php
echo sprintf('My name is %s and I have %d children', 'Tomas', 10);
```

:+1:

<br>

## CheckTypehintCallerTypeRule

Parameter %d should use "%s" type as the only type passed to this method

- class: [`Symplify\PHPStanRules\Rules\CheckTypehintCallerTypeRule`](../src/Rules/CheckTypehintCallerTypeRule.php)

```php
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;

class SomeClass
{
    public function run(MethodCall $node)
    {
        $this->isCheck($node);
    }

    private function isCheck(Node $node)
    {
    }
}
```

:x:

<br>

```php
use PhpParser\Node\Expr\MethodCall;

class SomeClass
{
    public function run(MethodCall $node)
    {
        $this->isCheck($node);
    }

    private function isCheck(MethodCall $node)
    {
    }
}
```

:+1:

<br>

## ClassNameRespectsParentSuffixRule

Class should have suffix "%s" to respect parent type

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\ClassNameRespectsParentSuffixRule`](../src/Rules/ClassNameRespectsParentSuffixRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ClassNameRespectsParentSuffixRule
        tags: [phpstan.rules.rule]
        arguments:
            parentClasses:
                - Symfony\Component\Console\Command\Command
```

↓

```php
class Some extends Command
{
}
```

:x:

<br>

```php
class SomeCommand extends Command
{
}
```

:+1:

<br>

## ClassNamespaceGuardRule

Define in which namespaces (using *, ** or ? glob-like pattern matching) can classes extending specified class or implementing specified interface exist

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\ClassNamespaceGuardRule`](../src/Rules/ClassNamespaceGuardRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ClassNamespaceGuardRule
        tags: [phpstan.rules.rule]
        arguments:
            guards:
                Symfony\Component\Form\FormTypeInterface:
                    - App\Form\**
```

↓

```php
namespace App;

// AbstractType implements \Symfony\Component\Form\FormTypeInterface
use Symfony\Component\Form\AbstractType;

class UserForm extends AbstractType
{
}
```

:x:

<br>

```php
namespace App\Form;

use Symfony\Component\Form\AbstractType;

class UserForm extends AbstractType
{
}
```

:+1:

<br>

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ClassNamespaceGuardRule
        tags: [phpstan.rules.rule]
        arguments:
            guards:
                App\Component\PriceEngine\**:
                    - App\Component\PriceEngine\**
                    - App\Component\PriceEngineImpl\**
```

↓

```php
namespace App\Services;

use App\Component\PriceEngine\PriceProviderInterface;

class CustomerProductProvider extends PriceProviderInterface
{
}
```

:x:

<br>

```php
namespace App\Component\PriceEngineImpl;

use App\Component\PriceEngine\PriceProviderInterface;

class CustomerProductProvider extends PriceProviderInterface
{
}
```

:+1:

<br>

## ConstantMapRuleRule

Static constant map should be extracted from this method

- class: [`Symplify\PHPStanRules\Rules\ConstantMapRuleRule`](../src/Rules/ConstantMapRuleRule.php)

```php
class SomeClass
{
    public function run($value)
    {
        if ($value instanceof SomeType) {
            return 100;
        }

        if ($value instanceof AnotherType) {
            return 1000;
        }

        return 200;
    }
}
```

:x:

<br>

```php
class SomeClass
{
    /**
     * @var array<string, int>
     */
    private const TYPE_TO_VALUE = [
        SomeType::class => 100,
        AnotherType::class => 1000,
    ];

    public function run($value)
    {
        foreach (self::TYPE_TO_VALUE as $type => $value) {
            if (is_a($value, $type, true)) {
                return $value;
            }
        }

        return 200;
    }
}
```

:+1:

<br>

## EmbeddedEnumClassConstSpotterRule

Constants "%s" should be extract to standalone enum class

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\Enum\EmbeddedEnumClassConstSpotterRule`](../src/Rules/Enum/EmbeddedEnumClassConstSpotterRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\Enum\EmbeddedEnumClassConstSpotterRule
        tags: [phpstan.rules.rule]
        arguments:
            parentTypes:
                - AbstractObject
```

↓

```php
class SomeProduct extends AbstractObject
{
    public const STATUS_ENABLED = 1;

    public const STATUS_DISABLED = 0;
}
```

:x:

<br>

```php
class SomeProduct extends AbstractObject
{
}

class SomeStatus
{
    public const ENABLED = 1;

    public const DISABLED = 0;
}
```

:+1:

<br>

## EnumSpotterRule

The string value "%s" is repeated %d times. Refactor to enum to avoid typos and make clear allowed values

- class: [`Symplify\PHPStanRules\Rules\Domain\EnumSpotterRule`](../src/Rules/Domain/EnumSpotterRule.php)

```php
$this->addFlash('info', 'Some message');
$this->addFlash('info', 'Another message');
```

:x:

<br>

```php
$this->addFlash(FlashType::INFO, 'Some message');
$this->addFlash(FlashType::INFO, 'Another message');
```

:+1:

<br>

## ExclusiveDependencyRule

Dependency of specific type can be used only in specific class types

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\ExclusiveDependencyRule`](../src/Rules/ExclusiveDependencyRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ExclusiveDependencyRule
        tags: [phpstan.rules.rule]
        arguments:
            allowedExclusiveDependencyInTypes:
                Doctrine\ORM\EntityManager:
                    - *Repository

                Doctrine\ORM\EntityManagerInterface:
                    - *Repository
```

↓

```php
final class CheckboxController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }
}
```

:x:

<br>

```php
final class CheckboxRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }
}
```

:+1:

<br>

## ExclusiveNamespaceRule

Exclusive namespace can only contain classes of specific type, nothing else

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\ExclusiveNamespaceRule`](../src/Rules/ExclusiveNamespaceRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ExclusiveNamespaceRule
        tags: [phpstan.rules.rule]
        arguments:
            namespaceParts:
                - Presenter
```

↓

```php
namespace App\Presenter;

class SomeRepository
{
}
```

:x:

<br>

```php
namespace App\Presenter;

class SomePresenter
{
}
```

:+1:

<br>

## ExplicitMethodCallOverMagicGetSetRule

Instead of magic property "%s" access use direct explicit `"%s->%s()"` method call

- class: [`Symplify\PHPStanRules\Rules\Explicit\ExplicitMethodCallOverMagicGetSetRule`](../src/Rules/Explicit/ExplicitMethodCallOverMagicGetSetRule.php)

```php
use Nette\SmartObject;

final class MagicObject
{
    // adds magic __get() and __set() methods
    use SmartObject;

    private $name;

    public function getName()
    {
        return $this->name;
    }
}

$magicObject = new MagicObject();
// magic re-directed to method
$magicObject->name;
```

:x:

<br>

```php
use Nette\SmartObject;

final class MagicObject
{
    // adds magic __get() and __set() methods
    use SmartObject;

    private $name;

    public function getName()
    {
        return $this->name;
    }
}

$magicObject = new MagicObject();
// explicit
$magicObject->getName();
```

:+1:

<br>

## ForbiddenAnonymousClassRule

Anonymous class is not allowed.

- class: [`Symplify\PHPStanRules\Rules\ForbiddenAnonymousClassRule`](../src/Rules/ForbiddenAnonymousClassRule.php)

```php
new class {};
```

:x:

<br>

```php
class SomeClass
{

}

new SomeClass;
```

:+1:

<br>

## ForbiddenArrayDestructRule

Array destruct is not allowed. Use value object to pass data instead

- class: [`Symplify\PHPStanRules\Rules\ForbiddenArrayDestructRule`](../src/Rules/ForbiddenArrayDestructRule.php)

```php
final class SomeClass
{
    public function run(): void
    {
        [$firstValue, $secondValue] = $this->getRandomData();
    }
}
```

:x:

<br>

```php
final class SomeClass
{
    public function run(): void
    {
        $valueObject = $this->getValueObject();
        $firstValue = $valueObject->getFirstValue();
        $secondValue = $valueObject->getSecondValue();
    }
}
```

:+1:

<br>

## ForbiddenArrayMethodCallRule

Array method calls [$this, "method"] are not allowed. Use explicit method instead to help PhpStorm, PHPStan and Rector understand your code

- class: [`Symplify\PHPStanRules\Rules\Complexity\ForbiddenArrayMethodCallRule`](../src/Rules/Complexity/ForbiddenArrayMethodCallRule.php)

```php
usort($items, [$this, "method"]);
```

:x:

<br>

```php
usort($items, function (array $apples) {
    return $this->method($apples);
};
```

:+1:

<br>

## ForbiddenArrayWithStringKeysRule

Array with keys is not allowed. Use value object to pass data instead

- class: [`Symplify\PHPStanRules\Rules\ForbiddenArrayWithStringKeysRule`](../src/Rules/ForbiddenArrayWithStringKeysRule.php)

```php
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

<br>

```php
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

## ForbiddenClassConstRule

Constants in this class are not allowed, move them to custom Enum class instead

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\Enum\ForbiddenClassConstRule`](../src/Rules/Enum/ForbiddenClassConstRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\Enum\ForbiddenClassConstRule
        tags: [phpstan.rules.rule]
        arguments:
            classTypes:
                - AbstractEntity
```

↓

```php
final class Product extends AbstractEntity
{
    public const TYPE_HIDDEN = 0;

    public const TYPE_VISIBLE = 1;
}
```

:x:

<br>

```php
final class Product extends AbstractEntity
{
}

class ProductVisibility extends Enum
{
    public const HIDDEN = 0;

    public const VISIBLE = 1;
}
```

:+1:

<br>

## ForbiddenComplexForeachIfExprRule

`foreach()`, `while()`, `for()` or `if()` cannot contain a complex expression. Extract it to a new variable on a line before

- class: [`Symplify\PHPStanRules\Rules\Complexity\ForbiddenComplexForeachIfExprRule`](../src/Rules/Complexity/ForbiddenComplexForeachIfExprRule.php)

```php
foreach ($this->getData($arg) as $key => $item) {
    // ...
}
```

:x:

<br>

```php
$data = $this->getData($arg);
foreach ($arg as $key => $item) {
    // ...
}
```

:+1:

<br>

## ForbiddenFuncCallRule

Function `"%s()"` cannot be used/left in the code

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\ForbiddenFuncCallRule`](../src/Rules/ForbiddenFuncCallRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ForbiddenFuncCallRule
        tags: [phpstan.rules.rule]
        arguments:
            forbiddenFunctions:
                - eval
```

↓

```php
class SomeClass
{
    return eval('...');
}
```

:x:

<br>

```php
class SomeClass
{
    return echo '...';
}
```

:+1:

<br>

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ForbiddenFuncCallRule
        tags: [phpstan.rules.rule]
        arguments:
            forbiddenFunctions:
                dump: seems you missed some debugging function
```

↓

```php
class SomeClass
{
    dump('hello world');
    return true;
}
```

:x:

<br>

```php
class SomeClass
{
    return true;
}
```

:+1:

<br>

## ForbiddenInlineClassMethodRule

Method `"%s()"` only calling another method call and has no added value. Use the inlined call instead

- class: [`Symplify\PHPStanRules\Rules\Complexity\ForbiddenInlineClassMethodRule`](../src/Rules/Complexity/ForbiddenInlineClassMethodRule.php)

```php
class SomeClass
{
    public function run()
    {
        return $this->away();
    }

    private function away()
    {
        return mt_rand(0, 100);
    }
}
```

:x:

<br>

```php
class SomeClass
{
    public function run()
    {
        return mt_rand(0, 100);
    }
}
```

:+1:

<br>

## ForbiddenMultipleClassLikeInOneFileRule

Multiple class/interface/trait is not allowed in single file

- class: [`Symplify\PHPStanRules\Rules\ForbiddenMultipleClassLikeInOneFileRule`](../src/Rules/ForbiddenMultipleClassLikeInOneFileRule.php)

```php
// src/SomeClass.php
class SomeClass
{
}

interface SomeInterface
{
}
```

:x:

<br>

```php
// src/SomeClass.php
class SomeClass
{
}

// src/SomeInterface.php
interface SomeInterface
{
}
```

:+1:

<br>

## ForbiddenNamedArgumentsRule

Named arguments do not add any value here. Use normal arguments in the same order

- class: [`Symplify\PHPStanRules\Rules\Complexity\ForbiddenNamedArgumentsRule`](../src/Rules/Complexity/ForbiddenNamedArgumentsRule.php)

```php
return strlen(string: 'name');
```

:x:

<br>

```php
return strlen('name');
```

:+1:

<br>

## ForbiddenNestedCallInAssertMethodCallRule

Decouple method call in assert to standalone line to make test core more readable

- class: [`Symplify\PHPStanRules\Rules\ForbiddenNestedCallInAssertMethodCallRule`](../src/Rules/ForbiddenNestedCallInAssertMethodCallRule.php)

```php
use PHPUnit\Framework\TestCase;

final class SomeClass extends TestCase
{
    public function test()
    {
        $this->assertSame('oooo', $this->someMethodCall());
    }
}
```

:x:

<br>

```php
use PHPUnit\Framework\TestCase;

final class SomeClass extends TestCase
{
    public function test()
    {
        $result = $this->someMethodCall();
        $this->assertSame('oooo', $result);
    }
}
```

:+1:

<br>

## ForbiddenNodeRule

"%s" is forbidden to use

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\ForbiddenNodeRule`](../src/Rules/ForbiddenNodeRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ForbiddenNodeRule
        tags: [phpstan.rules.rule]
        arguments:
            forbiddenNodes:
                - PhpParser\Node\Expr\ErrorSuppress
```

↓

```php
return @strlen('...');
```

:x:

<br>

```php
return strlen('...');
```

:+1:

<br>

## ForbiddenParamTypeRemovalRule

Removing parent param type is forbidden

- class: [`Symplify\PHPStanRules\Rules\ForbiddenParamTypeRemovalRule`](../src/Rules/ForbiddenParamTypeRemovalRule.php)

```php
interface RectorInterface
{
    public function refactor(Node $node);
}

final class SomeRector implements RectorInterface
{
    public function refactor($node)
    {
    }
}
```

:x:

<br>

```php
interface RectorInterface
{
    public function refactor(Node $node);
}

final class SomeRector implements RectorInterface
{
    public function refactor(Node $node)
    {
    }
}
```

:+1:

<br>

## ForbiddenProtectedPropertyRule

Property with protected modifier is not allowed. Use interface contract method instead

- class: [`Symplify\PHPStanRules\Rules\ForbiddenProtectedPropertyRule`](../src/Rules/ForbiddenProtectedPropertyRule.php)

```php
class SomeClass
{
    protected $repository;
}
```

:x:

<br>

```php
class SomeClass implements RepositoryAwareInterface
{
    public function getRepository()
    {
        // ....
    }
}
```

:+1:

<br>

## ForbiddenSameNamedAssignRule

Variables "%s" are overridden. This can lead to unwanted bugs, please pick a different name to avoid it.

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\Complexity\ForbiddenSameNamedAssignRule`](../src/Rules/Complexity/ForbiddenSameNamedAssignRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\Complexity\ForbiddenSameNamedAssignRule
        tags: [phpstan.rules.rule]
        arguments:
            allowedVariableNames:
                - position
```

↓

```php
$value = 1000;
$value = 2000;
```

:x:

<br>

```php
$value = 1000;
$anotherValue = 2000;
```

:+1:

<br>

## ForbiddenSameNamedNewInstanceRule

New objects with "%s" name are overridden. This can lead to unwanted bugs, please pick a different name to avoid it.

- class: [`Symplify\PHPStanRules\Rules\Complexity\ForbiddenSameNamedNewInstanceRule`](../src/Rules/Complexity/ForbiddenSameNamedNewInstanceRule.php)

```php
$product = new Product();
$product = new Product();

$this->productRepository->save($product);
```

:x:

<br>

```php
$firstProduct = new Product();
$secondProduct = new Product();

$this->productRepository->save($firstProduct);
```

:+1:

<br>

## ForbiddenSpreadOperatorRule

Spread operator is not allowed.

- class: [`Symplify\PHPStanRules\Rules\ForbiddenSpreadOperatorRule`](../src/Rules/ForbiddenSpreadOperatorRule.php)

```php
$args = [$firstValue, $secondValue];
$message = sprintf('%s', ...$args);
```

:x:

<br>

```php
$message = sprintf('%s', $firstValue, $secondValue);
```

:+1:

<br>

## ForbiddenTestsNamespaceOutsideTestsDirectoryRule

"Tests" namespace can be only in "/tests" directory

- class: [`Symplify\PHPStanRules\Rules\ForbiddenTestsNamespaceOutsideTestsDirectoryRule`](../src/Rules/ForbiddenTestsNamespaceOutsideTestsDirectoryRule.php)

```php
// file path: "src/SomeClass.php

namespace App\Tests;

class SomeClass
{
}
```

:x:

<br>

```php
// file path: "tests/SomeClass.php

namespace App\Tests;

class SomeClass
{
}
```

:+1:

<br>

## ForbiddenThisArgumentRule

`$this` as argument is not allowed. Refactor method to service composition

- class: [`Symplify\PHPStanRules\Rules\ForbiddenThisArgumentRule`](../src/Rules/ForbiddenThisArgumentRule.php)

```php
$this->someService->process($this, ...);
```

:x:

<br>

```php
$this->someService->process($value, ...);
```

:+1:

<br>

## IfElseToMatchSpotterRule

If/else construction can be replace with more robust `match()`

- class: [`Symplify\PHPStanRules\Rules\Spotter\IfElseToMatchSpotterRule`](../src/Rules/Spotter/IfElseToMatchSpotterRule.php)

```php
class SomeClass
{
    public function spot($value)
    {
        if ($value === 100) {
            $items = ['yes'];
        } else {
            $items = ['no'];
        }

        return $items;
    }
}
```

:x:

<br>

```php
class SomeClass
{
    public function spot($value)
    {
        return match($value) {
            100 => ['yes'],
            default => ['no'],
        };
    }
}
```

:+1:

<br>

## NoAbstractMethodRule

Use explicit interface contract or a service over unclear abstract methods

- class: [`Symplify\PHPStanRules\Rules\NoAbstractMethodRule`](../src/Rules/NoAbstractMethodRule.php)

```php
abstract class SomeClass
{
    abstract public function run();
}
```

:x:

<br>

```php
abstract class SomeClass implements RunnableInterface
{
}

interface RunnableInterface
{
    public function run();
}
```

:+1:

<br>

## NoAbstractRule

Instead of abstract class, use specific service with composition

- class: [`Symplify\PHPStanRules\Rules\Complexity\NoAbstractRule`](../src/Rules/Complexity/NoAbstractRule.php)

```php
final class NormalHelper extends AbstractHelper
{
}

abstract class AbstractHelper
{
}
```

:x:

<br>

```php
final class NormalHelper
{
    public function __construct(
        private SpecificHelper $specificHelper
    ) {
    }
}

final class SpecificHelper
{
}
```

:+1:

<br>

## NoArrayAccessOnObjectRule

Use explicit methods over array access on object

- class: [`Symplify\PHPStanRules\Rules\NoArrayAccessOnObjectRule`](../src/Rules/NoArrayAccessOnObjectRule.php)

```php
class SomeClass
{
    public function run(MagicArrayObject $magicArrayObject)
    {
        return $magicArrayObject['more_magic'];
    }
}
```

:x:

<br>

```php
class SomeClass
{
    public function run(MagicArrayObject $magicArrayObject)
    {
        return $magicArrayObject->getExplicitValue();
    }
}
```

:+1:

<br>

## NoArrayStringObjectReturnRule

Use another value object over array with string-keys and objects, array<string, ValueObject>

- class: [`Symplify\PHPStanRules\Rules\NoArrayStringObjectReturnRule`](../src/Rules/NoArrayStringObjectReturnRule.php)

```php
final class SomeClass
{
    public function getItems()
    {
        return $this->getValues();
    }

    /**
     * @return array<string, Value>
     */
    private function getValues()
    {
    }
}
```

:x:

<br>

```php
final class SomeClass
{
    public function getItems()
    {
        return $this->getValues();
    }

    /**
     * @return WrappingValue[]
     */
    private function getValues()
    {
        // ...
    }
}
```

:+1:

<br>

## NoBinaryOpCallCompareRule

No magic closure function call is allowed, use explicit class with method instead

- class: [`Symplify\PHPStanRules\Rules\NoBinaryOpCallCompareRule`](../src/Rules/NoBinaryOpCallCompareRule.php)

```php
return array_filter($items, function ($item) {
}) !== [];
```

:x:

<br>

```php
$values = array_filter($items, function ($item) {
});
return $values !== [];
```

:+1:

<br>

## NoClassWithStaticMethodWithoutStaticNameRule

Class has a static method must so must contains "Static" in its name

- class: [`Symplify\PHPStanRules\Rules\NoClassWithStaticMethodWithoutStaticNameRule`](../src/Rules/NoClassWithStaticMethodWithoutStaticNameRule.php)

```php
class SomeClass
{
    public static function getSome()
    {
    }
}
```

:x:

<br>

```php
class SomeStaticClass
{
    public static function getSome()
    {
    }
}
```

:+1:

<br>

## NoConstantInterfaceRule

Reserve interface for contract only. Move constant holder to a class soon-to-be Enum

- class: [`Symplify\PHPStanRules\Rules\Enum\NoConstantInterfaceRule`](../src/Rules/Enum/NoConstantInterfaceRule.php)

```php
interface SomeContract
{
    public const YES = 'yes';

    public const NO = 'no';
}
```

:x:

<br>

```php
class SomeValues
{
    public const YES = 'yes';

    public const NO = 'no';
}
```

:+1:

<br>

## NoConstructorInTestRule

Do not use constructor in tests. Move to `setUp()` method

- class: [`Symplify\PHPStanRules\Rules\NoConstructorInTestRule`](../src/Rules/NoConstructorInTestRule.php)

```php
final class SomeTest
{
    public function __construct()
    {
        // ...
    }
}
```

:x:

<br>

```php
final class SomeTest
{
    public function setUp()
    {
        // ...
    }
}
```

:+1:

<br>

## NoDefaultExceptionRule

Use custom exceptions instead of native "%s"

- class: [`Symplify\PHPStanRules\Rules\NoDefaultExceptionRule`](../src/Rules/NoDefaultExceptionRule.php)

```php
throw new RuntimeException('...');
```

:x:

<br>

```php
use App\Exception\FileNotFoundException;

throw new FileNotFoundException('...');
```

:+1:

<br>

## NoDependencyJugglingRule

Use dependency injection instead of dependency juggling

- class: [`Symplify\PHPStanRules\Rules\NoDependencyJugglingRule`](../src/Rules/NoDependencyJugglingRule.php)

```php
public function __construct(
    private $service
) {
}

public function run($someObject)
{
    return $someObject->someMethod($this->service);
}
```

:x:

<br>

```php
public function run($someObject)
{
    return $someObject->someMethod();
}
```

:+1:

<br>

## NoDuplicatedArgumentRule

This call has duplicate argument

- class: [`Symplify\PHPStanRules\Rules\Complexity\NoDuplicatedArgumentRule`](../src/Rules/Complexity/NoDuplicatedArgumentRule.php)

```php
function run($one, $one);
```

:x:

<br>

```php
function run($one, $two);
```

:+1:

<br>

## NoDuplicatedShortClassNameRule

Class with base "%s" name is already used in "%s". Use unique name to make classes easy to recognize

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\NoDuplicatedShortClassNameRule`](../src/Rules/NoDuplicatedShortClassNameRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\NoDuplicatedShortClassNameRule
        tags: [phpstan.rules.rule]
        arguments:
            toleratedNestingLevel: 1
```

↓

```php
namespace App;

class SomeClass
{
}

namespace App\Nested;

class SomeClass
{
}
```

:x:

<br>

```php
namespace App;

class SomeClass
{
}

namespace App\Nested;

class AnotherClass
{
}
```

:+1:

<br>

## NoDynamicNameRule

Use explicit names over dynamic ones

- class: [`Symplify\PHPStanRules\Rules\NoDynamicNameRule`](../src/Rules/NoDynamicNameRule.php)

```php
class SomeClass
{
    public function old(): bool
    {
        return $this->${variable};
    }
}
```

:x:

<br>

```php
class SomeClass
{
    public function old(): bool
    {
        return $this->specificMethodName();
    }
}
```

:+1:

<br>

## NoDynamicPropertyOnStaticCallRule

Use non-dynamic property on static calls or class const fetches

- class: [`Symplify\PHPStanRules\Rules\NoDynamicPropertyOnStaticCallRule`](../src/Rules/NoDynamicPropertyOnStaticCallRule.php)

```php
class SomeClass
{
    public function run()
    {
        return $this->connection::literal();
    }
}
```

:x:

<br>

```php
class SomeClass
{
    public function run()
    {
        return Connection::literal();
    }
}
```

:+1:

<br>

## NoEmptyClassRule

There should be no empty class

- class: [`Symplify\PHPStanRules\Rules\NoEmptyClassRule`](../src/Rules/NoEmptyClassRule.php)

```php
class SomeClass
{
}
```

:x:

<br>

```php
class SomeClass
{
    public function getSome()
    {
    }
}
```

:+1:

<br>

## NoFactoryInConstructorRule

Do not use factory/method call in constructor. Put factory in config and get service with dependency injection

- class: [`Symplify\PHPStanRules\Rules\NoFactoryInConstructorRule`](../src/Rules/NoFactoryInConstructorRule.php)

```php
class SomeClass
{
    private $someDependency;

    public function __construct(SomeFactory $factory)
    {
        $this->someDependency = $factory->build();
    }
}
```

:x:

<br>

```php
class SomeClass
{
    private $someDependency;

    public function __construct(SomeDependency $someDependency)
    {
        $this->someDependency = $someDependency;
    }
}
```

:+1:

<br>

## NoFuncCallInMethodCallRule

Separate function `"%s()"` in method call to standalone row to improve readability

- class: [`Symplify\PHPStanRules\Rules\NoFuncCallInMethodCallRule`](../src/Rules/NoFuncCallInMethodCallRule.php)

```php
final class SomeClass
{
    public function run($value): void
    {
        $this->someMethod(strlen('fooo'));
    }

    // ...
}
```

:x:

<br>

```php
final class SomeClass
{
    public function run($value): void
    {
        $fooLength = strlen('fooo');
        $this->someMethod($fooLength);
    }

    // ...
}
```

:+1:

<br>

## NoGetRepositoryOutsideConstructorRule

Do not use `"$entityManager->getRepository()"` outside of the constructor of repository service or `setUp()` method in test case

- class: [`Symplify\PHPStanRules\Rules\NoGetRepositoryOutsideConstructorRule`](../src/Rules/NoGetRepositoryOutsideConstructorRule.php)

```php
final class SomeController
{
    public function someAction(EntityManager $entityManager): void
    {
        $someEntityRepository = $entityManager->getRepository(SomeEntity::class);
    }
}
```

:x:

<br>

```php
final class SomeRepository
{
    public function __construct(EntityManager $entityManager): void
    {
        $someEntityRepository = $entityManager->getRepository(SomeEntity::class);
    }
}
```

:+1:

<br>

## NoGetterAndPropertyRule

There are 2 way to get "%s" value: public property and getter now - pick one to avoid variant behavior.

- class: [`Symplify\PHPStanRules\Rules\Explicit\NoGetterAndPropertyRule`](../src/Rules/Explicit/NoGetterAndPropertyRule.php)

```php
final class SomeProduct
{
    public $name;

    public function getName(): string
    {
        return $this->name;
    }
}
```

:x:

<br>

```php
final class SomeProduct
{
    private $name;

    public function getName(): string
    {
        return $this->name;
    }
}
```

:+1:

<br>

## NoInlineStringRegexRule

Use local named constant instead of inline string for regex to explain meaning by constant name

- class: [`Symplify\PHPStanRules\Rules\NoInlineStringRegexRule`](../src/Rules/NoInlineStringRegexRule.php)

```php
class SomeClass
{
    public function run($value)
    {
        return preg_match('#some_stu|ff#', $value);
    }
}
```

:x:

<br>

```php
class SomeClass
{
    /**
     * @var string
     */
    public const SOME_STUFF_REGEX = '#some_stu|ff#';

    public function run($value)
    {
        return preg_match(self::SOME_STUFF_REGEX, $value);
    }
}
```

:+1:

<br>

## NoIssetOnObjectRule

Use default null value and nullable compare instead of isset on object

- class: [`Symplify\PHPStanRules\Rules\NoIssetOnObjectRule`](../src/Rules/NoIssetOnObjectRule.php)

```php
class SomeClass
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

<br>

```php
class SomeClass
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

## NoMagicClosureRule

No magic closure function call is allowed, use explicit class with method instead

- class: [`Symplify\PHPStanRules\Rules\NoMagicClosureRule`](../src/Rules/NoMagicClosureRule.php)

```php
(static function () {
    // ...
})
```

:x:

<br>

```php
final class HelpfulName
{
    public function clearName()
    {
        // ...
    }
}
```

:+1:

<br>

## NoMethodTagInClassDocblockRule

Do not use `@method` tag in class docblock

- class: [`Symplify\PHPStanRules\Rules\NoMethodTagInClassDocblockRule`](../src/Rules/NoMethodTagInClassDocblockRule.php)

```php
/**
 * @method getMagic() string
 */
class SomeClass
{
    public function __call()
    {
        // more magic
    }
}
```

:x:

<br>

```php
class SomeClass
{
    public function getExplicitValue()
    {
        return 'explicit';
    }
}
```

:+1:

<br>

## NoMirrorAssertRule

The assert is tautology that compares to itself. Fix it to different values

- class: [`Symplify\PHPStanRules\Rules\Complexity\NoMirrorAssertRule`](../src/Rules/Complexity/NoMirrorAssertRule.php)

```php
use PHPUnit\Framework\TestCase;

final class AssertMirror extends TestCase
{
    public function test()
    {
        $this->assertSame(1, 1);
    }
}
```

:x:

<br>

```php
use PHPUnit\Framework\TestCase;

final class AssertMirror extends TestCase
{
    public function test()
    {
        $value = 200;
        $this->assertSame(1, $value);
    }
}
```

:+1:

<br>

## NoMissingArrayShapeReturnArrayRule

Complete known array shape to the method `@return` type

- class: [`Symplify\PHPStanRules\Rules\Explicit\NoMissingArrayShapeReturnArrayRule`](../src/Rules/Explicit/NoMissingArrayShapeReturnArrayRule.php)

```php
function run(string $name)
{
    return ['name' => $name];
}
```

:x:

<br>

```php
/**
 * @return array{name: string}
 */
function run(string $name)
{
    return ['name' => $name];
}
```

:+1:

<br>

## NoMissingDirPathRule

The path "%s" was not found

- class: [`Symplify\PHPStanRules\Rules\NoMissingDirPathRule`](../src/Rules/NoMissingDirPathRule.php)

```php
$filePath = __DIR__ . '/missing_location.txt';
```

:x:

<br>

```php
$filePath = __DIR__ . '/existing_location.txt';
```

:+1:

<br>

## NoMixedArrayDimFetchRule

Add explicit array type to assigned "%s" expression

- class: [`Symplify\PHPStanRules\Rules\Explicit\NoMixedArrayDimFetchRule`](../src/Rules/Explicit/NoMixedArrayDimFetchRule.php)

```php
class SomeClass
{
    private $items = [];

    public function addItem(string $key, string $value)
    {
        $this->items[$key] = $value;
    }
}
```

:x:

<br>

```php
class SomeClass
{
    /**
     * @var array<string, string>
     */
    private $items = [];

    public function addItem(string $key, string $value)
    {
        $this->items[$key] = $value;
    }
}
```

:+1:

<br>

## NoMixedCallableRule

Make callable type explicit. Here is how: https://phpstan.org/writing-php-code/phpdoc-types#callables

- class: [`Symplify\PHPStanRules\Rules\Explicit\NoMixedCallableRule`](../src/Rules/Explicit/NoMixedCallableRule.php)

```php
function run(callable $callable)
{
    return $callable(100);
}
```

:x:

<br>

```php
/**
 * @param callable(): int $callable
 */
function run(callable $callable): int
{
    return $callable(100);
}
```

:+1:

<br>

## NoMixedMethodCallerRule

Anonymous variable in a `%s->...()` method call can lead to false dead methods. Make sure the variable type is known

- class: [`Symplify\PHPStanRules\Rules\Explicit\NoMixedMethodCallerRule`](../src/Rules/Explicit/NoMixedMethodCallerRule.php)

```php
function run($unknownType)
{
    return $unknownType->call();
}
```

:x:

<br>

```php
function run(KnownType $knownType)
{
    return $knownType->call();
}
```

:+1:

<br>

## NoMixedPropertyFetcherRule

Anonymous variables in a "%s->..." property fetch can lead to false dead property. Make sure the variable type is known

- class: [`Symplify\PHPStanRules\Rules\Explicit\NoMixedPropertyFetcherRule`](../src/Rules/Explicit/NoMixedPropertyFetcherRule.php)

```php
function run($unknownType)
{
    return $unknownType->name;
}
```

:x:

<br>

```php
function run(KnownType $knownType)
{
    return $knownType->name;
}
```

:+1:

<br>

## NoModifyAndReturnSelfObjectRule

Use void instead of modify and return self object

- class: [`Symplify\PHPStanRules\Rules\NoModifyAndReturnSelfObjectRule`](../src/Rules/NoModifyAndReturnSelfObjectRule.php)

```php
final class SomeClass
{
    public function modify(ComposerJson $composerJson): ComposerJson
    {
        $composerJson->addPackage('some-package');
        return $composerJson;
    }
}
```

:x:

<br>

```php
final class SomeClass
{
    public function modify(ComposerJson $composerJson): void
    {
        $composerJson->addPackage('some-package');
    }
}
```

:+1:

<br>

## NoMultiArrayAssignRule

Use value object over multi array assign

- class: [`Symplify\PHPStanRules\Rules\NoMultiArrayAssignRule`](../src/Rules/NoMultiArrayAssignRule.php)

```php
$values = [];
$values['person']['name'] = 'Tom';
$values['person']['surname'] = 'Dev';
```

:x:

<br>

```php
$values = [];
$values[] = new Person('Tom', 'Dev');
```

:+1:

<br>

## NoNestedFuncCallRule

Use separate function calls with readable variable names

- class: [`Symplify\PHPStanRules\Rules\NoNestedFuncCallRule`](../src/Rules/NoNestedFuncCallRule.php)

```php
$filteredValues = array_filter(array_map($callback, $items));
```

:x:

<br>

```php
$mappedItems = array_map($callback, $items);
$filteredValues = array_filter($mappedItems);
```

:+1:

<br>

## NoNullableArrayPropertyRule

Use required typed property over of nullable array property

- class: [`Symplify\PHPStanRules\Rules\NoNullableArrayPropertyRule`](../src/Rules/NoNullableArrayPropertyRule.php)

```php
final class SomeClass
{
    private ?array $property = null;
}
```

:x:

<br>

```php
final class SomeClass
{
    private array $property = [];
}
```

:+1:

<br>

## NoParentDuplicatedTraitUseRule

The "%s" trait is already used in parent class. Remove it here

- class: [`Symplify\PHPStanRules\Rules\Complexity\NoParentDuplicatedTraitUseRule`](../src/Rules/Complexity/NoParentDuplicatedTraitUseRule.php)

```php
class ParentClass
{
    use SomeTrait;
}

class SomeClass extends ParentClass
{
    use SomeTrait;
}
```

:x:

<br>

```php
class ParentClass
{
    use SomeTrait;
}

class SomeClass extends ParentClass
{
}
```

:+1:

<br>

## NoParentMethodCallOnEmptyStatementInParentMethodRule

Do not call parent method if parent method is empty

- class: [`Symplify\PHPStanRules\Rules\NoParentMethodCallOnEmptyStatementInParentMethodRule`](../src/Rules/NoParentMethodCallOnEmptyStatementInParentMethodRule.php)

```php
class ParentClass
{
    public function someMethod()
    {
    }
}

class SomeClass extends ParentClass
{
    public function someMethod()
    {
        parent::someMethod();
    }
}
```

:x:

<br>

```php
class ParentClass
{
    public function someMethod()
    {
    }
}

class SomeClass extends ParentClass
{
    public function someMethod()
    {
    }
}
```

:+1:

<br>

## NoParentMethodCallOnNoOverrideProcessRule

Do not call parent method if no override process

- class: [`Symplify\PHPStanRules\Rules\NoParentMethodCallOnNoOverrideProcessRule`](../src/Rules/NoParentMethodCallOnNoOverrideProcessRule.php)

```php
class SomeClass extends Printer
{
    public function print($nodes)
    {
        return parent::print($nodes);
    }
}
```

:x:

<br>

```php
class SomeClass extends Printer
{
}
```

:+1:

<br>

## NoPropertySetOverrideRule

Property set "%s" is overridden.

- class: [`Symplify\PHPStanRules\Rules\Complexity\NoPropertySetOverrideRule`](../src/Rules/Complexity/NoPropertySetOverrideRule.php)

```php
$someObject = new SomeClass();
$someObject->name = 'First value';

// ...
$someObject->name = 'Second value';
```

:x:

<br>

```php
$someObject = new SomeClass();
$someObject->name = 'First value';
```

:+1:

<br>

## NoProtectedElementInFinalClassRule

Instead of protected element in final class use private element or contract method

- class: [`Symplify\PHPStanRules\Rules\NoProtectedElementInFinalClassRule`](../src/Rules/NoProtectedElementInFinalClassRule.php)

```php
final class SomeClass
{
    protected function run()
    {
    }
}
```

:x:

<br>

```php
final class SomeClass
{
    private function run()
    {
    }
}
```

:+1:

<br>

## NoReadonlyStaticVariableRule

Avoid using static variables, as they can change. Use class constant instead

- class: [`Symplify\PHPStanRules\Rules\Explicit\NoReadonlyStaticVariableRule`](../src/Rules/Explicit/NoReadonlyStaticVariableRule.php)

```php
final class SomeClass
{
    public function run()
    {
        static $list = [1, 2, 3];

        return $list;
    }
}
```

:x:

<br>

```php
final class SomeClass
{
    private const LIST = [1, 2, 3];
    public function run()
    {
        return self::LIST;
    }
}
```

:+1:

<br>

## NoReferenceRule

Use explicit return value over magic &reference

- class: [`Symplify\PHPStanRules\Rules\NoReferenceRule`](../src/Rules/NoReferenceRule.php)

```php
class SomeClass
{
    public function run(&$value)
    {
    }
}
```

:x:

<br>

```php
class SomeClass
{
    public function run($value)
    {
        return $value;
    }
}
```

:+1:

<br>

## NoReturnArrayVariableListRule

Use value object over return of values

- class: [`Symplify\PHPStanRules\Rules\NoReturnArrayVariableListRule`](../src/Rules/NoReturnArrayVariableListRule.php)

```php
class ReturnVariables
{
    public function run($value, $value2): array
    {
        return [$value, $value2];
    }
}
```

:x:

<br>

```php
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

## NoReturnSetterMethodRule

Setter method cannot return anything, only set value

- class: [`Symplify\PHPStanRules\Rules\NoReturnSetterMethodRule`](../src/Rules/NoReturnSetterMethodRule.php)

```php
final class SomeClass
{
    private $name;

    public function setName(string $name): int
    {
        return 1000;
    }
}
```

:x:

<br>

```php
final class SomeClass
{
    private $name;

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
```

:+1:

<br>

## NoStaticPropertyRule

Do not use static property

- class: [`Symplify\PHPStanRules\Rules\NoStaticPropertyRule`](../src/Rules/NoStaticPropertyRule.php)

```php
final class SomeClass
{
    private static $customFileNames = [];
}
```

:x:

<br>

```php
final class SomeClass
{
    private $customFileNames = [];
}
```

:+1:

<br>

## NoTraitRule

Do not use trait, extract to a service and dependency injection instead

- class: [`Symplify\PHPStanRules\Rules\NoTraitRule`](../src/Rules/NoTraitRule.php)

```php
trait SomeTrait
{
    public function run()
    {
    }
}
```

:x:

<br>

```php
class SomeService
{
    public function run(...)
    {
    }
}
```

:+1:

<br>

## NoVoidAssignRule

Assign of void value is not allowed, as it can lead to unexpected results

- class: [`Symplify\PHPStanRules\Rules\Explicit\NoVoidAssignRule`](../src/Rules/Explicit/NoVoidAssignRule.php)

```php
final class SomeClass
{
    public function run()
    {
        $value = $this->getNothing();
    }

    public function getNothing(): void
    {
    }
}
```

:x:

<br>

```php
final class SomeClass
{
    public function run()
    {
        $this->getNothing();
    }

    public function getNothing(): void
    {
    }
}
```

:+1:

<br>

## NoVoidGetterMethodRule

Getter method must return something, not void

- class: [`Symplify\PHPStanRules\Rules\NoVoidGetterMethodRule`](../src/Rules/NoVoidGetterMethodRule.php)

```php
final class SomeClass
{
    public function getData(): void
    {
        // ...
    }
}
```

:x:

<br>

```php
final class SomeClass
{
    public function getData(): array
    {
        // ...
    }
}
```

:+1:

<br>

## PreferredAttributeOverAnnotationRule

Use attribute instead of "%s" annotation

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\PreferredAttributeOverAnnotationRule`](../src/Rules/PreferredAttributeOverAnnotationRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\PreferredAttributeOverAnnotationRule
        tags: [phpstan.rules.rule]
        arguments:
            annotations:
                - Symfony\Component\Routing\Annotation\Route
```

↓

```php
use Symfony\Component\Routing\Annotation\Route;

class SomeController
{
    /**
     * @Route()
     */
    public function action()
    {
    }
}
```

:x:

<br>

```php
use Symfony\Component\Routing\Annotation\Route;

class SomeController
{
    #[Route]
    public function action()
    {
    }
}
```

:+1:

<br>

## PreferredClassRule

Instead of "%s" class/interface use "%s"

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\PreferredClassRule`](../src/Rules/PreferredClassRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\PreferredClassRule
        tags: [phpstan.rules.rule]
        arguments:
            oldToPreferredClasses:
                SplFileInfo: Symplify\SmartFileSystem\SmartFileInfo
```

↓

```php
class SomeClass
{
    public function run()
    {
        return new SplFileInfo('...');
    }
}
```

:x:

<br>

```php
use Symplify\SmartFileSystem\SmartFileInfo;

class SomeClass
{
    public function run()
    {
        return new SmartFileInfo('...');
    }
}
```

:+1:

<br>

## PreferredRawDataInTestDataProviderRule

Code configured at `setUp()` cannot be used in data provider. Move it to `test()` method

- class: [`Symplify\PHPStanRules\Rules\PreferredRawDataInTestDataProviderRule`](../src/Rules/PreferredRawDataInTestDataProviderRule.php)

```php
final class UseDataFromSetupInTestDataProviderTest extends TestCase
{
    private $data;

    protected function setUp()
    {
        $this->data = true;
    }

    public function provideFoo()
    {
        yield [$this->data];
    }

    /**
     * @dataProvider provideFoo
     */
    public function testFoo($value)
    {
        $this->assertTrue($value);
    }
}
```

:x:

<br>

```php
use stdClass;

final class UseRawDataForTestDataProviderTest
{
    private $obj;

    protected function setUp()
    {
        $this->obj = new stdClass;
    }

    public function provideFoo()
    {
        yield [true];
    }

    /**
     * @dataProvider provideFoo
     */
    public function testFoo($value)
    {
        $this->obj->x = $value;
        $this->assertTrue($this->obj->x);
    }
}
```

:+1:

<br>

## PrefixAbstractClassRule

Abstract class name "%s" must be prefixed with "Abstract"

- class: [`Symplify\PHPStanRules\Rules\PrefixAbstractClassRule`](../src/Rules/PrefixAbstractClassRule.php)

```php
abstract class SomeClass
{
}
```

:x:

<br>

```php
abstract class AbstractSomeClass
{
}
```

:+1:

<br>

## PreventDuplicateClassMethodRule

Content of method `"%s()"` is duplicated with method `"%s()"` in "%s" class. Use unique content or service instead

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\PreventDuplicateClassMethodRule`](../src/Rules/PreventDuplicateClassMethodRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\PreventDuplicateClassMethodRule
        tags: [phpstan.rules.rule]
        arguments:
            minimumLineCount: 3
```

↓

```php
class SomeClass
{
    public function someMethod()
    {
        echo 'statement';
        $value = new SmartFinder();
    }
}

class AnotherClass
{
    public function someMethod()
    {
        echo 'statement';
        $differentValue = new SmartFinder();
    }
}
```

:x:

<br>

```php
class SomeClass
{
    public function someMethod()
    {
        echo 'statement';
        $value = new SmartFinder();
    }
}
}
```

:+1:

<br>

## PreventParentMethodVisibilityOverrideRule

Change `"%s()"` method visibility to "%s" to respect parent method visibility.

- class: [`Symplify\PHPStanRules\Rules\PreventParentMethodVisibilityOverrideRule`](../src/Rules/PreventParentMethodVisibilityOverrideRule.php)

```php
class SomeParentClass
{
    public function run()
    {
    }
}

class SomeClass
{
    protected function run()
    {
    }
}
```

:x:

<br>

```php
class SomeParentClass
{
    public function run()
    {
    }
}

class SomeClass
{
    public function run()
    {
    }
}
```

:+1:

<br>

## RegexSuffixInRegexConstantRule

Name your constant with "_REGEX" suffix, instead of "%s"

- class: [`Symplify\PHPStanRules\Rules\RegexSuffixInRegexConstantRule`](../src/Rules/RegexSuffixInRegexConstantRule.php)

```php
class SomeClass
{
    public const SOME_NAME = '#some\s+name#';

    public function run($value)
    {
        $somePath = preg_match(self::SOME_NAME, $value);
    }
}
```

:x:

<br>

```php
class SomeClass
{
    public const SOME_NAME_REGEX = '#some\s+name#';

    public function run($value)
    {
        $somePath = preg_match(self::SOME_NAME_REGEX, $value);
    }
}
```

:+1:

<br>

## RequireAttributeNameRule

Attribute must have all names explicitly defined

- class: [`Symplify\PHPStanRules\Rules\RequireAttributeNameRule`](../src/Rules/RequireAttributeNameRule.php)

```php
use Symfony\Component\Routing\Annotation\Route;

class SomeController
{
    #[Route("/path")]
    public function someAction()
    {
    }
}
```

:x:

<br>

```php
use Symfony\Component\Routing\Annotation\Route;

class SomeController
{
    #[Route(path: "/path")]
    public function someAction()
    {
    }
}
```

:+1:

<br>

## RequireAttributeNamespaceRule

Attribute must be located in "Attribute" namespace

- class: [`Symplify\PHPStanRules\Rules\Domain\RequireAttributeNamespaceRule`](../src/Rules/Domain/RequireAttributeNamespaceRule.php)

```php
// app/Entity/SomeAttribute.php
namespace App\Controller;

#[\Attribute]
final class SomeAttribute
{
}
```

:x:

<br>

```php
// app/Attribute/SomeAttribute.php
namespace App\Attribute;

#[\Attribute]
final class SomeAttribute
{
}
```

:+1:

<br>

## RequireConstantInAttributeArgumentRule

Argument "%s" must be a constant

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\RequireConstantInAttributeArgumentRule`](../src/Rules/RequireConstantInAttributeArgumentRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\RequireConstantInAttributeArgumentRule
        tags: [phpstan.rules.rule]
        arguments:
            attributeWithNames:
                Symfony\Component\Routing\Annotation\Route:
                    - name
```

↓

```php
use Symfony\Component\Routing\Annotation\Route;

final class SomeClass
{
    #[Route(path: '/archive', name: 'blog_archive')]
    public function __invoke()
    {
    }
}
```

:x:

<br>

```php
use Symfony\Component\Routing\Annotation\Route;

final class SomeClass
{
    #[Route(path: '/archive', name: RouteName::BLOG_ARCHIVE)]
    public function __invoke()
    {
    }
}
```

:+1:

<br>

## RequireConstantInMethodCallPositionRule

Parameter argument on position %d must use constant

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\Enum\RequireConstantInMethodCallPositionRule`](../src/Rules/Enum/RequireConstantInMethodCallPositionRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\Enum\RequireConstantInMethodCallPositionRule
        tags: [phpstan.rules.rule]
        arguments:
            requiredLocalConstantInMethodCall:
                SomeType:
                    someMethod:
                        - 0
```

↓

```php
class SomeClass
{
    public function someMethod(SomeType $someType)
    {
        $someType->someMethod('hey');
    }
}
```

:x:

<br>

```php
class SomeClass
{
    private const HEY = 'hey'

    public function someMethod(SomeType $someType)
    {
        $someType->someMethod(self::HEY);
    }
}
```

:+1:

<br>

## RequireExceptionNamespaceRule

`Exception` must be located in "Exception" namespace

- class: [`Symplify\PHPStanRules\Rules\Domain\RequireExceptionNamespaceRule`](../src/Rules/Domain/RequireExceptionNamespaceRule.php)

```php
// app/Controller/SomeException.php
namespace App\Controller;

final class SomeException extends Exception
{

}
```

:x:

<br>

```php
// app/Exception/SomeException.php
namespace App\Exception;

final class SomeException extends Exception
{
}
```

:+1:

<br>

## RequireNewArgumentConstantRule

New expression argument on position %d must use constant over value

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\Enum\RequireNewArgumentConstantRule`](../src/Rules/Enum/RequireNewArgumentConstantRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\Enum\RequireNewArgumentConstantRule
        tags: [phpstan.rules.rule]
        arguments:
            constantArgByNewByType:
                Symfony\Component\Console\Input\InputOption:
                    - 2
```

↓

```php
use Symfony\Component\Console\Input\InputOption;

$inputOption = new InputOption('name', null, 2);
```

:x:

<br>

```php
use Symfony\Component\Console\Input\InputOption;

$inputOption = new InputOption('name', null, InputOption::VALUE_REQUIRED);
```

:+1:

<br>

## RequireSpecificReturnTypeOverAbstractRule

Provide more specific return type "%s" over abstract one

- class: [`Symplify\PHPStanRules\Rules\Explicit\RequireSpecificReturnTypeOverAbstractRule`](../src/Rules/Explicit/RequireSpecificReturnTypeOverAbstractRule.php)

```php
final class IssueControlFactory
{
    public function create(): Control
    {
        return new IssueControl();
    }
}

final class IssueControl extends Control
{
}
```

:x:

<br>

```php
final class IssueControlFactory
{
    public function create(): IssueControl
    {
        return new IssueControl();
    }
}

final class IssueControl extends Control
{
}
```

:+1:

<br>

## RequireStringArgumentInConstructorRule

Use quoted string in constructor "new `%s()"` argument on position %d instead of "::class". It prevent scoping of the class in building prefixed package.

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\RequireStringArgumentInConstructorRule`](../src/Rules/RequireStringArgumentInConstructorRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\RequireStringArgumentInConstructorRule
        tags: [phpstan.rules.rule]
        arguments:
            stringArgPositionsByType:
                SomeClass:
                    - 0
```

↓

```php
class AnotherClass
{
    public function run()
    {
        new SomeClass(YetAnotherClass:class);
    }
}
```

:x:

<br>

```php
class AnotherClass
{
    public function run()
    {
        new SomeClass('YetAnotherClass');
    }
}
```

:+1:

<br>

## RequireStringRegexMatchKeyRule

Regex must use string named capture groups instead of numeric

- class: [`Symplify\PHPStanRules\Rules\RequireStringRegexMatchKeyRule`](../src/Rules/RequireStringRegexMatchKeyRule.php)

```php
use Nette\Utils\Strings;

class SomeClass
{
    private const REGEX = '#(a content)#';

    public function run()
    {
        $matches = Strings::match('a content', self::REGEX);
        if ($matches) {
            echo $matches[1];
        }
    }
}
```

:x:

<br>

```php
use Nette\Utils\Strings;

class SomeClass
{
    private const REGEX = '#(?<content>a content)#';

    public function run()
    {
        $matches = Strings::match('a content', self::REGEX);
        if ($matches) {
            echo $matches['content'];
        }
    }
}
```

:+1:

<br>

## RequireThisCallOnLocalMethodRule

Use "$this-><method>()" instead of "self::<method>()" to call local method

- class: [`Symplify\PHPStanRules\Rules\RequireThisCallOnLocalMethodRule`](../src/Rules/RequireThisCallOnLocalMethodRule.php)

```php
class SomeClass
{
    public function run()
    {
        self::execute();
    }

    private function execute()
    {
    }
}
```

:x:

<br>

```php
class SomeClass
{
    public function run()
    {
        $this->execute();
    }

    private function execute()
    {
    }
}
```

:+1:

<br>

## RequireThisOnParentMethodCallRule

Use "$this-><method>()" instead of "parent::<method>()" unless in the same named method

- class: [`Symplify\PHPStanRules\Rules\RequireThisOnParentMethodCallRule`](../src/Rules/RequireThisOnParentMethodCallRule.php)

```php
class SomeParentClass
{
    public function run()
    {
    }
}

class SomeClass extends SomeParentClass
{
    public function go()
    {
        parent::run();
    }
}
```

:x:

<br>

```php
class SomeParentClass
{
    public function run()
    {
    }
}

class SomeClass extends SomeParentClass
{
    public function go()
    {
        $this->run();
    }
}
```

:+1:

<br>

## RequireUniqueEnumConstantRule

Enum constants "%s" are duplicated. Make them unique instead

- class: [`Symplify\PHPStanRules\Rules\Enum\RequireUniqueEnumConstantRule`](../src/Rules/Enum/RequireUniqueEnumConstantRule.php)

```php
use MyCLabs\Enum\Enum;

class SomeClass extends Enum
{
    private const YES = 'yes';

    private const NO = 'yes';
}
```

:x:

<br>

```php
use MyCLabs\Enum\Enum;

class SomeClass extends Enum
{
    private const YES = 'yes';

    private const NO = 'no';
}
```

:+1:

<br>

## RequiredAbstractClassKeywordRule

Class name starting with "Abstract" must have an `abstract` keyword

- class: [`Symplify\PHPStanRules\Rules\RequiredAbstractClassKeywordRule`](../src/Rules/RequiredAbstractClassKeywordRule.php)

```php
class AbstractClass
{
}
```

:x:

<br>

```php
abstract class AbstractClass
{
}
```

:+1:

<br>

## RespectPropertyTypeInGetterReturnTypeRule

This getter method does not respect property type

- class: [`Symplify\PHPStanRules\Rules\StrictTypes\RespectPropertyTypeInGetterReturnTypeRule`](../src/Rules/StrictTypes/RespectPropertyTypeInGetterReturnTypeRule.php)

```php
final class SomeClass
{
    private $value = [];

    public function setValues(array $values)
    {
        $this->values = $values;
    }

    public function getValues(): array|null
    {
        return $this->values;
    }
}
```

:x:

<br>

```php
final class SomeClass
{
    private $value = [];

    public function setValues(array $values)
    {
        $this->values = $values;
    }

    public function getValues(): array
    {
        return $this->values;
    }
}
```

:+1:

<br>

## SeeAnnotationToTestRule

Class "%s" is missing `@see` annotation with test case class reference

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\SeeAnnotationToTestRule`](../src/Rules/SeeAnnotationToTestRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\SeeAnnotationToTestRule
        tags: [phpstan.rules.rule]
        arguments:
            requiredSeeTypes:
                - Rule
```

↓

```php
class SomeClass extends Rule
{
}
```

:x:

<br>

```php
/**
 * @see SomeClassTest
 */
class SomeClass extends Rule
{
}
```

:+1:

<br>

## SuffixInterfaceRule

Interface must be suffixed with "Interface" exclusively

- class: [`Symplify\PHPStanRules\Rules\SuffixInterfaceRule`](../src/Rules/SuffixInterfaceRule.php)

```php
interface SomeClass
{
}
```

:x:

<br>

```php
interface SomeInterface
{
}
```

:+1:

<br>

## SuffixTraitRule

Trait must be suffixed by "Trait" exclusively

- class: [`Symplify\PHPStanRules\Rules\SuffixTraitRule`](../src/Rules/SuffixTraitRule.php)

```php
trait SomeClass
{
}
```

:x:

<br>

```php
trait SomeTrait
{
}
```

:+1:

<br>

## SwitchToMatchSpotterRule

Switch construction can be replace with more robust `match()`

- class: [`Symplify\PHPStanRules\Rules\Spotter\SwitchToMatchSpotterRule`](../src/Rules/Spotter/SwitchToMatchSpotterRule.php)

```php
switch ($key) {
    case 1:
        return 100;
    case 2:
        return 200;
    default:
        return 300;
};
```

:x:

<br>

```php
return match($key) {
    1 => 100,
    2 => 200,
    default => 300,
};
```

:+1:

<br>

## UppercaseConstantRule

Constant "%s" must be uppercase

- class: [`Symplify\PHPStanRules\Rules\UppercaseConstantRule`](../src/Rules/UppercaseConstantRule.php)

```php
final class SomeClass
{
    public const some = 'value';
}
```

:x:

<br>

```php
final class SomeClass
{
    public const SOME = 'value';
}
```

:+1:

<br>

## ValueObjectOverArrayShapeRule

Instead of array shape, use value object with specific types in constructor and getters

- class: [`Symplify\PHPStanRules\Rules\Explicit\ValueObjectOverArrayShapeRule`](../src/Rules/Explicit/ValueObjectOverArrayShapeRule.php)

```php
/**
 * @return array{line: int}
 */
function createConfiguration()
{
    return ['line' => 100];
}
```

:x:

<br>

```php
function createConfiguration()
{
    return new Configuration(100);
}

final class Configuration
{
    public function __construct(
        private int $line
    ) {
    }

    public function getLine(): int
    {
        return $this->line;
    }
}
```

:+1:

<br>
