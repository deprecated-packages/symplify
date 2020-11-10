# Rules Overview

## ClassLikeCognitiveComplexityRule

Cognitive complexity of class/trait must be under specific limit

- class: `Symplify\PHPStanRules\CognitiveComplexity\Rules\ClassLikeCognitiveComplexityRule`

```php
declare(strict_types=1);

class SomeClass
{
    public function simple($value)
    {
        if ($value !== 1) {
            if ($value !== 2) {
                return false;
            }
        }

        return true;
    }

    public function another($value)
    {
        if ($value !== 1 && $value !== 2) {
            return false;
        }

        return true;
    }
}
```
```

:x:

```php
declare(strict_types=1);

class SomeClass
{
    public function simple($value)
    {
        return $this->someOtherService->count($value);
    }

    public function another($value)
    {
        return $this->someOtherService->delete($value);
    }
}
```

:+1:

<br>

## FunctionLikeCognitiveComplexityRule

Cognitive complexity of function/method must be under specific limit

- class: `Symplify\PHPStanRules\CognitiveComplexity\Rules\FunctionLikeCognitiveComplexityRule`

```php
declare(strict_types=1);

class SomeClass
{
    public function simple($value)
    {
        if ($value !== 1) {
            if ($value !== 2) {
                return false;
            }
        }

        return true;
    }
}
```
```

:x:

```php
declare(strict_types=1);

class SomeClass
{
    public function simple($value)
    {
        if ($value === 1) {
            return true;
        }

        return $value === 2;
    }
}
```

:+1:

<br>

## NoChainMethodCallRule

Do not use chained method calls

- class: `Symplify\PHPStanRules\ObjectCalisthenics\Rules\NoChainMethodCallRule`

```php
declare(strict_types=1);

$this->runThis()
    ->runThat();
```

:x:

```php
declare(strict_types=1);

$this->runThis();
$this->runThat();
```

:+1:

<br>

## NoElseAndElseIfRule

Do not use "else/elseif". Refactor to early return

- class: `Symplify\PHPStanRules\ObjectCalisthenics\Rules\NoElseAndElseIfRule`

```php
if (...) {
    return 1;
} else {
    return 2;
}
```

:x:

```php
if (...) {
    return 1;
}

return 2;
```

:+1:

<br>

## NoSetterClassMethodRule

Setter "%s()" is not allowed. Use constructor injection or behavior name instead, e.g. "changeName()"

- class: `Symplify\PHPStanRules\ObjectCalisthenics\Rules\NoSetterClassMethodRule`

```php
declare(strict_types=1);

final class SomeClass
{
    public function setName(string $name): void
    {
        // ...
    }
}
```

:x:

```php
declare(strict_types=1);

final class SomeClass
{
    public function __construct(string $name)
    {
        // ...
    }
}
```

:+1:

<br>

## NoShortNameRule

Do not name "%s", shorter than %d chars

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\ObjectCalisthenics\Rules\NoShortNameRule`

```php
declare(strict_types=1);

function is(): void
{
}
```

:x:

```php
declare(strict_types=1);

function isClass(): void
{
}
```

:+1:

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\ObjectCalisthenics\Rules\NoShortNameRule;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(NoShortNameRule::class)
        ->call('configure', [[
            'minNameLength' => 3,
        ]]);
};
```

<br>

## SingleIndentationInMethodRule

Do not indent more than %dx in class methods

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\ObjectCalisthenics\Rules\SingleIndentationInMethodRule`

```php
function someFunction()
{
    if (...) {
        if (...) {
        }
    }
}
```

:x:

```php
function someFunction()
{
    if (! ...) {
    }

    if (!...) {
    }
}
```

:+1:

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\ObjectCalisthenics\Rules\SingleIndentationInMethodRule;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(SingleIndentationInMethodRule::class)
        ->call('configure', [[
            'maxNestingLevel' => [2],
        ]]);
};
```

<br>

## TooLongClassLikeRule

%s has %d lines, it is too long. Shorted it under %d lines

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\ObjectCalisthenics\Rules\TooLongClassLikeRule`

```php
class SomeClass
{
    public function someMethod()
    {
        if (...) {
            return 1;
        } else {
            return 2;
        }
    }
}
```

:x:

```php
class SomeClass
{
    public function someMethod()
    {
        return (...) ? 1 : 2;
    }
}
```

:+1:

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\ObjectCalisthenics\Rules\TooLongClassLikeRule;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(TooLongClassLikeRule::class)
        ->call('configure', [[
            'maxClassLikeLength' => 3,
        ]]);
};
```

<br>

## TooLongFunctionLikeRule

%s has %d lines, it is too long. Shorted it under %d lines

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\ObjectCalisthenics\Rules\TooLongFunctionLikeRule`

```php
function some()
{
    if (...) {
        return 1;
    } else {
        return 2;
    }
}
```

:x:

```php
function some()
{
    return (...) ? 1 : 2;
}
```

:+1:

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\ObjectCalisthenics\Rules\TooLongFunctionLikeRule;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(TooLongFunctionLikeRule::class)
        ->call('configure', [[
            'maxFunctionLikeLength' => 3,
        ]]);
};
```

<br>

## TooManyMethodsRule

Method has too many methods %d. Try narrowing it down under %d

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\ObjectCalisthenics\Rules\TooManyMethodsRule`

```php
declare(strict_types=1);

class SomeClass
{
    public function firstMethod(): void
    {
    }

    public function secondMethod(): void
    {
    }
}
```

:x:

```php
declare(strict_types=1);

class SomeClass
{
    public function firstMethod(): void
    {
    }
}
```

:+1:

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\ObjectCalisthenics\Rules\TooManyMethodsRule;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(TooManyMethodsRule::class)
        ->call('configure', [[
            'maxMethodCount' => 1,
        ]]);
};
```

<br>

## TooManyPropertiesRule

Class has too many properties %d. Try narrowing it down under %d

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\ObjectCalisthenics\Rules\TooManyPropertiesRule`

```php
declare(strict_types=1);

class SomeClass
{
    private $some;

    private $another;

    private $third;
}
```

:x:

```php
declare(strict_types=1);

class SomeClass
{
    private $some;

    private $another;
}
```

:+1:

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\ObjectCalisthenics\Rules\TooManyPropertiesRule;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(TooManyPropertiesRule::class)
        ->call('configure', [[
            'maxPropertyCount' => 2,
        ]]);
};
```

<br>

## AnnotateRegexClassConstWithRegexLinkRule

Add regex101.com link to that shows the regex in practise, so it will be easier to maintain in case of bug/extension in the future

- class: `Symplify\PHPStanRules\Rules\AnnotateRegexClassConstWithRegexLinkRule`

