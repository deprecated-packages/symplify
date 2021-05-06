# 149 Rules Overview

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

## CheckOptionArgumentCommandRule

Argument and options "%s" got confused

- class: [`Symplify\PHPStanRules\Rules\CheckOptionArgumentCommandRule`](../src/Rules/CheckOptionArgumentCommandRule.php)

```php
class SomeClass extends Command
{
    protected function configure(): void
    {
        $this->addOption('source');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = $input->getArgument('source');
    }
}
```

:x:

<br>

```php
class SomeClass extends Command
{
    protected function configure(): void
    {
        $this->addArgument('source');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = $input->getArgument('source');
    }
}
```

:+1:

<br>

## CheckParentChildMethodParameterTypeCompatibleRule

Method parameters must be compatible with its parent

- class: [`Symplify\PHPStanRules\Rules\CheckParentChildMethodParameterTypeCompatibleRule`](../src/Rules/CheckParentChildMethodParameterTypeCompatibleRule.php)

```php
class ParentClass
{
    public function run(string $someParameter)
    {
    }
}

class SomeClass extends ParentClass
{
    public function run($someParameter)
    {
    }
}
```

:x:

<br>

```php
class ParentClass
{
    public function run(string $someParameter)
    {
    }
}

class SomeClass extends ParentClass
{
    public function run(string $someParameter)
    {
    }
}
```

:+1:

<br>

## CheckRequiredAutowireAutoconfigurePublicInConfigServiceRule

`autowire()`, `autoconfigure()`, and `public()` are required in config service

- class: [`Symplify\PHPStanRules\Rules\CheckRequiredAutowireAutoconfigurePublicInConfigServiceRule`](../src/Rules/CheckRequiredAutowireAutoconfigurePublicInConfigServiceRule.php)

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public();
};
```

:x:

<br>

```php
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

## CheckRequiredMethodNamingRule

Method with "@required" must respect "autowire" + class name `("%s()")`

- class: [`Symplify\PHPStanRules\Rules\CheckRequiredMethodNamingRule`](../src/Rules/CheckRequiredMethodNamingRule.php)

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

<br>

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

## CheckUnneededSymfonyStyleUsageRule

SymfonyStyle service is not needed for only newline and text echo. Use PHP_EOL and concatenation instead

- class: [`Symplify\PHPStanRules\Rules\CheckUnneededSymfonyStyleUsageRule`](../src/Rules/CheckUnneededSymfonyStyleUsageRule.php)

```php
use Symfony\Component\Console\Style\SymfonyStyle;

class SomeClass
{
    private $symfonyStyle;

    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }

    public function run()
    {
        $this->symfonyStyle->writeln('Hi');
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
        echo 'Hi' . PHP_EOL;
    }
}
```

:+1:

<br>

## CheckUsedNamespacedNameOnClassNodeRule

Use `$class->namespaceName` instead of `$class->name` that only returns short class name

- class: [`Symplify\PHPStanRules\Rules\CheckUsedNamespacedNameOnClassNodeRule`](../src/Rules/CheckUsedNamespacedNameOnClassNodeRule.php)

```php
use PhpParser\Node\Stmt\Class_;

final class SomeClass
{
    public function run(Class_ $class)
    {
        $className = (string) $class->name;
    }
}
```

:x:

<br>

```php
use PhpParser\Node\Stmt\Class_;

final class SomeClass
{
    public function run(Class_ $class)
    {
        $className = (string) $class->namespacedName;
    }
}
```

:+1:

<br>

## ClassLikeCognitiveComplexityRule

Cognitive complexity of class/trait must be under specific limit

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\CognitiveComplexity\Rules\ClassLikeCognitiveComplexityRule`](../packages/cognitive-complexity/src/Rules/ClassLikeCognitiveComplexityRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\CognitiveComplexity\Rules\ClassLikeCognitiveComplexityRule
        tags: [phpstan.rules.rule]
        arguments:
            maxClassCognitiveComplexity: 10
```

↓

```php
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

:x:

<br>

```php
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

```yaml
services:
    -
        class: Symplify\PHPStanRules\CognitiveComplexity\Rules\ClassLikeCognitiveComplexityRule
        tags: [phpstan.rules.rule]
        arguments:
            limitsByTypes:
                Symfony\Component\Console\Command\Command: 5
```

↓

```php
use Symfony\Component\Console\Command\Command;

class SomeCommand extends Command
{
    public function configure()
    {
        $this->setName('...');
    }

    public function execute()
    {
        if (...) {
            // ...
        } else {
            // ...
        }
    }
}
```

:x:

<br>

```php
use Symfony\Component\Console\Command\Command;

class SomeCommand extends Command
{
    public function configure()
    {
        $this->setName('...');
    }

    public function execute()
    {
        return $this->externalService->resolve(...);
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

## DifferentMethodNameToParameterRule

Method name should be different to its parameter name, in a verb form

- class: [`Symplify\PHPStanRules\Rules\DifferentMethodNameToParameterRule`](../src/Rules/DifferentMethodNameToParameterRule.php)

```php
class SomeClass
{
    public function apple(string $apple)
    {
        // ...
    }
}
```

:x:

<br>

```php
class SomeClass
{
    public function eatApple(string $apple)
    {
        // ...
    }
}
```

:+1:

<br>

## ExcessiveParameterListRule

Method `"%s()"` is using too many parameters - %d. Make it under %d

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\ExcessiveParameterListRule`](../src/Rules/ExcessiveParameterListRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ExcessiveParameterListRule
        tags: [phpstan.rules.rule]
        arguments:
            maxParameterCount: 2
```

↓

```php
class SomeClass
{
    public function __construct($one, $two, $three)
    {
        // ...
    }
}
```

:x:

<br>

```php
class SomeClass
{
    public function __construct($one, $two)
    {
        // ...
    }
}
```

:+1:

<br>

## ExcessivePublicCountRule

Too many public elements on class - %d. Narrow it down under %d

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\ExcessivePublicCountRule`](../src/Rules/ExcessivePublicCountRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ExcessivePublicCountRule
        tags: [phpstan.rules.rule]
        arguments:
            maxPublicClassElementCount: 2
```

↓

```php
class SomeClass
{
    public $one;

    public $two;

    public $three;
}
```

:x:

<br>

```php
class SomeClass
{
    public $one;

    public $two;
}
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
class CheckboxController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
}
```

:x:

<br>

```php
class CheckboxRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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

