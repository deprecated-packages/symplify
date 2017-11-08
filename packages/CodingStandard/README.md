# Coding Standard

[![Build Status](https://img.shields.io/travis/Symplify/CodingStandard/master.svg?style=flat-square)](https://travis-ci.org/Symplify/CodingStandard)
[![Downloads](https://img.shields.io/packagist/dt/symplify/coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/coding-standard)
[![Subscribe](https://img.shields.io/badge/subscribe-to--releases-green.svg?style=flat-square)](https://libraries.io/packagist/symplify%2Fcoding-standard)

Set of PHP_CodeSniffer Sniffs and PHP-CS-Fixer Fixers used by Symplify projects.

**They run best with [EasyCodingStandard](https://github.com/Symplify/EasyCodingStandard)**.


## Install

```bash
composer require symplify/coding-standard --dev
```

## Rules Overview


### Indexed PHP arrays should have 1 item per line
 
- class: [`Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer`](/src/Fixer/ArrayNotation/StandaloneLineInMultilineArrayFixer.php)

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


### Last property and first method must be separated by 1 blank line(s).

- class: [`Symplify\CodingStandard\Fixer\ClassNotation\LastPropertyAndFirstMethodSeparationFixer`](/src/Fixer/ClassNotation/LastPropertyAndFirstMethodSeparationFixer.php)

:x:

```php
class SomeClass
{
    public $lastProperty;
    public function someFunction()
    {

    }
}
```

:+1:

```php
class SomeClass
{
    public $lastProperty;

    public function someFunction()
    {

    }
}
```

This checker requires 1 space by default. But if you need, you can **configure it**:


```yaml
# easy-coding-standard.neon
checkers:
    Symplify\CodingStandard\Fixer\ClassNotation\LastPropertyAndFirstMethodSeparationFixer:
        space_count: 2 # 1 by default
```



### Properties and constants must be separated by 1 blank line(s).

- class: [`Symplify\CodingStandard\Fixer\ClassNotation\PropertyAndConstantSeparationFixer`](/src/Fixer/ClassNotation/PropertyAndConstantSeparationFixer.php)

:x:

```php
class SomeClass
{
    public $someProperty;
    public $anotherProperty;
}
```

:+1:

```php
class SomeClass
{
    public $someProperty;

    public $anotherProperty;
}
```

This checker requires 1 space by default. But if you need, you can **configure it**:


```yaml
# easy-coding-standard.neon
checkers:
    Symplify\CodingStandard\Fixer\ClassNotation\PropertyAndConstantSeparationFixer:
        space_count: 2 # 1 by default
```



### Variables created with `$container->get(SomeService::class)` should have annotation, so every IDE supports autocomplete without any plugins

- class: [`Symplify\CodingStandard\Fixer\Commenting\AnnotateMagicContainerGetterFixer`](/src/Fixer/Commenting/AnnotateMagicContainerGetterFixer.php)

:x:

```php
class SomeTest extends ContainerAwareTestCase
{
    protected function setUp(): void
    {
        $someService = $this->container->get(SomeType::class);
        $someService->unknownMethod();
    }
}
```

:+1:

```php
class SomeTest extends ContainerAwareTestCase
{
    protected function setUp(): void
    {
        /** @var SomeType $someService */
        $someService = $this->container->get(SomeType::class);
        $someService->knownMethod();
    }
}
```


### Include/Require should be followed by absolute path

- class: [`Symplify\CodingStandard\Fixer\ControlStructure\RequireFollowedByAbsolutePathFixer`](/src/Fixer/ControlStructure/RequireFollowedByAbsolutePathFixer.php)

:x:

```php
require 'vendor/autoload.php';
```

:+1:

```php
require __DIR__.'/vendor/autoload.php';
```


### Magic PHP methods (`__*()`) should respect their casing form

- class: [`Symplify\CodingStandard\Fixer\Naming\MagicMethodsNamingFixer`](/src/Fixer/Naming/MagicMethodsNamingFixer.php)

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


### Property name should match its type, if possible

- class: [`Symplify\CodingStandard\Fixer\Naming\PropertyNameMatchingTypeFixer`](/src/Fixer/Naming/PropertyNameMatchingTypeFixer.php)

:x:

```php
public function __construct(EntityManagerInterface $eventManager)
{
    $this->eventManager = $eventManager;
}
```

:+1:

```php
public function __construct(EntityManagerInterface $entityManager)
{
    $this->entityManager = $entityManager;
}
```

This checker ignores few **system classes like `std*` or `Spl*` by default**. In case want to skip more classes, you can **configure it**:

```yaml
# easy-coding-standard.neon
checkers:
    Symplify\CodingStandard\Fixer\Naming\PropertyNameMatchingTypeFixer:
        extra_skipped_classes:
            - 'MyApp*' # accepts anything like fnmatch
```


### `::class` references should be used over string for classes and interfaces
 
 
- class: [`Symplify\CodingStandard\Fixer\Php\ClassStringToClassConstantFixer`](/src/Fixer/Php/ClassStringToClassConstantFixer.php)

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

- class: [`Symplify\CodingStandard\Fixer\Commenting\BlockPropertyCommentFixer`](/src/Fixer/Commenting/BlockPropertyCommentFixer.php)

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


### New class statement should not have empty parentheses

- class: [`Symplify\CodingStandard\Sniffs\ControlStructures\NewClassSniff`](/src/Sniffs/ControlStructures/NewClassSniff.php)

:x:

```php
$file = new File();
```
 
:+1:

```php
$file = new File;
$directory = new Directory([$file]);
```


### There should not be comments with valid code

- class: [`Symplify\CodingStandard\Sniffs\Debug\CommentedOutCodeSniff`](/src/Sniffs/Debug/CommentedOutCodeSniff.php)

:x:

```php
// $file = new File;
// $directory = new Diretory([$file]);
```


### Debug functions should not be left in the code

- class: [`Symplify\CodingStandard\Sniffs\Debug\DebugFunctionCallSniff`](/src/Sniffs/Debug/DebugFunctionCallSniff.php)

:x:

```php
dump($value);
```


### Use service and constructor injection rather than instantiation with new

- class: [`Symplify\CodingStandard\Sniffs\DependencyInjection\NoClassInstantiationSniff`](/src/Sniffs/DependencyInjection/NoClassInstantiationSniff.php)

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

This checkers ignores by default some classes, see `$allowedClasses` property.

In case want to exclude more classes, you can **configure it** with class or pattern using [`fnmatch`](http://php.net/manual/en/function.fnmatch.php):

```yaml
# easy-coding-standard.neon
checkers:
    Symplify\CodingStandard\Fixer\DependencyInjection\NoClassInstantiationSniff:
        extraAllowedClasses:
            - 'PhpParser\Node\*'
```

Doctrine entities are skipped as well. You can disable that by:

```yaml
# easy-coding-standard.neon
checkers:
    Symplify\CodingStandard\Fixer\DependencyInjection\NoClassInstantiationSniff:
        includeEntities: true
```


### Abstract class should have prefix "Abstract"

- class: [`Symplify\CodingStandard\Sniffs\Naming\AbstractClassNameSniff`](/src/Sniffs/Naming/AbstractClassNameSniff.php)

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


### Properties should be used instead of dynamically defined properties

- class: [`Symplify\CodingStandard\Sniffs\Property\DynamicPropertySniff`](/src/Sniffs/Property/DynamicPropertySniff.php)

:x:

```php
class SomeClass
{
    public function __construct()
    {
        $this->someProperty = 5;
    }
}

```

:+1:

```php
class SomeClass
{
    /**
     * @var int
     */
    public $someProperty;

    public function __construct()
    {
        $this->someProperty = 5;
    }
}
```




## Contributing

Send [issue](https://github.com/Symplify/Symplify/issues) or [pull-request](https://github.com/Symplify/Symplify/pulls) to main repository.