```php
declare(strict_types=1);

class SomeClass
{
    private const COMPLICATED_REGEX = '#some_complicated_stu|ff#';
}
```

:x:

```php
declare(strict_types=1);

/**
 * @see https://regex101.com/r/SZr0X5/12
 */
class SomeClass
{
    private const COMPLICATED_REGEX = '#some_complicated_stu|ff#';
}
```

:+1:

<br>

## BoolishClassMethodPrefixRule

Method "%s()" returns bool type, so the name should start with is/has/was...

- class: `Symplify\PHPStanRules\Rules\BoolishClassMethodPrefixRule`

```php
declare(strict_types=1);

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
declare(strict_types=1);

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

## CheckConstantExpressionDefinedInConstructOrSetupRule

Move constant expression to "__construct()", "setUp()" method or constant

- class: `Symplify\PHPStanRules\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule`

```php
class SomeClass
{
    public function someMethod()
    {
        $mainPath = getcwd() . '/absolute_path;
        // ...
        return $mainPath;
    }
}
```

:x:

```php
class SomeClass
{
    private $mainPath;

    public function __construct()
    {
        $this->mainPath = getcwd() . '/absolute_path;
    }

    public function someMethod()
    {
        // ...
        return $this->mainPath;
    }
}
```

:+1:

<br>

## CheckConstantStringValueFormatRule

Constant string value need to only have small letters, _, -, . and numbers

- class: `Symplify\PHPStanRules\Rules\CheckConstantStringValueFormatRule`

```php
declare(strict_types=1);

class SomeClass
{
    private const FOO = '$not_ok$';
}
```

:x:

```php
declare(strict_types=1);

class SomeClass
{
    private const FOO = 'bar';
}
```

:+1:

<br>

## CheckNotTestsNamespaceOutsideTestsDirectoryRule

"*Test.php" file cannot be located outside "Tests" namespace

- class: `Symplify\PHPStanRules\Rules\CheckNotTestsNamespaceOutsideTestsDirectoryRule`

```php
declare(strict_types=1);

// file: "SomeTest.php

namespace App;

class SomeTest
{
}
```

:x:

```php
declare(strict_types=1);

// file: "SomeTest.php

namespace App\Tests;

class SomeTest
{
}
```

:+1:

<br>

## CheckParentChildMethodParameterTypeCompatibleRule

Method parameters must be compatible with its parent

- class: `Symplify\PHPStanRules\Rules\CheckParentChildMethodParameterTypeCompatibleRule`

```php
declare(strict_types=1);

class ParentClass
{
    public function run(string $someParameter): void
    {
    }
}

class SomeClass extends ParentClass
{
    public function run($someParameter): void
    {
    }
}
```

:x:

```php
declare(strict_types=1);

class ParentClass
{
    public function run(string $someParameter): void
    {
    }
}

class SomeClass extends ParentClass
{
    public function run(string $someParameter): void
    {
    }
}
```

:+1:

<br>

## CheckRequiredAbstractKeywordForClassNameStartWithAbstractRule

Class name start with Abstract must have abstract keyword

- class: `Symplify\PHPStanRules\Rules\CheckRequiredAbstractKeywordForClassNameStartWithAbstractRule`

```php
declare(strict_types=1);

class AbstractClass
{
}
```

:x:

```php
declare(strict_types=1);

abstract class AbstractClass
{
}

class SomeClass
{
}
```

:+1:

<br>

## CheckRequiredAutowireAutoconfigurePublicUsedInConfigServiceRule

autowire(), autoconfigure(), and public() are required in config service

- class: `Symplify\PHPStanRules\Rules\CheckRequiredAutowireAutoconfigurePublicUsedInConfigServiceRule`

```php
declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public();
};
```

:x:

```php
declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();
};
```

:+1:

<br>

## CheckRequiredInterfaceInContractNamespaceRule

Relocate Interface to a "Contract" namespace

- class: `Symplify\PHPStanRules\Rules\CheckRequiredInterfaceInContractNamespaceRule`

```php
declare(strict_types=1);

namespace App\Repository;

interface ProductRepositoryInterface
{
}
```

:x:

```php
declare(strict_types=1);

namespace App\Contract\Repository;

interface ProductRepositoryInterface
{
}
```

:+1:

<br>

## CheckRequiredMethodNamingRule

Method with "@required" annotation need to be named "autowire<class-name>()"

- class: `Symplify\PHPStanRules\Rules\CheckRequiredMethodNamingRule`

```php
final class SomeClass
{
    /**
     * @required
     */
    public function autowireRandom(...)
    {
        // ...
    }
}
```

:x:

```php
final class SomeClass
{
    /**
     * @required
     */
    public function autowireSomeClass(...)
    {
        // ...
    }
}
```

:+1:

<br>

## CheckTraitMethodOnlyDelegateOtherClassRule

Trait method "%s()" should not contain any logic, but only delegate to other class call

- class: `Symplify\PHPStanRules\Rules\CheckTraitMethodOnlyDelegateOtherClassRule`

```php
trait SomeTrait
{
    public function someComplexLogic()
    {
        if (...) {
        } else {
            // ...
        }
    }
}
```

:x:

```php
declare(strict_types=1);

trait SomeTrait
{
    public function someDelegateCall(): void
    {
        $this->singleDelegateCall();
    }
}
```

:+1:

<br>

## CheckUnneededSymfonyStyleUsageRule

SymfonyStyle usage is unneeded for only newline, write, and/or writeln, use PHP_EOL and concatenation instead

- class: `Symplify\PHPStanRules\Rules\CheckUnneededSymfonyStyleUsageRule`

```php
declare(strict_types=1);

use Symfony\Component\Console\Style\SymfonyStyle;

class SomeClass
{
    private $symfonyStyle;

    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }

    public function run(): void
    {
        $this->symfonyStyle->writeln('Hi');
    }
}
```

:x:

```php
declare(strict_types=1);

class SomeClass
{
    public function run(): void
    {
        echo 'Hi';
    }
}
```

:+1:

<br>

## CheckUsedNamespacedNameOnClassNodeRule

Use "$class->namespaceName" instead of "$class->name" that only returns short class name

- class: `Symplify\PHPStanRules\Rules\CheckUsedNamespacedNameOnClassNodeRule`

```php
declare(strict_types=1);

use PhpParser\Node\Stmt\Class_;

final class SomeClass
{
    public function run(Class_ $class): bool
    {
        $className = (string) $class->name;
        return class_exists($className);
    }
}
```

:x:

```php
declare(strict_types=1);

use PhpParser\Node\Stmt\Class_;