## ForbiddenAnonymousClassRule

Anonymous class is not allowed.

- class: [`Symplify\PHPStanRules\Rules\ForbiddenAnonymousClassRule`](../src/Rules/ForbiddenAnonymousClassRule.php)

```php
new class() {
};
```

:x:

<br>

```php
class SomeClass
{
}

new SomeClass();
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

## ForbiddenAssignInIfRule

Assignment inside if is not allowed. Extract condition to extra variable on line above

- class: [`Symplify\PHPStanRules\Rules\ForbiddenAssignInIfRule`](../src/Rules/ForbiddenAssignInIfRule.php)

```php
if ($isRandom = mt_rand()) {
    // ...
}
```

:x:

<br>

```php
$isRandom = mt_rand();
if ($isRandom) {
    // ...
}
```

:+1:

<br>

## ForbiddenAssignInLoopRule

Assign in loop is not allowed.

- class: [`Symplify\PHPStanRules\Rules\ForbiddenAssignInLoopRule`](../src/Rules/ForbiddenAssignInLoopRule.php)

```php
foreach (...) {
    $value = new SmartFileInfo('a.php');
    if ($value) {
    }
}
```

:x:

<br>

```php
$value = new SmartFileInfo('a.php');
foreach (...) {
    if ($value) {
    }
}
```

:+1:

<br>

## ForbiddenCallOnTypeRule

Method call or Static Call on %s is not allowed

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\ForbiddenCallOnTypeRule`](../src/Rules/ForbiddenCallOnTypeRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ForbiddenCallOnTypeRule
        tags: [phpstan.rules.rule]
        arguments:
            forbiddenTypes:
                - Symfony\Component\DependencyInjection\Container
```

↓

```php
use Symfony\Component\DependencyInjection\Container;

class SomeClass
{
    /**
     * @var Container
     */
    private $some;

    public function __construct(Container $some)
    {
        $this->some = $some;
    }

    public function call()
    {
        $this->some->call();
    }
}
```

:x:

<br>

```php
use Other\SpecificService;

class SomeClass
{
    /**
     * @var SpecificService
     */
    private $specificService;

    public function __construct(SpecificService $specificService)
    {
        $this->specificService = $specificService;
    }

    public function call()
    {
        $this->specificService->call();
    }
}
```

:+1:

<br>

## ForbiddenComplexArrayConfigInSetRule

For complex configuration use value object over array

- class: [`Symplify\PHPStanRules\Rules\ForbiddenComplexArrayConfigInSetRule`](../src/Rules/ForbiddenComplexArrayConfigInSetRule.php)

```php
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

<br>

```php
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

## ForbiddenComplexFuncCallRule

Do not use "%s" function with complex content, make it more readable with extracted method or single-line statement

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\ForbiddenComplexFuncCallRule`](../src/Rules/ForbiddenComplexFuncCallRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ForbiddenComplexFuncCallRule
        tags: [phpstan.rules.rule]
        arguments:
            forbiddenComplexFunctions:
                - array_filter

            maximumStmtCount: 2
```

↓

```php
$filteredElements = array_filter($elemnets, function ($item) {
    if ($item) {
        return true;
    }

    if ($item === null) {
        return true;
    }

    return false;
};
```

:x:

<br>

```php
$filteredElements = array_filter($elemnets, function ($item) {
    return $item instanceof KeepItSimple;
};
```

:+1:

<br>

## ForbiddenDependencyByTypeRule

Object instance of "%s" is forbidden to be passed to constructor

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\ForbiddenDependencyByTypeRule`](../src/Rules/ForbiddenDependencyByTypeRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ForbiddenDependencyByTypeRule
        tags: [phpstan.rules.rule]
        arguments:
            forbiddenTypes:
                - EntityManager
```

↓

```php
class SomeClass
{
    public function __construct(EntityManager $entityManager)
    {
        // ...
    }
}
```

:x:

<br>

```php
class SomeClass
{
    public function __construct(ProductRepository $productRepository)
    {
        // ...
    }
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

## ForbiddenMethodCallOnNewRule

Method call on new expression is not allowed.

- class: [`Symplify\PHPStanRules\Rules\ForbiddenMethodCallOnNewRule`](../src/Rules/ForbiddenMethodCallOnNewRule.php)

```php
(new SomeClass())->run();
```

:x:

<br>

```php
$someClass = new SomeClass();
$someClass->run();
```

:+1:

<br>

## ForbiddenMethodCallOnTypeRule

Prevent using certain method calls on certains types

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\ForbiddenMethodCallOnTypeRule`](../src/Rules/ForbiddenMethodCallOnTypeRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ForbiddenMethodCallOnTypeRule
        tags: [phpstan.rules.rule]
        arguments:
            forbiddenMethodNamesByTypes:
                SpecificType:
                    - nope
```

↓

```php
class SomeClass
{
    public function process(SpecificType $specificType)
    {
        $specificType->nope();
    }
}
```

:x:

<br>

```php
class SomeClass
{
    public function process(SpecificType $specificType)
    {
        $specificType->yes();
    }
}
```

:+1:

<br>

## ForbiddenMethodOrStaticCallInForeachRule

Method nor static call in foreach is not allowed. Extract expression to a new variable assign on line before

- class: [`Symplify\PHPStanRules\Rules\ForbiddenMethodOrStaticCallInForeachRule`](../src/Rules/ForbiddenMethodOrStaticCallInForeachRule.php)

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

## ForbiddenMethodOrStaticCallInIfRule

Method nor static call in `if()` or `elseif()` is not allowed. Extract expression to a new variable assign on line before

- class: [`Symplify\PHPStanRules\Rules\ForbiddenMethodOrStaticCallInIfRule`](../src/Rules/ForbiddenMethodOrStaticCallInIfRule.php)

```php
$someObject = new SomeClass();
if ($someObject->getData($arg) === []) {
    // ...
}
```

:x:

<br>

```php
$someObject = new SomeClass();
$dataFirstArg = $someObject->getData($arg);
if ($dataFirstArg === []) {
    // ...
}
```

:+1:

<br>

## ForbiddenMultipleClassLikeInOneFileRule

Multiple class/interface/trait is not allowed in single file

- class: [`Symplify\PHPStanRules\Rules\ForbiddenMultipleClassLikeInOneFileRule`](../src/Rules/ForbiddenMultipleClassLikeInOneFileRule.php)

```php
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

## ForbiddenNestedCallInAssertMethodCallRule

Decouple method call in assert to standalone line to make test core more readable

- class: [`Symplify\PHPStanRules\Rules\ForbiddenNestedCallInAssertMethodCallRule`](../src/Rules/ForbiddenNestedCallInAssertMethodCallRule.php)

```php
use PHPUnit\Framework\TestCase;

final class SomeClass extends TestCase
{
    public function test()
    {
        $this->assetSame('oooo', $this->someMethodCall());
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
        $this->assetSame('oooo', $result);
    }
}
```

:+1:

<br>

## ForbiddenNestedForeachWithEmptyStatementRule

Nested foreach with empty statement is not allowed

- class: [`Symplify\PHPStanRules\Rules\ForbiddenNestedForeachWithEmptyStatementRule`](../src/Rules/ForbiddenNestedForeachWithEmptyStatementRule.php)

```php
$collectedFileErrors = [];

foreach ($errors as $fileErrors) {
    foreach ($fileErrors as $fileError) {
        $collectedFileErrors[] = $fileError;
    }
}
```

:x:

<br>

```php
$collectedFileErrors = [];

foreach ($fileErrors as $fileError) {
    $collectedFileErrors[] = $fileError;
}
```

:+1:

<br>

## ForbiddenNewInMethodRule

"new" in method `"%s->%s()"` is not allowed.

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\ForbiddenNewInMethodRule`](../src/Rules/ForbiddenNewInMethodRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ForbiddenNewInMethodRule
        tags: [phpstan.rules.rule]
        arguments:
            forbiddenClassMethods:
                PHPStan\Rules\Rule:
                    - getRule
```

↓

```php
use PHPStan\Rules\Rule;

class SomeRuleTest implements Rule
{
    protected function getRule(): Rule
    {
        return new SomeRule();
    }
}
```

:x:

<br>

```php
use PHPStan\Rules\Rule;

class SomeRuleTest implements Rule
{
    protected function getRule(): Rule
    {
        return $this->getService(SomeRule::class);
    }
}
```

:+1:

<br>

## ForbiddenNewOutsideFactoryServiceRule

"new" outside factory is not allowed for object type "%s"

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\ForbiddenNewOutsideFactoryServiceRule`](../src/Rules/ForbiddenNewOutsideFactoryServiceRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ForbiddenNewOutsideFactoryServiceRule
        tags: [phpstan.rules.rule]
        arguments:
            types:
                - AnotherObject
```

↓

```php
class SomeClass
{
    public function process()
    {
        $anotherObject = new AnotherObject();
        // ...
    }
}
```

:x:

<br>

```php
class SomeClass
{
    public function __construt(AnotherObjectFactory $anotherObjectFactory)
    {
        $this->anotherObjectFactory = $anotherObjectFactory;
    }

    public function process()
    {
        $anotherObject = $this->anotherObjectFactory = $anotherObjectFactory->create();
        // ...
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

## ForbiddenNullableParameterRule

Parameter "%s" cannot be nullable

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\ForbiddenNullableParameterRule`](../src/Rules/ForbiddenNullableParameterRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ForbiddenNullableParameterRule
        tags: [phpstan.rules.rule]
        arguments:
            forbiddenTypes:
                - PhpParser\Node

            allowedTypes:
                - PhpParser\Node\Scalar\String_
```

↓

```php
use PhpParser\Node;

class SomeClass
{
    public function run(?Node $node = null): void
    {
    }
}
```

:x:

<br>

```php
use PhpParser\Node;

class SomeClass
{
    public function run(Node $node): void
    {
    }
}
```

:+1:

<br>

## ForbiddenNullableReturnRule

Return type "%s" cannot be nullable

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\ForbiddenNullableReturnRule`](../src/Rules/ForbiddenNullableReturnRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ForbiddenNullableReturnRule
        tags: [phpstan.rules.rule]
        arguments:
            forbiddenTypes:
                - PhpParser\Node

            allowedTypes:
                - PhpParser\Node\Scalar\String_
```

↓

```php
use PhpParser\Node;

class SomeClass
{
    public function run(): ?Node
    {
    }
}
```

:x:

<br>

```php
use PhpParser\Node;

class SomeClass
{
    public function run(): Node
    {
    }
}
```

:+1:

<br>

## ForbiddenPrivateMethodByTypeRule

Private method in is not allowed here - it should only delegate to others. Decouple the private method to a new service class

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\ForbiddenPrivateMethodByTypeRule`](../src/Rules/ForbiddenPrivateMethodByTypeRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ForbiddenPrivateMethodByTypeRule
        tags: [phpstan.rules.rule]
        arguments:
            forbiddenTypes:
                - Command
```

↓

```php
class SomeCommand extends Command
{
    public function run()
    {
        $this->somePrivateMethod();
    }

    private function somePrivateMethod()
    {
        // ...
    }
}
```

:x:

<br>

```php
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

    public function run()
    {
        $this->externalService->someMethod();
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

## ForbiddenReturnValueOfIncludeOnceRule

Cannot return include_once/require_once

- class: [`Symplify\PHPStanRules\Rules\ForbiddenReturnValueOfIncludeOnceRule`](../src/Rules/ForbiddenReturnValueOfIncludeOnceRule.php)

```php
class SomeClass
{
    public function run()
    {
        return require_once 'Test.php';
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
        require_once 'Test.php';
    }
}
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

## FunctionLikeCognitiveComplexityRule

Cognitive complexity of function/method must be under specific limit

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\CognitiveComplexity\Rules\FunctionLikeCognitiveComplexityRule`](../packages/cognitive-complexity/src/Rules/FunctionLikeCognitiveComplexityRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\CognitiveComplexity\Rules\FunctionLikeCognitiveComplexityRule
        tags: [phpstan.rules.rule]
        arguments:
            maxMethodCognitiveComplexity: 5
```

↓

```php
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

:x:

<br>

```php
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

## IfImplementsInterfaceThenNewTypeRule

Class that implements specific interface, must use related class in `new SomeClass`

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\IfImplementsInterfaceThenNewTypeRule`](../src/Rules/IfImplementsInterfaceThenNewTypeRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\IfImplementsInterfaceThenNewTypeRule
        tags: [phpstan.rules.rule]
        arguments:
            newTypesByInterface:
                ConfigurableRuleInterface: ConfiguredCodeSample
```

↓

```php
class SomeRule implements ConfigurableRuleInterface
{
    public function some()
    {
        $codeSample = new CodeSample();
    }
}
```

:x:

<br>

```php
class SomeRule implements ConfigurableRuleInterface
{
    public function some()
    {
        $configuredCodeSample = new ConfiguredCodeSample();
    }
}
```

:+1:

<br>

## IfNewTypeThenImplementInterfaceRule

Class must implement "%s" interface

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\IfNewTypeThenImplementInterfaceRule`](../src/Rules/IfNewTypeThenImplementInterfaceRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\IfNewTypeThenImplementInterfaceRule
        tags: [phpstan.rules.rule]
        arguments:
            interfacesByNewTypes:
                ConfiguredCodeSample: ConfiguredRuleInterface
```

↓

```php
class SomeRule
{
    public function run()
    {
        return new ConfiguredCodeSample('...');
    }
}
```

:x:

<br>

```php
class SomeRule implements ConfiguredRuleInterface
{
    public function run()
    {
        return new ConfiguredCodeSample('...');
    }
}
```

:+1:

<br>

## InvokableControllerByRouteNamingRule

Use controller class name based on route name instead

- class: [`Symplify\PHPStanRules\Rules\InvokableControllerByRouteNamingRule`](../src/Rules/InvokableControllerByRouteNamingRule.php)

```php
use Symfony\Component\Routing\Annotation\Route;

final class SecurityController extends AbstractController
{
    #[Route(path: '/logout', name: 'logout')]
    public function __invoke(): Response
    {
    }
}
```

:x:

<br>

```php
use Symfony\Component\Routing\Annotation\Route;

final class LogoutController extends AbstractController
{
    #[Route(path: '/logout', name: 'logout')]
    public function __invoke(): Response
    {
    }
}
```

:+1:

<br>

## MatchingTypeConstantRule

Constant type should be "%s", but is "%s"

- class: [`Symplify\PHPStanRules\Rules\MatchingTypeConstantRule`](../src/Rules/MatchingTypeConstantRule.php)

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

<br>

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
    public getItems()
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
    public getItems()
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

## NoChainMethodCallRule

Do not use chained method calls. Put each on separated lines.

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\ObjectCalisthenics\Rules\NoChainMethodCallRule`](../packages/object-calisthenics/src/Rules/NoChainMethodCallRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\ObjectCalisthenics\Rules\NoChainMethodCallRule
        tags: [phpstan.rules.rule]
        arguments:
            allowedChainTypes:
                - AllowedFluent
```

↓

```php
$this->runThis()
    ->runThat();

$fluentClass = new AllowedFluent();
$fluentClass->one()
    ->two();
```

:x:

<br>

```php
$this->runThis();
$this->runThat();

$fluentClass = new AllowedFluent();
$fluentClass->one()
    ->two();
```

:+1:

<br>

## NoClassWithStaticMethodWithoutStaticNameRule

Class "%s" with static method must have "Static" in its name it explicit

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

- class: [`Symplify\PHPStanRules\Rules\NoConstantInterfaceRule`](../src/Rules/NoConstantInterfaceRule.php)

```php
interface SomeContract
{
    public const YES = 'yes';

    public const NO = 'ne';
}
```

:x:

<br>

```php
class SomeValues
{
    public const YES = 'yes';

    public const NO = 'ne';
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
    protected function setUp()
    {
        // ...
    }
}
```

:+1:

<br>

## NoContainerInjectionInConstructorRule

Instead of container injection, use specific service

- class: [`Symplify\PHPStanRules\Rules\NoContainerInjectionInConstructorRule`](../src/Rules/NoContainerInjectionInConstructorRule.php)

```php
class SomeClass
{
    public function __construct(ContainerInterface $container)
    {
        $this->someDependency = $container->get('...');
    }
}
```

:x:

<br>

```php
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

## NoDefaultParameterValueRule

Parameter "%s" cannot have default value

- class: [`Symplify\PHPStanRules\Rules\NoDefaultParameterValueRule`](../src/Rules/NoDefaultParameterValueRule.php)

```php
class SomeClass
{
    public function run($value = true): void
    {
    }
}
```

:x:

<br>

```php
class SomeClass
{
    public function run($value): void
    {
    }
}
```

:+1:

<br>

## NoDependencyJugglingRule

Use dependency injection instead of dependency juggling

- class: [`Symplify\PHPStanRules\Rules\NoDependencyJugglingRule`](../src/Rules/NoDependencyJugglingRule.php)

```php
public function __construct($service)
{
    $this->service = $service;
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

## NoDuplicatedShortClassNameRule

Class with base "%s" name is already used in "%s". Use unique name to make classes easy to recognize

- class: [`Symplify\PHPStanRules\Rules\NoDuplicatedShortClassNameRule`](../src/Rules/NoDuplicatedShortClassNameRule.php)

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

## NoElseAndElseIfRule

Do not use "else/elseif". Refactor to early return

- class: [`Symplify\PHPStanRules\ObjectCalisthenics\Rules\NoElseAndElseIfRule`](../packages/object-calisthenics/src/Rules/NoElseAndElseIfRule.php)

```php
if (...) {
    return 1;
} else {
    return 2;
}
```

:x:

<br>

```php
if (...) {
    return 1;
}

return 2;
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

## NoInheritanceRule

Do not inherit from abstract class, better use composition

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\NoInheritanceRule`](../src/Rules/NoInheritanceRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\NoInheritanceRule
        tags: [phpstan.rules.rule]
        arguments:
            allowedParentTypes:
                - AnotherParent
```

↓

```php
class SomeClass extends AbstratcClass
{
    public function run()
    {
        $this->parentMethod();
    }
}
```

:x:

<br>

```php
class SomeClass
{
    private function __construct(
        private $dependency Dependency
    ) {
    }

    public function run()
    {
        $this->dependency->otherMethod();
    }
}
```

:+1:

<br>

## NoInjectOnFinalRule

Use constructor on final classes, instead of property injection

- class: [`Symplify\PHPStanRules\Rules\NoInjectOnFinalRule`](../src/Rules/NoInjectOnFinalRule.php)

```php
final class SomePresenter
{
    /**
     * @inject
     */
    public $property;
}
```

:x:

<br>

```php
abstract class SomePresenter
{
    /**
     * @inject
     */
    public $property;
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
            $object = new self();
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

## NoMissingDirPathRule

The path "%s" was not found

- class: [`Symplify\PHPStanRules\Rules\NoMissingDirPathRule`](../src/Rules/NoMissingDirPathRule.php)

```php
class SomeClass
{
    public function run()
    {
        return __DIR__ . '/missing_location.txt';
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
        return __DIR__ . '/existing_location.txt';
    }
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
final class SomeClass
{
    public function run()
    {
        $values = [];
        $values['person']['name'] = 'Tom';
        $values['person']['surname'] = 'Dev';
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
        $values = [];
        $values[] = new Person('Tom', 'Dev');
    }
}
```

:+1:

<br>

## NoNestedFuncCallRule

Use separate function calls with readable variable names

- class: [`Symplify\PHPStanRules\Rules\NoNestedFuncCallRule`](../src/Rules/NoNestedFuncCallRule.php)

```php
class SomeClass
{
    public function run()
    {
        return array_filter(array_map($callback, $items));
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
        $mappedItems = array_map($callback, $items);
        return array_filter($mappedItems);
    }
}
```

:+1:

<br>

## NoNetteArrayAccessInControlRule

Avoid using magical unclear array access and use explicit `"$this->getComponent()"` instead

- class: [`Symplify\PHPStanRules\Rules\NoNetteArrayAccessInControlRule`](../src/Rules/NoNetteArrayAccessInControlRule.php)

```php
use Nette\Application\UI\Presenter;

class SomeClass extends Presenter
{
    public function render()
    {
        return $this['someControl'];
    }
}
```

:x:

<br>

```php
use Nette\Application\UI\Presenter;

class SomeClass extends Presenter
{
    public function render()
    {
        return $this->getComponent('someControl');
    }
}
```

:+1:

<br>

## NoNetteDoubleTemplateAssignRule

Avoid double template variable override of "%s"

- class: [`Symplify\PHPStanRules\Rules\NoNetteDoubleTemplateAssignRule`](../src/Rules/NoNetteDoubleTemplateAssignRule.php)

```php
use Nette\Application\UI\Presenter;

class SomeClass extends Presenter
{
    public function render()
    {
        $this->template->key = '1';
        $this->template->key = '2';
    }
}
```

:x:

<br>

```php
use Nette\Application\UI\Presenter;

class SomeClass extends Presenter
{
    public function render()
    {
        $this->template->key = '2';
    }
}
```

:+1:

<br>

## NoNetteInjectAndConstructorRule

Use either `__construct()` or injects, not both

- class: [`Symplify\PHPStanRules\Rules\NoNetteInjectAndConstructorRule`](../src/Rules/NoNetteInjectAndConstructorRule.php)

```php
class SomeClass
{
    private $someType;

    public function __construct()
    {
        // ...
    }

    public function injectSomeType($someType)
    {
        $this->someType = $someType;
    }
}
```

:x:

<br>

```php
class SomeClass
{
    private $someType;

    public function __construct($someType)
    {
        $this->someType = $someType;
    }
}
```

:+1:

<br>

## NoNetteRenderMissingVariableRule

Passed "%s" variable that are not used in the template

- class: [`Symplify\PHPStanRules\Rules\NoNetteRenderMissingVariableRule`](../src/Rules/NoNetteRenderMissingVariableRule.php)

```php
use Nette\Application\UI\Control;

final class SomeControl extends Control
{
    public function render()
    {
        $this->template->render(__DIR__ . '/some_file.latte', [
            'non_existing_variable' => 'value',
        ]);
    }
}
```

:x:

<br>

```php
use Nette\Application\UI\Control;

final class SomeControl extends Control
{
    public function render()
    {
        $this->template->render(__DIR__ . '/some_file.latte', [
            'existing_variable' => 'value',
        ]);
    }
}
```

:+1:

<br>

## NoNetteRenderUnusedVariableRule

Missing "%s" variable that are not passed to the template

- class: [`Symplify\PHPStanRules\Rules\NoNetteRenderUnusedVariableRule`](../src/Rules/NoNetteRenderUnusedVariableRule.php)

```php
use Nette\Application\UI\Control;

final class SomeControl extends Control
{
    public function render()
    {
        $this->template->render(__DIR__ . '/some_file.latte');
    }
}
```

:x:

<br>

```php
use Nette\Application\UI\Control;

final class SomeControl extends Control
{
    public function render()
    {
        $this->template->render(__DIR__ . '/some_file.latte', [
            'existing_variable' => 'value',
        ]);
    }
}
```

:+1:

<br>

## NoNetteTemplateVariableReadRule

Avoid `$this->template->variable` for read access, as it can be defined anywhere. Use local `$variable` instead

- class: [`Symplify\PHPStanRules\Rules\NoNetteTemplateVariableReadRule`](../src/Rules/NoNetteTemplateVariableReadRule.php)

```php
use Nette\Application\UI\Presenter;

class SomeClass extends Presenter
{
    public function render()
    {
        if ($this->template->key === 'value') {
            return;
        }
    }
}
```

:x:

<br>

```php
use Nette\Application\UI\Presenter;

class SomeClass extends Presenter
{
    public function render()
    {
        $this->template->key = 'value';
    }
}
```

:+1:

<br>

## NoNewOutsideFactoryRule

Use decoupled factory service to create "%s" object

- class: [`Symplify\PHPStanRules\Rules\NoNewOutsideFactoryRule`](../src/Rules/NoNewOutsideFactoryRule.php)

```php
final class SomeClass
{
    public function run()
    {
        return new SomeValueObject();
    }
}
```

:x:

<br>

```php
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
    private array $property;
}
```

:+1:

<br>

## NoNullablePropertyRule

Use required typed property over of nullable property

- class: [`Symplify\PHPStanRules\Rules\NoNullablePropertyRule`](../src/Rules/NoNullablePropertyRule.php)

```php
final class SomeClass
{
    private ?DateTime $property = null;
}
```

:x:

<br>

```php
final class SomeClass
{
    private DateTime $property;
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

## NoPostIncPostDecRule

Post operation are forbidden, as they make 2 values at the same line. Use pre instead

- class: [`Symplify\PHPStanRules\Rules\NoPostIncPostDecRule`](../src/Rules/NoPostIncPostDecRule.php)

```php
class SomeClass
{
    public function run($value = 1)
    {
        // 1 ... 0
        if ($value--) {
        }
    }
}
```

:x:

<br>

```php
class SomeClass
{
    public function run($value = 1)
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

- class: [`Symplify\PHPStanRules\Rules\NoProtectedElementInFinalClassRule`](../src/Rules/NoProtectedElementInFinalClassRule.php)

```php
final class SomeClass
{
    private function run()
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

## NoSetterClassMethodRule

Setter `"%s()"` is not allowed. Use constructor injection or behavior name instead, e.g. `"changeName()"`

- class: [`Symplify\PHPStanRules\ObjectCalisthenics\Rules\NoSetterClassMethodRule`](../packages/object-calisthenics/src/Rules/NoSetterClassMethodRule.php)

```php
final class SomeClass
{
    public function setName(string $name)
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
    public function __construct(string $name)
    {
        // ...
    }
}
```

:+1:

<br>

## NoSetterOnServiceRule

Do not use setter on a service

- class: [`Symplify\PHPStanRules\Rules\NoSetterOnServiceRule`](../src/Rules/NoSetterOnServiceRule.php)

```php
class SomeService
{
    public function setSomeValue($value)
    {
    }
}
```

:x:

<br>

```php
class SomeEntity
{
    public function setSomeValue($value)
    {
    }
}
```

:+1:

<br>

## NoShortNameRule

Do not name "%s", shorter than %d chars

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\ObjectCalisthenics\Rules\NoShortNameRule`](../packages/object-calisthenics/src/Rules/NoShortNameRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\ObjectCalisthenics\Rules\NoShortNameRule
        tags: [phpstan.rules.rule]
        arguments:
            minNameLength: 3
```

↓

```php
function is()
{
}
```

:x:

<br>

```php
function isClass()
{
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

## NoSuffixValueObjectClassRule

Value Object class name "%s" must be withotu "ValueObject" suffix. The correct class name is "%s".

- class: [`Symplify\PHPStanRules\Rules\NoSuffixValueObjectClassRule`](../src/Rules/NoSuffixValueObjectClassRule.php)

```php
class SomeValueObject
{
    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
```

:x:

<br>

```php
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

## OnlyOneClassMethodRule

Allow only one of methods to be implemented on type

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\OnlyOneClassMethodRule`](../src/Rules/OnlyOneClassMethodRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\OnlyOneClassMethodRule
        tags: [phpstan.rules.rule]
        arguments:
            onlyOneMethodsByType:
                CheckedInterface:
                    - run
                    - hide
```

↓

```php
class SomeClass implements CheckedInterface
{
    public function run()
    {
    }

    public function hide()
    {
    }
}
```

:x:

<br>

```php
class SomeClass implements CheckedInterface
{
    public function run()
    {
    }
}
```

:+1:

<br>

## PreferConstantValueRule

Use defined constant %s::%s over string %s

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\PreferConstantValueRule`](../src/Rules/PreferConstantValueRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\PreferConstantValueRule
        tags: [phpstan.rules.rule]
        arguments:
            constantHoldingObjects:
                Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection:
                    - "REQUIRE(_.*)?"
                    - "AUTOLOAD(_.*)?"
```

↓

```php
class SomeClass
{
    public function run()
    {
        return 'require';
    }
}
```

:x:

<br>

```php
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection;

class SomeClass
{
    public function run()
    {
        return ComposerJsonSection::REQUIRE;
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
    #Route()
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

## PreferredMethodCallOverFuncCallRule

Use `"%s->%s()"` method call over `"%s()"` func call

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\PreferredMethodCallOverFuncCallRule`](../src/Rules/PreferredMethodCallOverFuncCallRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\PreferredMethodCallOverFuncCallRule
        tags: [phpstan.rules.rule]
        arguments:
            funcCallToPreferredMethodCalls:
                strlen:
                    - Nette\Utils\Strings
                    - length
```

↓

```php
class SomeClass
{
    public function run($value)
    {
        return strlen($value);
    }
}
```

:x:

<br>

```php
use Nette\Utils\Strings;

class SomeClass
{
    public function __construct(Strings $strings)
    {
        $this->strings = $strings;
    }

    public function run($value)
    {
        return $this->strings->length($value);
    }
}
```

:+1:

<br>

## PreferredMethodCallOverIdenticalCompareRule

Use "%s->%s('value')" method call over `"%s->%s()` === 'value'" comparison

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\PreferredMethodCallOverIdenticalCompareRule`](../src/Rules/PreferredMethodCallOverIdenticalCompareRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\PreferredMethodCallOverIdenticalCompareRule
        tags: [phpstan.rules.rule]
        arguments:
            identicalToPreferredMethodCalls:
                Rector\Core\Rector\AbstractRector:
                    getName: isName
```

↓

```php
class SomeClass
{
    public function run()
    {
        $this->getName($node) === 'hey';
        $this->getName($node) !== 'hey';
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
        $this->isName($node, 'hey');
        ! $this->isName($node, 'hey');
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

    private function setUp()
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
    public function testFoo($value)
    {
        $this->obj->x = $value;
        $this->assertTrue($this->obj->x);
    }
}
```

:+1:

<br>

## PreferredStaticCallOverFuncCallRule

Use `"%s::%s()"` static call over `"%s()"` func call

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\PreferredStaticCallOverFuncCallRule`](../src/Rules/PreferredStaticCallOverFuncCallRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\PreferredStaticCallOverFuncCallRule
        tags: [phpstan.rules.rule]
        arguments:
            funcCallToPreferredStaticCalls:
                strlen:
                    - Nette\Utils\Strings
                    - length
```

↓

```php
class SomeClass
{
    public function run($value)
    {
        return strlen($value);
    }
}
```

:x:

<br>

```php
use Nette\Utils\Strings;

class SomeClass
{
    public function run($value)
    {
        return Strings::length($value);
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

## PreventDoubleSetParameterRule

Set param value is overriden. Merge it to previous set above

- class: [`Symplify\PHPStanRules\Rules\PreventDoubleSetParameterRule`](../src/Rules/PreventDoubleSetParameterRule.php)

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('some_param', [1]);
    $parameters->set('some_param', [2]);
};
```

:x:

<br>

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('some_param', [1, 2]);
};
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
            minimumLineCount: 1
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
    #[Route('/path')]
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
    #[Route(path: '/path')]
    public function someAction()
    {
    }
}
```

:+1:

<br>

## RequireClassTypeInClassMethodByTypeRule

Required specific class-string types in defined methods

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\RequireClassTypeInClassMethodByTypeRule`](../src/Rules/RequireClassTypeInClassMethodByTypeRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\RequireClassTypeInClassMethodByTypeRule
        tags: [phpstan.rules.rule]
        arguments:
            requiredTypeInMethodByClass:
                SomeTypeInterface:
                    someMethod: PhpParser\Node
```

↓

```php
class SomeClass implements SomeTypeInterface
{
    /**
     * @return string[]
     */
    public function someMethod(): array
    {
        return [AnyClass::class];
    }
}
```

:x:

<br>

```php
use PhpParser\Node\Scalar\String_;

class SomeClass implements SomeTypeInterface
{
    /**
     * @return string[]
     */
    public function someMethod(): array
    {
        return [String_::class];
    }
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

Parameter argument on position %d must use %s constant

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\RequireConstantInMethodCallPositionRule`](../src/Rules/RequireConstantInMethodCallPositionRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\RequireConstantInMethodCallPositionRule
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

## RequireDataProviderTestMethodRule

The `"%s()"` method must use data provider

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\RequireDataProviderTestMethodRule`](../src/Rules/RequireDataProviderTestMethodRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\RequireDataProviderTestMethodRule
        tags: [phpstan.rules.rule]
        arguments:
            classesRequiringDataProvider:
                - *RectorTestCase
```

↓

```php
class SomeRectorTestCase extends RectorTestCase
{
    public function test()
    {
    }
}
```

:x:

<br>

```php
class SomeRectorTestCase extends RectorTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test($value)
    {
    }

    public function provideData()
    {
        // ...
    }
}
```

:+1:

<br>

## RequireInvokableControllerRule

Use invokable controller with `__invoke()` method instead of named action method

- class: [`Symplify\PHPStanRules\Rules\RequireInvokableControllerRule`](../src/Rules/RequireInvokableControllerRule.php)

```php
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class SomeController extends AbstractController
{
    /**
     * @Route()
     */
    public function someMethod()
    {
    }
}
```

:x:

<br>

```php
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class SomeController extends AbstractController
{
    /**
     * @Route()
     */
    public function __invoke()
    {
    }
}
```

:+1:

<br>

## RequireMethodCallArgumentConstantRule

Method call argument on position %d must use constant (e.g. "Option::NAME") over value

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\RequireMethodCallArgumentConstantRule`](../src/Rules/RequireMethodCallArgumentConstantRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\RequireMethodCallArgumentConstantRule
        tags: [phpstan.rules.rule]
        arguments:
            constantArgByMethodByType:
                SomeClass:
                    call:
                        - 0
```

↓

```php
class AnotherClass
{
    public function run(SomeClass $someClass)
    {
        $someClass->call('name');
    }
}
```

:x:

<br>

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

<br>

## RequireNewArgumentConstantRule

New expression argument on position %d must use constant over value

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\RequireNewArgumentConstantRule`](../src/Rules/RequireNewArgumentConstantRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\RequireNewArgumentConstantRule
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

## RequireQuoteStringValueSprintfRule

"%s" in `sprintf()` format must be quoted

- class: [`Symplify\PHPStanRules\Rules\RequireQuoteStringValueSprintfRule`](../src/Rules/RequireQuoteStringValueSprintfRule.php)

```php
class SomeClass
{
    public function run()
    {
        echo sprintf('%s value', $variable);
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
        echo sprintf('"%s" value', $variable);
    }
}
```

:+1:

<br>

## RequireSkipPrefixForRuleSkippedFixtureRule

Skipped tested file must start with "Skip" prefix

- class: [`Symplify\PHPStanRules\Rules\RequireSkipPrefixForRuleSkippedFixtureRule`](../src/Rules/RequireSkipPrefixForRuleSkippedFixtureRule.php)

```php
use PHPStan\Testing\RuleTestCase;

final class SomeRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|int> $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/NewWithInterface.php', []];
    }

    protected function getRule(): Rule
    {
        return new SomeRule());
    }
}
```

:x:

<br>

```php
use PHPStan\Testing\RuleTestCase;

final class SomeRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|int> $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipNewWithInterface.php', []];
    }

    protected function getRule(): Rule
    {
        return new SomeRule());
    }
}
```

:+1:

<br>

## RequireStringArgumentInConstructorRule

Use quoted string in constructor "new `%s()"` argument on position %d instead of "::class. It prevent scoping of the class in building prefixed package.

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

## RequireStringArgumentInMethodCallRule

Use quoted string in method call `"%s()"` argument on position %d instead of "::class. It prevent scoping of the class in building prefixed package.

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\RequireStringArgumentInMethodCallRule`](../src/Rules/RequireStringArgumentInMethodCallRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\RequireStringArgumentInMethodCallRule
        tags: [phpstan.rules.rule]
        arguments:
            stringArgPositionByMethodByType:
                SomeClass:
                    someMethod:
                        - 0
```

↓

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

<br>

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

## RequireTemplateInNetteControlRule

Set control template explicitly in `$this->template->setFile(...)` or `$this->template->render(...)`

- class: [`Symplify\PHPStanRules\Rules\RequireTemplateInNetteControlRule`](../src/Rules/RequireTemplateInNetteControlRule.php)

```php
use Nette\Application\UI\Control;

final class SomeControl extends Control
{
    public function render()
    {
    }
}
```

:x:

<br>

```php
use Nette\Application\UI\Control;

final class SomeControl extends Control
{
    public function render()
    {
        $this->template->render('some_file.latte');
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
        $tihs->run();
    }
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

## ServiceAndValueObjectHaveSameStartsRule

Make specific service suffix to use similar value object names for configuring in Symfony configs

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\ServiceAndValueObjectHaveSameStartsRule`](../src/Rules/ServiceAndValueObjectHaveSameStartsRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ServiceAndValueObjectHaveSameStartsRule
        tags: [phpstan.rules.rule]
        arguments:
            classSuffixes:
                - Rector
```

↓

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(SomeRector::class)
        ->call('configure', [[new Another()]]);
};
```

:x:

<br>

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(SomeRector::class)
        ->call('configure', [[new Some()]]);
};
```

:+1:

<br>

## SingleIndentationInMethodRule

Do not indent more than %dx in class methods

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\ObjectCalisthenics\Rules\SingleIndentationInMethodRule`](../packages/object-calisthenics/src/Rules/SingleIndentationInMethodRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\ObjectCalisthenics\Rules\SingleIndentationInMethodRule
        tags: [phpstan.rules.rule]
        arguments:
            maxNestingLevel:
                - 2
```

↓

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

<br>

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

<br>

## SingleNetteInjectMethodRule

Use single inject*() class method per class

- class: [`Symplify\PHPStanRules\Rules\SingleNetteInjectMethodRule`](../src/Rules/SingleNetteInjectMethodRule.php)

```php
class SomeClass
{
    private $type;

    private $anotherType;

    public function injectOne(Type $type)
    {
        $this->type = $type;
    }

    public function injectTwo(AnotherType $anotherType)
    {
        $this->anotherType = $anotherType;
    }
}
```

:x:

<br>

```php
class SomeClass
{
    private $type;

    private $anotherType;

    public function injectSomeClass(Type $type, AnotherType $anotherType) {
        $this->type = $type;
        $this->anotherType = $anotherType;
    }
}
```

:+1:

<br>

## SuffixInterfaceRule

Interface name "%s" must be suffixed with "Interface"

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

Trait name "%s" must be suffixed with "Trait"

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

## TooDeepNewClassNestingRule

new <class> is limited to %d "new <class>(new <class>))" nesting to each other. You have %d nesting.

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\TooDeepNewClassNestingRule`](../src/Rules/TooDeepNewClassNestingRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\TooDeepNewClassNestingRule
        tags: [phpstan.rules.rule]
        arguments:
            maxNewClassNesting: 2
```

↓

```php
$someObject = new A(new B(new C()));
```

:x:

<br>

```php
$firstObject = new B(new C());
$someObject = new A($firstObject);
```

:+1:

<br>

## TooLongClassLikeRule

%s has %d lines, it is too long. Shorted it under %d lines

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\ObjectCalisthenics\Rules\TooLongClassLikeRule`](../packages/object-calisthenics/src/Rules/TooLongClassLikeRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\ObjectCalisthenics\Rules\TooLongClassLikeRule
        tags: [phpstan.rules.rule]
        arguments:
            maxClassLikeLength: 3
```

↓

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

<br>

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

<br>

## TooLongFunctionLikeRule

%s has %d lines, it is too long. Shorted it under %d lines

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\ObjectCalisthenics\Rules\TooLongFunctionLikeRule`](../packages/object-calisthenics/src/Rules/TooLongFunctionLikeRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\ObjectCalisthenics\Rules\TooLongFunctionLikeRule
        tags: [phpstan.rules.rule]
        arguments:
            maxFunctionLikeLength: 3
```

↓

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

<br>

```php
function some()
{
    return (...) ? 1 : 2;
}
```

:+1:

<br>

## TooLongVariableRule

Variable "$%s" is too long with %d chars. Narrow it under %d chars

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\Rules\TooLongVariableRule`](../src/Rules/TooLongVariableRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\TooLongVariableRule
        tags: [phpstan.rules.rule]
        arguments:
            maxVariableLength: 10
```

↓

```php
class SomeClass
{
    public function run()
    {
        return $superLongVariableName;
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
        return $shortName;
    }
}
```

:+1:

<br>

## TooManyMethodsRule

Method has too many methods %d. Try narrowing it down under %d

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\ObjectCalisthenics\Rules\TooManyMethodsRule`](../packages/object-calisthenics/src/Rules/TooManyMethodsRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\ObjectCalisthenics\Rules\TooManyMethodsRule
        tags: [phpstan.rules.rule]
        arguments:
            maxMethodCount: 1
```

↓

```php
class SomeClass
{
    public function firstMethod()
    {
    }

    public function secondMethod()
    {
    }
}
```

:x:

<br>

```php
class SomeClass
{
    public function firstMethod()
    {
    }
}
```

:+1:

<br>

## TooManyPropertiesRule

Class has too many properties %d. Try narrowing it down under %d

:wrench: **configure it!**

- class: [`Symplify\PHPStanRules\ObjectCalisthenics\Rules\TooManyPropertiesRule`](../packages/object-calisthenics/src/Rules/TooManyPropertiesRule.php)

```yaml
services:
    -
        class: Symplify\PHPStanRules\ObjectCalisthenics\Rules\TooManyPropertiesRule
        tags: [phpstan.rules.rule]
        arguments:
            maxPropertyCount: 2
```

↓

```php
class SomeClass
{
    private $some;

    private $another;

    private $third;
}
```

:x:

<br>

```php
class SomeClass
{
    private $some;

    private $another;
}
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

## ValidNetteInjectRule

Nette `@inject` annotation must be valid

- class: [`Symplify\PHPStanRules\Rules\ValidNetteInjectRule`](../src/Rules/ValidNetteInjectRule.php)

```php
class SomeClass
{
    /**
     * @injected
     * @var
     */
    public $someDependency;
}
```

:x:

<br>

```php
class SomeClass
{
    /**
     * @inject
     * @var
     */
    public $someDependency;
}
```

:+1:

<br>
