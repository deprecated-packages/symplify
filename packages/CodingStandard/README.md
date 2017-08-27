# Coding Standard

[![Build Status](https://img.shields.io/travis/Symplify/CodingStandard/master.svg?style=flat-square)](https://travis-ci.org/Symplify/CodingStandard)
[![Downloads](https://img.shields.io/packagist/dt/symplify/coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/coding-standard)

Set of PHP_CodeSniffer Sniffs and PHP-CS-Fixer Fixers used by Symplify projects.

**They run best with [EasyCodingStandard](https://github.com/Symplify/EasyCodingStandard)**.


## Install

```bash
composer require symplify/coding-standard --dev
```

## Rules Overview


### Indexed PHP arrays should have 1 item per line
 
- class: [`Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer`](/src/Fixer/ArrayNotation/StandaloneLineInMultilineArrayFixer.php)
- This checker uses *[PHP-CS-Fixer](https://github.com/friendsofphp/php-cs-fixer)*

:x:

```php
$friends = [1 => 'Peter', 2 => 'Paul'];
```

:+1:

```php
$friends = [
    1 => 'Peter',
    2 => 'Paul'
];
```


### Constructor injection should be used instead of `@inject` annotations 

- class: [`Symplify\CodingStandard\Fixer\DependencyInjection\InjectToConstructorInjectionFixer`](/src/Fixer/DependencyInjection/InjectToConstructorInjectionFixer.php)
- This checker uses *[PHP-CS-Fixer](https://github.com/friendsofphp/php-cs-fixer)*

:x:

```php
class SomeClass
{
    /**
     * @inject
     * @var RequiredDependencyClass
     */
    public $requiredDependencyClass;
}
```

:+1:

```php
class SomeClass
{
    /**
     * @var RequiredDependencyClass
     */
    private $requiredDependencyClass;
    
    public function __construct(RequiredDependencyClass $requiredDependencyClass)
    {
        $this->requiredDependencyClass = $requiredDependencyClass;
    }
}
```


### Magic PHP methods (`__*()`) should respect their casing form

- class: [`Symplify\CodingStandard\Fixer\Naming\MagicMethodsNamingFixer`](/src/Fixer/Naming/MagicMethodsNamingFixer.php)
- This checker uses *[PHP-CS-Fixer](https://github.com/friendsofphp/php-cs-fixer)*

:x:

```php
class SomeClass
{
    public function __CONSTRUCT()
    {
    }
}
```

:+1:

```php
class SomeClass
{
    public function __construct()
    {
    }
}
```


### `::class` references should be used over string for classes and interfaces
 
 
- class: [`Symplify\CodingStandard\Fixer\Php\ClassStringToClassConstantFixer`](/src/Fixer/Php/ClassStringToClassConstantFixer.php)
- This checker uses *[PHP-CS-Fixer](https://github.com/friendsofphp/php-cs-fixer)*

:x:

```php
$className = 'DateTime';
```

:+1:

```php
$className = DateTime::class;
```

This checker takes **only existing classes by default**. In case want to check another code not loaded by local composer, you can **configure it**:

```yaml
# easy-coding-standard.neon
checkers:
    Symplify\CodingStandard\Fixer\Php\ClassStringToClassConstantFixer:
        class_must_exist: false # true by default
```

### Array property should have default value, to prevent undefined array issues

- class: [`Symplify\CodingStandard\Fixer\Property/ArrayPropertyDefaultValueFixer`](/src/Fixer/Property/ArrayPropertyDefaultValueFixer.php)
- This checker uses *[PHP-CS-Fixer](https://github.com/friendsofphp/php-cs-fixer)*


:x:

``` php
class SomeClass
{
    /**
     * @var string[]
     */
    public $apples;
    
    public function run()
    {
        foreach ($this->apples as $mac) {
            // ...
        }
    }
}
```


:+1:


``` php
class SomeClass
{
    /**
     * @var string[]
     */
    public $apples = [];
}
```


### Implementation of interface should only contain its methods

- class: [`Symplify\CodingStandard\Sniffs\Classes\EqualInterfaceImplementationSniff`](/src/Sniffs/Classes/EqualInterfaceImplementationSniff.php)
- `Symplify.Classes.EqualInterfaceImplementation`

:x:

```php
interface SomeInterface
{
    public function run(): void;
}

final class SomeClass implements SomeInterface
{
    public function run(): void
    {
    }
    
    public function extra(): void
    {
    }
}
```

:+1:

```php
interface SomeInterface
{
    public function run(): void;
}

final class SomeClass implements SomeInterface
{
    public function run(): void
    {
    }
}
```


### Non-abstract class that implements interface should be final

- class: [`Symplify\CodingStandard\Sniffs\Classes\FinalInterfaceSniff`](/src/Sniffs/Classes/FinalInterfaceSniff.php)
- `Symplify.Classes.FinalInterface`

:x:

```php
class SomeClass implements SomeInterface
{
}
```

:+1:

```php
final class SomeClass implements SomeInterface
{
}
```

- Except for Doctrine entities, they cannot be final.

### Block comment should be used instead of one liner

- class: [`Symplify\CodingStandard\Sniffs\Commenting\BlockPropertyCommentSniff`](/src/Sniffs/Commenting/BlockPropertyCommentSniff.php)
- `Symplify.Commenting.BlockPropertyComment`

:x:

```php
class SomeClass
{
    /** @var int */
    public $count;
}
```

:+1:

```php
class SomeClass
{
    /**
     * @var int 
     */
    public $count;
}
```


### Constant should have docblock comment

- class: [`Symplify\CodingStandard\Sniffs\Commenting\VarConstantCommentSniff`](/src/Sniffs/Commenting/VarConstantCommentSniff.php)
- `Symplify.Commenting.VarConstantComment`

:x:

```php
class SomeClass
{
    private const EMPATH_LEVEL = 55;
}
```

:+1:

```php
class SomeClass
{
    /**
     * @var int
     */
    private const EMPATH_LEVEL = 55;
}
```


### Controller should have max. 1 render method

- class: [`Symplify\CodingStandard\Sniffs\Classes\ControllerRenderMethodLimitSniff`](/src/Sniffs/Classes/ControllerRenderMethodLimitSniff.php)
- `Symplify.Classes.ControllerRenderMethodLimit`

:x:

```php
final class Controller
{
    public function defaultAction()
    {
    }
    
    public function listAction()
    {
    }
}
```

:+1:

```php
final class Controller
{
    public function defaultAction()
    {
    }
}
```

### Controller has to contain `__invoke()` method
 
- class: [`Symplify\CodingStandard\Sniffs\Classes\InvokableControllerSniff`](/src/Sniffs/Classes/InvokableControllerSniff.php)
- `Symplify.Classes.InvokableController`

:x:

```php
final class Controller
{
    public function defaultAction()
    {
    }
}
```

:+1:

```php
final class Controller
{
    public function __invoke()
    {
    }
}
```


### New class statement should not have empty parentheses

- class: [`Symplify\CodingStandard\Sniffs\ControlStructures\NewClassSniff`](/src/Sniffs/ControlStructures/NewClassSniff.php)
- `Symplify.ControlStructures.NewClass`

:x:

```php
$file = new File();
```
 
:+1:

```php
$file = new File;
$directory = new Directory([$file]);
```

### There should comments with valid code

- class: [`Symplify\CodingStandard\Sniffs\Debug\CommentedOutCodeSniff`](/src/Sniffs/Debug/CommentedOutCodeSniff.php)
- `Symplify.Debug.CommentedOutCode`

:x:

```php
// $file = new File;
// $directory = new Diretory([$file]);
```

### Debug functions should not be left in the code

- class: [`Symplify\CodingStandard\Sniffs\Debug\DebugFunctionCallSniff`](/src/Sniffs/Debug/DebugFunctionCallSniff.php)
- `Symplify.Debug.DebugFunctionCall`

:x:

```php
dump($value);
```



### Use service and constructor injection rather than instantiation with new

- class: [`Symplify\CodingStandard\Sniffs\DependencyInjection\NoClassInstantiationSniff`](/src/Sniffs/DependencyInjection/NoClassInstantiationSniff.php)
- `Symplify.DependencyInjection.NoClassInstantiation`

:x:

```php
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
class SomeController
{
   public function renderEdit(array $data)
   {
        $this->database->save($data);
   }
}
```

This checkers ignores by default:

- `DateTime`
- `*Exception` (@todo)

In case want to exclude more classes, you can **configure it**:

```yaml
# easy-coding-standard.neon
checkers:
    Symplify\CodingStandard\Fixer\DependencyInjection\NoClassInstantiationSniff:
        allowedClasses:
            - DateTime
            - App\ProductModule\Product
```

(@todo)

Doctrine entities are skipped as well. You can disable that by:

```yaml
# easy-coding-standard.neon
checkers:
    Symplify\CodingStandard\Fixer\DependencyInjection\NoClassInstantiationSniff:
        includeEntities: true
```



### Abstract class should have prefix "Abstract"

- class: [`Symplify\CodingStandard\Sniffs\Naming\AbstractClassNameSniff`](/src/Sniffs/Naming/AbstractClassNameSniff.php)
- `Symplify.Naming.AbstractClassName`

:x:

```php
abstract class SomeClass
{
}
```

:+1:

```php
abstract class AbstractSomeClass
{
}
```


### Exception should have suffix "Exception"

For *PHP_CodeSniffer*:

- class: [`Symplify\CodingStandard\Sniffs\Naming\ExceptionNameSniff`](/src/Sniffs/Naming/ExceptionNameSniff.php)
- `Symplify.Naming.ExceptionName`

or *PHP-CS-Fixer*:

- class: [`Symplify\CodingStandard\Fixer\Naming\ExceptionNameSniff`](/src/Fixer/Naming/ExceptionNameFixer.php)

:x:

```php
class SomeClass extends Exception
{
}
```

:+1:

```php
class SomeClassException extends Exception
{
}
```

### Interface should have suffix "Interface"

- class: [`Symplify\CodingStandard\Sniffs\Naming\InterfaceNameSniff`](/src/Sniffs/Naming/InterfaceNameSniff.php)
- `Symplify.Naming.InterfaceName`

:x:

```php
interface Some
{
}
```

:+1:

```php
interface SomeInterface
{
}
```


### Trait should have suffix "Trait"

- class: [`Symplify\CodingStandard\Sniffs\Naming\TraitNameSniff`](/src/Sniffs/Naming/TraitNameSniff.php)
- `Symplify.Naming.TraitName`

:x:

```php
trait Some
{
}
```

:+1:

```php
trait SomeTrait
{
}
```


### Non-abstract class that extends TestCase should be final

- class: [`Symplify\CodingStandard\Sniffs\PHPUnit\FinalTestCaseSniff`](/src/Sniffs/PHPUnit/FinalTestCaseSniff.php)
- `Symplify.PHPUnit.FinalTestCase`

:x:

```php
use PHPUnit\Framework\TestCase;

class SomeTest extends TestCase
{
}
```

:+1:

```php
use PHPUnit\Framework\TestCase;

final class SomeTest extends TestCase
{
}
```


## Contributing

Send [issue](https://github.com/Symplify/Symplify/issues) or [pull-request](https://github.com/Symplify/Symplify/pulls) to main repository.