final class SomeClass
{
    public function run(Class_ $class): bool
    {
        $className = (string) $class->namespacedName;
        return class_exists($className);
    }
}
```

:+1:

<br>

## ClassNameRespectsParentSuffixRule

Class "%s" should have suffix "%s" by parent class/interface

- class: `Symplify\PHPStanRules\Rules\ClassNameRespectsParentSuffixRule`

```php
declare(strict_types=1);

class Some extends Command
{
}
```

:x:

```php
declare(strict_types=1);

class SomeCommand extends Command
{
}
```

:+1:

<br>

## ExcessiveParameterListRule

Method "%s()" is using too many parameters - %d. Make it under %d

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\ExcessiveParameterListRule`

```php
declare(strict_types=1);

class SomeClass
{
    public function __construct($one, $two, $three)
    {
        // ...
    }
}
```

:x:

```php
declare(strict_types=1);

class SomeClass
{
    public function __construct($one, $two)
    {
        // ...
    }
}
```

:+1:

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Rules\ExcessiveParameterListRule;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(ExcessiveParameterListRule::class)
        ->call('configure', [[
            'maxParameterCount' => 2,
        ]]);
};
```

<br>

## ExcessivePublicCountRule

Too many public elements on class - %d. Try narrow it down under %d

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\ExcessivePublicCountRule`

```php
declare(strict_types=1);

class SomeClass
{
    public $one;

    public $two;

    public $three;
}
```

:x:

```php
declare(strict_types=1);

class SomeClass
{
    public $one;

    public $two;
}
```

:+1:

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Rules\ExcessivePublicCountRule;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(ExcessivePublicCountRule::class)
        ->call('configure', [[
            'maxPublicClassElementCount' => 2,
        ]]);
};
```

<br>

## ForbiddenArrayDestructRule

Array destruct is not allowed. Use value object to pass data instead

- class: `Symplify\PHPStanRules\Rules\ForbiddenArrayDestructRule`

```php
declare(strict_types=1);

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
declare(strict_types=1);

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

## ForbiddenArrayWithStringKeysRule

Array with keys is not allowed. Use value object to pass data instead

- class: `Symplify\PHPStanRules\Rules\ForbiddenArrayWithStringKeysRule`

```php
declare(strict_types=1);

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
declare(strict_types=1);

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

## ForbiddenAssignInIfRule

Assignment inside if is not allowed. Extract condition to extra variable on line above

- class: `Symplify\PHPStanRules\Rules\ForbiddenAssignInIfRule`

```php
declare(strict_types=1);

if ($isRandom = mt_rand()) {
    // ...
}
```

:x:

```php
declare(strict_types=1);

$isRandom = mt_rand();
if ($isRandom) {
    // ...
}
```

:+1:

<br>

## ForbiddenComplexArrayConfigInSetRule

For complex configuration use value object over array

- class: `Symplify\PHPStanRules\Rules\ForbiddenComplexArrayConfigInSetRule`

```php
declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('...')
        ->call('...', [[
            'options' => ['Cake\Network\Response', ['withLocation', 'withHeader']],
        ]]);
};
```

:x:

```php
declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('...')
        ->call('...', [[
            'options' => inline_value_objects([
                new SomeValueObject('Cake\Network\Response', ['withLocation', 'withHeader']),
            ]),
        ]]);
};
```

:+1:

<br>

## ForbiddenConstructorDependencyByTypeRule

Object instance of "%s" is forbidden to be passed to constructor

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\ForbiddenConstructorDependencyByTypeRule`

```php
declare(strict_types=1);

class SomeClass
{
    public function __construct(EntityManager $entityManager)
    {
        // ...
    }
}
```

:x:

```php
declare(strict_types=1);

class SomeClass
{
    public function __construct(ProductRepository $productRepository)
    {
        // ...
    }
}
```

:+1:

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Rules\ForbiddenConstructorDependencyByTypeRule;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(ForbiddenConstructorDependencyByTypeRule::class)
        ->call('configure', [[
            'forbiddenTypes' => ['EntityManager'],
        ]]);
};
```

<br>

## ForbiddenFuncCallRule

Function "%s()" cannot be used/left in the code

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\ForbiddenFuncCallRule`

```php
class SomeClass
{
    return eval('...');
}
```

:x:

```php
class SomeClass
{
    return echo '...';
}
```

:+1:

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Rules\ForbiddenFuncCallRule;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(ForbiddenFuncCallRule::class)
        ->call('configure', [[
            'forbiddenFunctions' => ['eval'],
        ]]);
};
```

<br>

## ForbiddenMethodCallByTypeInLocationRule

Method call "ClassName"->"method" is not allowed in "Location"

- class: `Symplify\PHPStanRules\Rules\ForbiddenMethodCallByTypeInLocationRule`

```php
declare(strict_types=1);

namespace App\Controller;

use View\Helper;

final class AlbumController
{
    public function get()
    {
        $helper = new Helper();
        $helper->number(4);

        return render();
    }
}
```

:x:

```php
declare(strict_types=1);

namespace App\Controller;

final class AlbumController
{
    public function get()
    {
        return render();
    }
}
```

:+1:

<br>

## ForbiddenMethodCallOnNewRule

Method call on new expression is not allowed.

- class: `Symplify\PHPStanRules\Rules\ForbiddenMethodCallOnNewRule`

```php
declare(strict_types=1);

(new SomeClass())->run();
```

:x:

```php
declare(strict_types=1);

$someClass = new SomeClass();
$someClass->run();
```

:+1:

<br>

## ForbiddenMethodOrStaticCallInForeachRule

Method nor static call in foreach is not allowed. Extract expression to a new variable assign on line before

- class: `Symplify\PHPStanRules\Rules\ForbiddenMethodOrStaticCallInForeachRule`

```php
declare(strict_types=1);

foreach ($this->getData($arg) as $key => $item) {
    // ...
}
```

:x:

```php
declare(strict_types=1);

$data = $this->getData($arg);
foreach ($arg as $key => $item) {
    // ...
}
```

:+1:

<br>

## ForbiddenMethodOrStaticCallInIfRule

Method nor static call in if () or elseif () is not allowed. Extract expression to a new variable assign on line before

- class: `Symplify\PHPStanRules\Rules\ForbiddenMethodOrStaticCallInIfRule`

```php
declare(strict_types=1);

$someObject = new SomeClass();
if ($someObject->getData($arg) === []) {
} elseif ($someObject->getData($arg2) !== []) {
}
```

:x:

```php
declare(strict_types=1);

$someObject = new SomeClass();
$dataFirstArg = $someObject->getData($arg);
$dataSecondArg = $someObject->getData($arg2);

