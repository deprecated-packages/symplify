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

- Rules with :wrench: are configurable.


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


### Last property and first method must be separated by 1 blank line :wrench:

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



### There should not be empty PHPDoc blocks

Just like `PhpCsFixer\Fixer\Phpdoc\NoEmptyPhpdocFixer`, but this one removes all doc block lines. 

- class: [`Symplify\CodingStandard\Fixer\Commenting\RemoveEmptyDocBlockFixer`](/src/Fixer/Commenting/RemoveEmptyDocBlockFixer.php)

:x:

```php
/**
 */
public function someMethod()
{
}
```

:+1:

```php
public function someMethod()
{
}
```


### Block comment should only contain useful information about types :wrench: 

- class: [`Symplify\CodingStandard\Fixer\Commenting\RemoveUselessDocBlockFixer`](/src/Fixer/Commenting/RemoveUselessDocBlockFixer.php)

:x:

```php
/**
 * @param int $value
 * @param $anotherValue
 * @param SomeType $someService
 * @return array
 */
public function setCount(int $value, $anotherValue, SomeType $someService): array
{
}
```

:+1:

```php
/**
 */
public function setCount(int $value, $anotherValue, SomeType $someService): array
{
}
```

This checker removes 'mixed' and 'object' doc types by default. But if you need, you can **configure it**:


```yaml
# easy-coding-standard.neon
checkers:
    Symplify\CodingStandard\Fixer\Commenting\RemoveUselessDocBlockFixer:
        useful_types: ['mixed', 'object']
```


### Block comment should not have 2 empty lines in a row

- class: [`Symplify\CodingStandard\Fixer\Commenting\RemoveSuperfluousDocBlockWhitespaceFixer`](/src/Fixer/Commenting/RemoveSuperfluousDocBlockWhitespaceFixer.php)

:x:

```php
/**
 * @param int $value
 *
 *
 * @return array
 */
public function setCount($value)
{
}
```

:+1:

```php
/**
 * @param int $value
 *
 * @return array
 */
public function setCount($value)
{
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


### Types should not be referenced via a fully/partially qualified name, but via a use statement :wrench:

- class: [`Symplify\CodingStandard\Fixer\Import\ImportNamespacedNameFixer`](/src/Fixer/Import/ImportNamespacedNameFixer.php)


:x:

```php
namespace SomeNamespace;

class SomeClass
{
    public function someMethod()
    {
        return new \AnotherNamespace\AnotherType;
    }
}
```


:+1:

```php
namespace SomeNamespace;

use AnotherNamespace\AnotherType;

class SomeClass
{
    public function someMethod()
    {
        return new AnotherType;
    }
}
```


This checker imports single name classes like `\Twig_Extension` or `\SplFileInfo` by default. But if you need, you can **configure it**:


```yaml
# easy-coding-standard.neon
checkers:
    Symplify\CodingStandard\Fixer\Import\ImportNamespacedNameFixer:
        allow_single_names: true # false by default
```

Duplicated class names are uniquized by vendor name:


```php
<?php declare(strict_types=1);

namespace SomeNamespace;

use Nette\Utils\Finder as NetteFinder;
use Symfony\Finder\Finder;

class SomeClass
{
    public function create(NetteFinder $someClass)
    {
        return new Finder;
    }
}
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


### Property name should match its type, if possible :wrench:

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


### `::class` references should be used over string for classes and interfaces :wrench:
 
 
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

- class: [`Symplify\CodingStandard\Fixer\Property\ArrayPropertyDefaultValueFixer`](/src/Fixer/Property/ArrayPropertyDefaultValueFixer.php)


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
    
    public function run()
    {
        foreach ($this->apples as $mac) {
            // ...
        }
    }
}
```


### Strict type declaration has to be followed by empty line

- class: [`Symplify\CodingStandard\Fixer\Strict\BlankLineAfterStrictTypesFixer`](/src/Fixer/Strict/BlankLineAfterStrictTypesFixer.php)


:x:

``` php
<?php declare(strict_types=1);
namespace SomeNamespace;
```

:+1:

``` php
<?php declare(strict_types=1);

namespace SomeNamespace;
```


### Non-abstract class that implements interface should be final :wrench:

*Except for Doctrine entities, they cannot be final.*


- class: [`Symplify\CodingStandard\Fixer\Solid\FinalInterfaceFixer`](/src/Fixer/Solid/FinalInterfaceFixer.php)

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


In case want check this only for specific interfaces, you can **configure them**:

```yaml
# easy-coding-standard.neon
checkers:
    Symplify\CodingStandard\Fixer\Solid\FinalInterfaceFixer:
        onlyInterfaces:
            - 'Symfony\Component\EventDispatcher\EventSubscriberInterface'
            - 'Nette\Application\IPresenter'
```


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


### Use service and constructor injection rather than instantiation with new :wrench:

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


## Brave Checkers

### Possible Unused Public Method

- class: [`Symplify\CodingStandard\Sniffs\DeadCode\UnusedPublicMethodSniff`](/src/Sniffs/DeadCode/UnusedPublicMethodSniff.php)
  
- **Requires ECS due *double run* feature**.


:x:

```php
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
$someObject->unusedMethod();
```


:+1:

```php
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

Send [issue](https://github.com/Symplify/Symplify/issues) or [pull-request](https://github.com/Symplify/Symplify/pulls) to main repository.
