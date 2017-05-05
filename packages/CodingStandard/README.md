# Coding Standard

[![Build Status](https://img.shields.io/travis/Symplify/CodingStandard/master.svg?style=flat-square)](https://travis-ci.org/Symplify/CodingStandard)
[![Downloads](https://img.shields.io/packagist/dt/symplify/coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/coding-standard)

Set of PHP_CodeSniffer rules for Symplify packages.

## Install

```bash
composer require symplify/coding-standard --dev
```

## Usage

To use, check [EasyCodingStandard](/packages/EasyCodingStandard/README.md).

---

Rules Overview...

### [Traits are forbidden. Prefer service and constructor injection](/src/Sniffs/Architecture/ForbiddenTraitSniff.php)

:x:

```php
trait SomeTrait
{
}
```

### [Implementation of interface should only contain its methods](/src/Sniffs/Classes/EqualInterfaceImplementationSniff.php)

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

### [Controller should have max.1 render method.](/src/Sniffs/Classes/ControllerRenderMethodLimitSniff.php)

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

### [Controller has to contain __invoke() method](/src/Sniffs/Classes/InvokableControllerSniff.php)

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

### [Non-abstract class that implements interface should be final](/src/Sniffs/Classes/FinalInterfaceSniff.php)

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

### [Block comment should be used instead of one liner](/src/Sniffs/Commenting/BlockPropertyCommentSniff.php) 

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


### [Constant should have docblock comment](/src/Sniffs/Commenting/VarConstantCommentSniff.php)

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

### [Property should have docblock comment](/src/Sniffs/Commenting/VarPropertyCommentSniff.php)

:x:

```php
class SomeClass
{
    private $someProperty;
}
```

:+1:

```php
class SomeClass
{
    /**
     * @var int
     */
    private $someProperty;
}
```


### [New class statement should not have empty parentheses.](/src/Sniffs/ControlStructures/NewClassSniff.php)

:x:

```php
$file = new File();
```
 
:+1:

```php
$file = new File;
$directory = new Directory([$file]);
```

### [This comment is valid code. Uncomment it or remove it.](/src/Sniffs/Debug/CommentedOutCodeSniff.php)

:x:

```php
// $file = new File;
// $directory = new Diretory([$file]);
```

### [Debug functions should not be left in the code](/src/Sniffs/Debug/DebugFunctionCallSniff.php)

:x:

```php
dump($value);
```

### [Class name after new/instanceof should not start with slash.](/src/Sniffs/Namespaces/ClassNamesWithoutPreSlashSniff.php)

:x:

```php
$file = new \File;
```
 
:+1:

```php
$file = new File;
```

### [Abstract class should have prefix "Abstract"](/src/Sniffs/Naming/AbstractClassNameSniff.php)

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

### [Interface should have suffix "Interface"](/src/Sniffs/Naming/InterfaceNameSniff.php)

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


### [Trait should have suffix "Trait"](/src/Sniffs/Naming/TraitNameSniff.php)

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



## Contributing

Send [issue](https://github.com/Symplify/Symplify/issues) or [pull-request](https://github.com/Symplify/Symplify/pulls) to main repository.