if ($dataFirstArg === []) {
} elseif ($dataSecondArg !== []) {
}
```

:+1:

<br>

## ForbiddenMultipleClassLikeInOneFileRule

Multiple class/interface/trait is not allowed in single file

- class: `Symplify\PHPStanRules\Rules\ForbiddenMultipleClassLikeInOneFileRule`

```php
declare(strict_types=1);

class SomeClass
{
}

interface SomeInterface
{
}
```

:x:

```php
declare(strict_types=1);

// SomeClass.php
class SomeClass
{
}

// SomeInterface.php
interface SomeInterface
{
}
```

:+1:

<br>

## ForbiddenNestedForeachWithEmptyStatementRule

Nested foreach with empty statement is not allowed

- class: `Symplify\PHPStanRules\Rules\ForbiddenNestedForeachWithEmptyStatementRule`

```php
declare(strict_types=1);

$collectedFileErrors = [];

foreach ($errors as $fileErrors) {
    foreach ($fileErrors as $fileError) {
        $collectedFileErrors[] = $fileError;
    }
}
```

:x:

```php
declare(strict_types=1);

$collectedFileErrors = [];

foreach ($fileErrors as $fileError) {
    $collectedFileErrors[] = $fileError;
}
```

:+1:

<br>

## ForbiddenNewInMethodRule

"new" in method "%s->%s()" is not allowed.

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\ForbiddenNewInMethodRule`

```php
declare(strict_types=1);

use PHPStan\Rules\Rule;

class SomeRule implements Rule
{
    protected function getRule(): Rule
    {
        return new self();
    }
}
```

:x:

```php
declare(strict_types=1);

use PHPStan\Rules\Rule;

class SomeRule implements Rule
{
    protected function getRule(): Rule
    {
        return $this->getService(self::class);
    }
}
```

:+1:

```php
<?php

declare(strict_types=1);

use PHPStan\Rules\Rule;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Rules\ForbiddenNewInMethodRule;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(ForbiddenNewInMethodRule::class)
        ->call('configure', [[
            'forbiddenClassMethods' => [
                Rule::class => ['getRule'],
            ],
        ]]);
};
```

<br>

## ForbiddenNewOutsideFactoryServiceRule

"new" outside factory is not allowed for object type "%s"

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\ForbiddenNewOutsideFactoryServiceRule`

```php
declare(strict_types=1);

class SomeClass
{
    public function process(): void
    {
        $anotherObject = new AnotherObject();
        // ...
    }
}
```

:x:

```php
declare(strict_types=1);

class SomeClass
{
    public function __construt(AnotherObjectFactory $anotherObjectFactory): void
    {
        $this->anotherObjectFactory = $anotherObjectFactory;
    }

    public function process(): void
    {
        $anotherObject = $this->anotherObjectFactory = $anotherObjectFactory->create();
        // ...
    }
}
```

:+1:

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Rules\ForbiddenNewOutsideFactoryServiceRule;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(ForbiddenNewOutsideFactoryServiceRule::class)
        ->call('configure', [[
            'types' => ['AnotherObject'],
        ]]);
};
```

<br>

## ForbiddenParentClassRule

Class "%s" inherits from forbidden parent class "%s". Use "%s" instead

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\ForbiddenParentClassRule`

```php
declare(strict_types=1);

class SomeClass extends ParentClass
{
}
```

:x:

```php
declare(strict_types=1);

class SomeClass
{
    public function __construct(DecoupledClass $decoupledClass)
    {
        $this->decoupledClass = $decoupledClass;
    }
}
```

:+1:

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Rules\ForbiddenParentClassRule;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(ForbiddenParentClassRule::class)
        ->call('configure', [[
            'forbiddenParentClasses' => ['ParentClass'],
        ]]);
};
```

<br>

## ForbiddenPrivateMethodByTypeRule

Private method in is not allowed here - it should only delegate to others. Decouple the private method to a new service class

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\ForbiddenPrivateMethodByTypeRule`

```php
declare(strict_types=1);

class SomeCommand extends Command
{
    public function run(): void
    {
        $this->somePrivateMethod();
    }

    private function somePrivateMethod(): void
    {
        // ...
    }
}
```

:x:

```php
declare(strict_types=1);

class SomeCommand extends Command
{
    /**
     * @var ExternalService
     */
    private $externalService;

    public function __construct(ExternalService $externalService)
    {
        $this->externalService = $externalService;
    }

    public function run(): void
    {
        $this->externalService->someMethod();
    }
}
```

:+1:

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Rules\ForbiddenPrivateMethodByTypeRule;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(ForbiddenPrivateMethodByTypeRule::class)
        ->call('configure', [[
            'forbiddenTypes' => ['Command'],
        ]]);
};
```

<br>

## ForbiddenProtectedPropertyRule

Property with protected modifier is not allowed. Use interface contract method instead

- class: `Symplify\PHPStanRules\Rules\ForbiddenProtectedPropertyRule`

```php
declare(strict_types=1);

class SomeClass
{
    protected $repository;
}
```

:x:

```php
declare(strict_types=1);

class SomeClass implements RepositoryAwareInterface
{
    public function getRepository(): void
    {
        // ....
    }
}
```

:+1:

<br>

## ForbiddenReturnValueOfIncludeOnceRule

Cannot return include_once/require_once

- class: `Symplify\PHPStanRules\Rules\ForbiddenReturnValueOfIncludeOnceRule`

```php
declare(strict_types=1);

class SomeClass
{
    public function run()
    {
        return require_once 'Test.php';
    }
}
```

:x:

```php
declare(strict_types=1);

class SomeClass
{
    public function run(): void
    {
        require_once 'Test.php';
    }
}
```

:+1:

<br>

## ForbiddenSpreadOperatorRule

Spread operator is not allowed.

- class: `Symplify\PHPStanRules\Rules\ForbiddenSpreadOperatorRule`

```php
declare(strict_types=1);

$args = [$firstValue, $secondValue];
$message = sprintf('%s', ...$args);
```

:x:

```php
declare(strict_types=1);

$message = sprintf('%s', $firstValue, $secondValue);
```

:+1:

<br>

## ForbiddenTestsNamespaceOutsideTestsDirectoryRule

"Tests" namespace can be only in "/tests" directory

- class: `Symplify\PHPStanRules\Rules\ForbiddenTestsNamespaceOutsideTestsDirectoryRule`

```php
declare(strict_types=1);

// file path: "src/SomeClass.php

namespace App\Tests;

class SomeClass
{
}
```

:x:

```php
declare(strict_types=1);

// file path: "tests/SomeClass.php

namespace App\Tests;

class SomeClass
{
}
```

:+1:

<br>

## MatchingTypeConstantRule

Constant type should be "%s", but is "%s"

- class: `Symplify\PHPStanRules\Rules\MatchingTypeConstantRule`

```php
declare(strict_types=1);

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
declare(strict_types=1);

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

## NoAbstractMethodRule

Use explicit interface contract or a service over unclear abstract methods

- class: `Symplify\PHPStanRules\Rules\NoAbstractMethodRule`

```php
declare(strict_types=1);

abstract class SomeClass
{
    abstract public function run();
}
```

:x:

```php
declare(strict_types=1);

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

## NoArrayAccessOnObjectRule

Use explicit methods over array access on object

- class: `Symplify\PHPStanRules\Rules\NoArrayAccessOnObjectRule`

```php
declare(strict_types=1);

class SomeClass
{
    public function run(MagicArrayObject $magicArrayObject)
    {
        return $magicArrayObject['more_magic'];
    }
}
```

:x:

```php
declare(strict_types=1);

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

- class: `Symplify\PHPStanRules\Rules\NoArrayStringObjectReturnRule`

```php
declare(strict_types=1);

final class SomeClass
{
    /**
     * @return array<string, Value>
     */
    private function getValues()
    {
    }
}
```

:x:

```php
declare(strict_types=1);

final class SomeClass
{
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

## NoClassWithStaticMethodWithoutStaticNameRule

Class "%s" with static method must have "Static" in its name it explicit

- class: `Symplify\PHPStanRules\Rules\NoClassWithStaticMethodWithoutStaticNameRule`

```php
declare(strict_types=1);

class SomeClass
{
    public static function getSome(): void
    {
    }
}
```

:x:

```php
declare(strict_types=1);

class SomeStaticClass
{
    public static function getSome(): void
    {
    }
}
```

:+1:

<br>

## NoConstructorInTestRule

Do not use constructor in tests. Move to "setUp()" method

- class: `Symplify\PHPStanRules\Rules\NoConstructorInTestRule`

```php
declare(strict_types=1);

final class SomeTest
{
    public function __construct()
    {
        // ...
    }
}
```

:x:

```php
declare(strict_types=1);

final class SomeTest
{
    protected function setUp(): void
    {
        // ...
    }
}
```

:+1:

<br>

## NoContainerInjectionInConstructorRule

Instead of container injection, use specific service

- class: `Symplify\PHPStanRules\Rules\NoContainerInjectionInConstructorRule`

```php
declare(strict_types=1);

class SomeClass
{
    public function __construct(ContainerInterface $container)
    {
        $this->someDependency = $container->get('...');
    }
}
```

:x:

```php
declare(strict_types=1);

class SomeClass
{
    public function __construct(SomeDependency $someDependency)
    {
        $this->someDependency = $someDependency;
    }
}
```

:+1:

<br>

## NoDefaultExceptionRule

Use custom exceptions instead of native "%s"

- class: `Symplify\PHPStanRules\Rules\NoDefaultExceptionRule`

```php
declare(strict_types=1);

throw new RuntimeException('...');
```

:x:

```php
declare(strict_types=1);


throw new FileNotFoundException('...');
```

:+1:

<br>

## NoDefaultParameterValueRule

Parameter "%s" cannot have default value

- class: `Symplify\PHPStanRules\Rules\NoDefaultParameterValueRule`

```php
declare(strict_types=1);

class SomeClass
{
    public function run($value = true): void
    {
    }
}
```

:x:

```php
declare(strict_types=1);

class SomeClass
{
    public function run($value): void
    {
    }
}
```

:+1:

<br>

## NoDuplicatedShortClassNameRule

Class with base "%s" name is already used in "%s". Use unique name to make classes easy to recognize

- class: `Symplify\PHPStanRules\Rules\NoDuplicatedShortClassNameRule`

```php
declare(strict_types=1);

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

```php
declare(strict_types=1);

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

- class: `Symplify\PHPStanRules\Rules\NoDynamicNameRule`

```php
declare(strict_types=1);

class SomeClass
{
    public function old(): bool
    {
        return $this->${variable};
    }
}
```

:x:

```php
declare(strict_types=1);

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

## NoEntityManagerInControllerRule

Use specific repository over entity manager in Controller

- class: `Symplify\PHPStanRules\Rules\NoEntityManagerInControllerRule`

```php
declare(strict_types=1);

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
declare(strict_types=1);

final class SomeController
{
    public function __construct(AnotherRepository $anotherRepository)
    {
        // ...
    }
}
```

:+1:

<br>

## NoFactoryInConstructorRule

Do not use factory/method call in constructor. Put factory in config and get service with dependency injection

- class: `Symplify\PHPStanRules\Rules\NoFactoryInConstructorRule`

```php
declare(strict_types=1);

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

```php
declare(strict_types=1);

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

Separate function "%s()" in method call to standalone row to improve readability

- class: `Symplify\PHPStanRules\Rules\NoFuncCallInMethodCallRule`

```php
declare(strict_types=1);

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

```php
declare(strict_types=1);

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

Do not use "$entityManager->getRepository()" outside of the constructor of repository service or setUp() method in test case

- class: `Symplify\PHPStanRules\Rules\NoGetRepositoryOutsideConstructorRule`

```php
declare(strict_types=1);

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
declare(strict_types=1);

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

## NoInlineStringRegexRule

Use local named constant instead of inline string for regex to explain meaning by constant name

- class: `Symplify\PHPStanRules\Rules\NoInlineStringRegexRule`

```php
declare(strict_types=1);

class SomeClass
{
    public function run($value)
    {
        return preg_match('#some_stu|ff#', $value);
    }
}
```

:x:

```php
declare(strict_types=1);

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

- class: `Symplify\PHPStanRules\Rules\NoIssetOnObjectRule`

```php
declare(strict_types=1);

class SomeClass
{
    public function run()
    {
        if (random_int(0, 1)) {
            $object = new self();
        }

        if (isset($object)) {
            return $object;
        }
    }
}
```

:x:

```php
declare(strict_types=1);

class SomeClass
{
    public function run()
    {
        $object = null;
        if (random_int(0, 1)) {
            $object = new self();
        }

        if ($object !== null) {
            return $object;
        }
    }
}
```

:+1:

<br>

## NoMethodTagInClassDocblockRule

Do not use @method tag in class docblock

- class: `Symplify\PHPStanRules\Rules\NoMethodTagInClassDocblockRule`

```php
declare(strict_types=1);

/**
 * @method getMagic() string
 */
class SomeClass
{
    public function __call(): void
    {
        // more magic
    }
}
```

:x:

```php
declare(strict_types=1);

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

## NoMissingDirPathRule

The path "%s" was not found

- class: `Symplify\PHPStanRules\Rules\NoMissingDirPathRule`

```php
declare(strict_types=1);

class SomeClass
{
    public function run()
    {
        return __DIR__ . '/missing_location.txt';
    }
}
```

:x:

```php
declare(strict_types=1);

class SomeClass
{
    public function run()
    {
        return __DIR__ . '/existing_location.txt';
    }
}
```

:+1:

<br>

## NoMultiArrayAssignRule

Use value object over multi array assign

- class: `Symplify\PHPStanRules\Rules\NoMultiArrayAssignRule`

```php
declare(strict_types=1);

final class SomeClass
{
    public function run(): void
    {
        $values = [];
        $values['person']['name'] = 'Tom';
        $values['person']['surname'] = 'Dev';
    }
}
```

:x:

```php
declare(strict_types=1);

final class SomeClass
{
    public function run(): void
    {
        $values = [];
        $values[] = new Person('Tom', 'Dev');
    }
}
```

:+1:

<br>

## NoNewOutsideFactoryRule

Use decoupled factory service to create "%s" object

- class: `Symplify\PHPStanRules\Rules\NoNewOutsideFactoryRule`

```php
declare(strict_types=1);

final class SomeClass
{
    public function run()
    {
        return new SomeValueObject();
    }
}
```

:x:

```php
declare(strict_types=1);

final class SomeFactory
{
    public function create()
    {
        return new SomeValueObject();
    }
}
```

:+1:

<br>

## NoNullableParameterRule

Parameter "%s" cannot be nullable

- class: `Symplify\PHPStanRules\Rules\NoNullableParameterRule`

```php
declare(strict_types=1);

class SomeClass
{
    public function run(?string $value = null): void
    {
    }
}
```

:x:

```php
declare(strict_types=1);

class SomeClass
{
    public function run(string $value): void
    {
    }
}
```

:+1:

<br>

## NoParentMethodCallOnEmptyStatementInParentMethodRule

Do not call parent method if parent method is empty

- class: `Symplify\PHPStanRules\Rules\NoParentMethodCallOnEmptyStatementInParentMethodRule`

```php
declare(strict_types=1);

class ParentClass
{
    public function someMethod(): void
    {
    }
}

class SomeClass extends ParentClass
{
    public function someMethod(): void
    {
        parent::someMethod();
    }
}
```

:x:

```php
declare(strict_types=1);

class ParentClass
{
    public function someMethod(): void
    {
    }
}

class SomeClass extends ParentClass
{
    public function someMethod(): void
    {
    }
}
```

:+1:

<br>

## NoParentMethodCallOnNoOverrideProcessRule

Do not call parent method if no override process

- class: `Symplify\PHPStanRules\Rules\NoParentMethodCallOnNoOverrideProcessRule`

```php
declare(strict_types=1);

class SomeClass extends Printer
{
    public function print($nodes)
    {
        return parent::print($nodes);
    }
}
```

:x:

```php
declare(strict_types=1);

class SomeClass extends Printer
{
}
```

:+1:

<br>

## NoParticularNodeRule

"%s" is forbidden to use

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\NoParticularNodeRule`

```php
declare(strict_types=1);

return @strlen('...');
```

:x:

```php
declare(strict_types=1);

return strlen('...');
```

:+1:

```php
<?php

declare(strict_types=1);

use PhpParser\Node\Expr\ErrorSuppress;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Rules\NoParticularNodeRule;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(NoParticularNodeRule::class)
        ->call('configure', [[
            'forbiddenNodes' => [ErrorSuppress::class],
        ]]);
};
```

<br>

## NoPostIncPostDecRule

Post operation are forbidden, as they make 2 values at the same line. Use pre instead

- class: `Symplify\PHPStanRules\Rules\NoPostIncPostDecRule`

```php
declare(strict_types=1);

class SomeClass
{
    public function run($value = 1): void
    {
        // 1 ... 0
        if ($value--) {
        }
    }
}
```

:x:

```php
declare(strict_types=1);

class SomeClass
{
    public function run($value = 1): void
    {
        // 0
        if (--$value) {
        }
    }
}
```

:+1:

<br>

## NoProtectedElementInFinalClassRule

Instead of protected element in final class use private element or contract method

- class: `Symplify\PHPStanRules\Rules\NoProtectedElementInFinalClassRule`

```php
declare(strict_types=1);

final class SomeClass
{
    private function run(): void
    {
    }
}
```

:x:

```php
declare(strict_types=1);

final class SomeClass
{
    private function run(): void
    {
    }
}
```

:+1:

<br>

## NoReferenceRule

Use explicit return value over magic &reference

- class: `Symplify\PHPStanRules\Rules\NoReferenceRule`

```php
declare(strict_types=1);

class SomeClass
{
    public function run(&$value): void
    {
    }
}
```

:x:

```php
declare(strict_types=1);

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

- class: `Symplify\PHPStanRules\Rules\NoReturnArrayVariableListRule`

```php
declare(strict_types=1);

class ReturnVariables
{
    public function run($value, $value2): array
    {
        return [$value, $value2];
    }
}
```

:x:

```php
declare(strict_types=1);

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

## NoScalarAndArrayConstructorParameterRule

Do not use scalar or array as constructor parameter. Use ParameterProvider service instead

- class: `Symplify\PHPStanRules\Rules\NoScalarAndArrayConstructorParameterRule`

```php
declare(strict_types=1);

final class SomeClass
{
    /**
     * @var string
     */
    private $outputDirectory;

    public function __construct(string $outputDirectory)
    {
        $this->outputDirectory = $outputDirectory;
    }
}
```

:x:

```php
final class SomeClass
{
    /**
     * @var string
     */
    private $outputDirectory;

    public function __construct(ParameterProvider $parameterProvider)
    {
        $this->outputDirectory = $parameterProvider->getStringParam(...);
    }
}
```

:+1:

<br>

## NoSetterOnServiceRule

Do not use setter on a service

- class: `Symplify\PHPStanRules\Rules\NoSetterOnServiceRule`

```php
class SomeService
{
    public function setSomeValue(...)
    {
    }
}
```

:x:

```php
class SomeEntity
{
    public function setSomeValue(...)
    {
    }
}
```

:+1:

<br>

## NoStaticCallRule

Do not use static calls

- class: `Symplify\PHPStanRules\Rules\NoStaticCallRule`

```php
declare(strict_types=1);

final class SomeClass
{
    public function run()
    {
        return AnotherClass::staticMethod();
    }
}
```

:x:

```php
declare(strict_types=1);

final class SomeClass
{
    public function run()
    {
        $anotherClass = new AnotherClass();
        return $anotherClass->staticMethod();
    }
}
```

:+1:

<br>

## NoStaticPropertyRule

Do not use static property

- class: `Symplify\PHPStanRules\Rules\NoStaticPropertyRule`

```php
declare(strict_types=1);

final class SomeClass
{
    private static $customFileNames = [];
}
```

:x:

```php
declare(strict_types=1);

final class SomeClass
{
    private $customFileNames = [];
}
```

:+1:

<br>

## NoSuffixValueObjectClassRule

Value Object class name "%s" must be withotu "ValueObject" suffix. The correct class name is "%s".

- class: `Symplify\PHPStanRules\Rules\NoSuffixValueObjectClassRule`

```php
declare(strict_types=1);

class SomeValueObject
{
    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
```

:x:

```php
declare(strict_types=1);

class Some
{
    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
```

:+1:

<br>

## NoTraitExceptRequiredAutowireRule

Do not use trait

- class: `Symplify\PHPStanRules\Rules\NoTraitExceptRequiredAutowireRule`

```php
declare(strict_types=1);

trait SomeTrait
{
    public function run(): void
    {
    }
}
```

:x:

```php
trait SomeTrait
{
    /**
     * @required
     */
    public function autowire(...)
    {
        // ...
    }
}
```

:+1:

<br>

## PreferredClassRule

Instead of "%s" class/interface use "%s"

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\PreferredClassRule`

```php
declare(strict_types=1);

class SomeClass
{
    public function run()
    {
        return new SplFileInfo('...');
    }
}
```

:x:

```php
declare(strict_types=1);

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

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Rules\PreferredClassRule;
use Symplify\SmartFileSystem\SmartFileInfo;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(PreferredClassRule::class)
        ->call('configure', [[
            'oldToPreferredClasses' => [
                SplFileInfo::class => SmartFileInfo::class,
            ],
        ]]);
};
```

<br>

## PreferredMethodCallOverFuncCallRule

Use "%s->%s()" method call over "%s()" func call

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\PreferredMethodCallOverFuncCallRule`

```php
declare(strict_types=1);

class SomeClass
{
    public function run($value)
    {
        return strlen($value);
    }
}
```

:x:

```php
declare(strict_types=1);

use Nette\Utils\Strings;

class SomeClass
{
    public function __construct(Strings $strings)
    {
        $this->strings = $strings;
    }

    public function run($value)
    {
        return $this->strings->lenght($value);
    }
}
```

:+1:

```php
<?php

declare(strict_types=1);

use Nette\Utils\Strings;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Rules\PreferredMethodCallOverFuncCallRule;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(PreferredMethodCallOverFuncCallRule::class)
        ->call('configure', [[
            'funcCallToPreferredMethodCalls' => [
                'strlen' => [Strings::class, 'lenght'],
],
                    ]]);
};
```

<br>

## PreferredRawDataInTestDataProviderRule

Code configured at setUp() cannot be used in data provider. Move it to test() method

- class: `Symplify\PHPStanRules\Rules\PreferredRawDataInTestDataProviderRule`

```php
declare(strict_types=1);

final class UseDataFromSetupInTestDataProviderTest extends TestCase
{
    private $data;

    protected function setUp(): void
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
    public function testFoo($value): void
    {
        $this->assertTrue($value);
    }
}
```

:x:

```php
declare(strict_types=1);

use stdClass;

final class UseRawDataForTestDataProviderTest
{
    private $obj;

    private function setUp(): void
    {
        $this->obj = new stdClass();
    }

    public function provideFoo()
    {
        yield [true];
    }

    /**
     * @dataProvider provideFoo
     */
    public function testFoo($value): void
    {
        $this->obj->x = $value;
        $this->assertTrue($this->obj->x);
    }
}
```

:+1:

<br>

## PreferredStaticCallOverFuncCallRule

Use "%s::%s()" static call over "%s()" func call

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\PreferredStaticCallOverFuncCallRule`

```php
declare(strict_types=1);

class SomeClass
{
    public function run($value)
    {
        return strlen($value);
    }
}
```

:x:

```php
declare(strict_types=1);

use Nette\Utils\Strings;

class SomeClass
{
    public function run($value)
    {
        return Strings::lenght($value);
    }
}
```

:+1:

```php
<?php

declare(strict_types=1);

use Nette\Utils\Strings;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Rules\PreferredStaticCallOverFuncCallRule;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(PreferredStaticCallOverFuncCallRule::class)
        ->call('configure', [[
            'funcCallToPreferredStaticCalls' => [
                'strlen' => [Strings::class, 'lenght'],
],
                    ]]);
};
```

<br>

## PrefixAbstractClassRule

Abstract class name "%s" must be prefixed with "Abstract"

- class: `Symplify\PHPStanRules\Rules\PrefixAbstractClassRule`

```php
declare(strict_types=1);

abstract class SomeClass
{
}
```

:x:

```php
declare(strict_types=1);

abstract class AbstractSomeClass
{
}
```

:+1:

<br>

## PreventParentMethodVisibilityOverrideRule

Change "%s()" method visibility to "%s" to respect parent method visibility.

- class: `Symplify\PHPStanRules\Rules\PreventParentMethodVisibilityOverrideRule`

```php
declare(strict_types=1);

class SomeParentClass
{
    public function run(): void
    {
    }
}

class SomeClass
{
    protected function run(): void
    {
    }
}
```

:x:

```php
declare(strict_types=1);

class SomeParentClass
{
    public function run(): void
    {
    }
}

class SomeClass
{
    public function run(): void
    {
    }
}
```

:+1:

<br>

## RegexSuffixInRegexConstantRule

Name your constant with "_REGEX" suffix, instead of "%s"

- class: `Symplify\PHPStanRules\Rules\RegexSuffixInRegexConstantRule`

```php
declare(strict_types=1);

class SomeClass
{
    public const SOME_NAME = '#some\s+name#';

    public function run($value): void
    {
        $somePath = preg_match(self::SOME_NAME, $value);
    }
}
```

:x:

```php
declare(strict_types=1);

class SomeClass
{
    public const SOME_NAME_REGEX = '#some\s+name#';

    public function run($value): void
    {
        $somePath = preg_match(self::SOME_NAME_REGEX, $value);
    }
}
```

:+1:

<br>

## RequireConstantInMethodCallPositionRule

Parameter argument on position %d must use %s constant

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\RequireConstantInMethodCallPositionRule`

```php
declare(strict_types=1);

class SomeClass
{
    public function someMethod(SomeType $someType): void
    {
        $someType->someMethod('hey');
    }
}
```

:x:

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

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Rules\RequireConstantInMethodCallPositionRule;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(RequireConstantInMethodCallPositionRule::class)
        ->call('configure', [[
            'requiredLocalConstantInMethodCall' => [
                'SomeType' => [
                    'someMethod' => [0],
                ],
],
                    ]]);
};
```

<br>

## RequireDataProviderTestMethodRule

The "%s()" method must use data provider

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\RequireDataProviderTestMethodRule`

```php
declare(strict_types=1);

class SomeRectorTestCase extends RectorTestCase
{
    public function test(): void
    {
    }
}
```

:x:

```php
declare(strict_types=1);

class SomeRectorTestCase extends RectorTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test($value): void
    {
    }

    public function provideData(): void
    {
        // ...
    }
}
```

:+1:

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Rules\RequireDataProviderTestMethodRule;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(RequireDataProviderTestMethodRule::class)
        ->call('configure', [[
            'classesRequiringDataProvider' => ['*RectorTestCase'],
        ]]);
};
```

<br>

## RequireMethodCallArgumentConstantRule

Method call argument on position %d must use constant (e.g. "Option::NAME") over value

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\RequireMethodCallArgumentConstantRule`

```php
declare(strict_types=1);

class AnotherClass
{
    public function run(SomeClass $someClass): void
    {
        $someClass->call('name');
    }
}
```

:x:

```php
class AnotherClass
{
    private OPTION_NAME = 'name';

    public function run(SomeClass $someClass)
    {
        $someClass->call(self::OPTION_NAME);
    }
}
```

:+1:

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Rules\RequireMethodCallArgumentConstantRule;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(RequireMethodCallArgumentConstantRule::class)
        ->call('configure', [[
            'constantArgByMethodByType' => [
                'SomeClass' => [
                    'call' => [0],
                ],
],
                    ]]);
};
```

<br>

## RequireNewArgumentConstantRule

New expression argument on position %d must use constant over value

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\RequireNewArgumentConstantRule`

```php
declare(strict_types=1);

use Symfony\Component\Console\Input\InputOption;

$inputOption = new InputOption('name', null, 2);
```

:x:

```php
declare(strict_types=1);

use Symfony\Component\Console\Input\InputOption;

$inputOption = new InputOption('name', null, InputOption::VALUE_REQUIRED);
```

:+1:

```php
<?php

declare(strict_types=1);

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Rules\RequireNewArgumentConstantRule;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(RequireNewArgumentConstantRule::class)
        ->call('configure', [[
            'constantArgByNewByType' => [
                InputOption::class => [2],
            ],
        ]]);
};
```

<br>

## RequireStringArgumentInMethodCallRule

Use quoted string in method call "%s()" argument on position %d instead of "::class. It prevent scoping of the class in building prefixed package.

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\RequireStringArgumentInMethodCallRule`

```php
class AnotherClass
{
    public function run(SomeClass $someClass)
    {
        $someClass->someMethod(YetAnotherClass:class);
    }
}
```

:x:

```php
class AnotherClass
{
    public function run(SomeClass $someClass)
    {
        $someClass->someMethod('YetAnotherClass'');
    }
}
```

:+1:

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Rules\RequireStringArgumentInMethodCallRule;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(RequireStringArgumentInMethodCallRule::class)
        ->call('configure', [[
            'stringArgByMethodByType' => [
                'SomeClass' => [
                    'someMethod' => [0],
                ],
],
                    ]]);
};
```

<br>

## RequireThisOnParentMethodCallRule

Use "$this-><method>()" instead of "parent::<method>()" unless in the same named method

- class: `Symplify\PHPStanRules\Rules\RequireThisOnParentMethodCallRule`

```php
declare(strict_types=1);

class SomeParentClass
{
    public function run(): void
    {
    }
}

class SomeClass extends SomeParentClass
{
    public function go(): void
    {
        parent::run();
    }
}
```

:x:

```php
declare(strict_types=1);

class SomeParentClass
{
    public function run(): void
    {
    }
}

class SomeClass extends SomeParentClass
{
    public function go(): void
    {
        $tihs->run();
    }
}
```

:+1:

<br>

## SeeAnnotationToTestRule

Class "%s" is missing @see annotation with test case class reference

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\SeeAnnotationToTestRule`

```php
declare(strict_types=1);

class SomeClass extends Rule
{
}
```

:x:

```php
declare(strict_types=1);

/**
 * @see SomeClassTest
 */
class SomeClass extends Rule
{
}
```

:+1:

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Rules\SeeAnnotationToTestRule;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(SeeAnnotationToTestRule::class)
        ->call('configure', [[
            'requiredSeeTypes' => ['Rule'],
        ]]);
};
```

<br>

## SuffixInterfaceRule

Interface name "%s" must be suffixed with "Interface"

- class: `Symplify\PHPStanRules\Rules\SuffixInterfaceRule`

```php
declare(strict_types=1);

interface SomeClass
{
}
```

:x:

```php
declare(strict_types=1);

interface SomeInterface
{
}
```

:+1:

<br>

## SuffixTraitRule

Trait name "%s" must be suffixed with "Trait"

- class: `Symplify\PHPStanRules\Rules\SuffixTraitRule`

```php
declare(strict_types=1);

trait SomeClass
{
}
```

:x:

```php
declare(strict_types=1);

trait SomeTrait
{
}
```

:+1:

<br>

## TooDeepNewClassNestingRule

new <class> is limited to %d "new <class>(new <class>))" nesting to each other. You have %d nesting.

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\TooDeepNewClassNestingRule`

```php
declare(strict_types=1);

$someObject = new A(new B(new C()));
```

:x:

```php
declare(strict_types=1);

$firstObject = new B(new C());
$someObject = new A($firstObject);
```

:+1:

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Rules\TooDeepNewClassNestingRule;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(TooDeepNewClassNestingRule::class)
        ->call('configure', [[
            'maxNewClassNesting' => 2,
        ]]);
};
```

<br>

## TooLongVariableRule

Variable "$%s" is too long with %d chars. Narrow it under %d chars

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\TooLongVariableRule`

```php
declare(strict_types=1);

class SomeClass
{
    public function run()
    {
        return $superLongVariableName;
    }
}
```

:x:

```php
declare(strict_types=1);

class SomeClass
{
    public function run()
    {
        return $shortName;
    }
}
```

:+1:

```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Rules\TooLongVariableRule;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(TooLongVariableRule::class)
        ->call('configure', [[
            'maxVariableLength' => 10,
        ]]);
};
```

<br>

## UppercaseConstantRule

Constant "%s" must be uppercase

- class: `Symplify\PHPStanRules\Rules\UppercaseConstantRule`

```php
declare(strict_types=1);

final class SomeClass
{
    public const some = 'value';
}
```

:x:

```php
declare(strict_types=1);

final class SomeClass
{
    public const SOME = 'value';
}
```

:+1:

<br>
