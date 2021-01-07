# 119 Rules Overview

## AnnotateRegexClassConstWithRegexLinkRule

Add regex101.com `link` to that shows the regex in practise, so it will be easier to maintain in case of bug/extension in the future

- class: `Symplify\PHPStanRules\Rules\AnnotateRegexClassConstWithRegexLinkRule`

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

- class: `Symplify\PHPStanRules\Rules\BoolishClassMethodPrefixRule`

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

%s namespace "%s" does not follow PSR-4 configuration in `composer.json`

- class: `Symplify\PHPStanRules\Rules\CheckClassNamespaceFollowPsr4Rule`

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

- class: `Symplify\PHPStanRules\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule`

```php
class SomeClass
{
    public function someMethod()
    {
        $mainPath = getcwd() . '/absolute_path';
        // ...
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
        // ...
        return $this->mainPath;
    }
}
```

:+1:

<br>

## CheckConstantStringValueFormatRule

`Constant` string value need to only have small letters, _, -, . and numbers

- class: `Symplify\PHPStanRules\Rules\CheckConstantStringValueFormatRule`

```php
class SomeClass
{
    private const FOO = '$not_ok$';
}
```

:x:

<br>

```php
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

`%s()` called in `configure()`, must be called with `%s()` in `execute()` in "Symfony\Component\Console\Command\Command" type

- class: `Symplify\PHPStanRules\Rules\CheckOptionArgumentCommandRule`

```php
class SomeClass extends Command
{
    protected function configure(): void
    {
        $this->addOption(Option::CATEGORIZE, null, InputOption::VALUE_NONE, 'Group in categories');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $shouldCategorize = (bool) $input->getArgument(Option::CATEGORIZE);
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
        $this->addOption(Option::CATEGORIZE, null, InputOption::VALUE_NONE, 'Group in categories');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $shouldCategorize = (bool) $input->getOption(Option::CATEGORIZE);
    }
}
```

:+1:

<br>

## CheckParentChildMethodParameterTypeCompatibleRule

Method parameters must be compatible with its parent

- class: `Symplify\PHPStanRules\Rules\CheckParentChildMethodParameterTypeCompatibleRule`

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

## CheckRequiredAbstractKeywordForClassNameStartWithAbstractRule

Class name start with Abstract must have abstract keyword

- class: `Symplify\PHPStanRules\Rules\CheckRequiredAbstractKeywordForClassNameStartWithAbstractRule`

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

## CheckRequiredAutowireAutoconfigurePublicUsedInConfigServiceRule

`autowire()`, `autoconfigure()`, and `public()` are required in config service

- class: `Symplify\PHPStanRules\Rules\CheckRequiredAutowireAutoconfigurePublicUsedInConfigServiceRule`

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

- class: `Symplify\PHPStanRules\Rules\CheckRequiredInterfaceInContractNamespaceRule`

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

## CheckTraitMethodOnlyDelegateOtherClassRule

Trait method `"%s()"` should not contain any logic, but only delegate to other class call

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

<br>

```php
trait SomeTrait
{
    /**
     * @required
     */
    public $someDependency;

    public function someDelegateCall()
    {
        $this->someDependency->singleDelegateCall();
    }
}
```

:+1:

<br>

## CheckTypehintCallerTypeRule

Parameter %d should use "%s" type as the only type passed to this method

- class: `Symplify\PHPStanRules\Rules\CheckTypehintCallerTypeRule`

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

- class: `Symplify\PHPStanRules\Rules\CheckUnneededSymfonyStyleUsageRule`

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

- class: `Symplify\PHPStanRules\Rules\CheckUsedNamespacedNameOnClassNodeRule`

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

- class: `Symplify\PHPStanRules\CognitiveComplexity\Rules\ClassLikeCognitiveComplexityRule`

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

Class "%s" should have suffix "%s" by parent class/interface

- class: `Symplify\PHPStanRules\Rules\ClassNameRespectsParentSuffixRule`

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

## ExcessiveParameterListRule

Method `"%s()"` is using too many parameters - %d. Make it under %d

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\ExcessiveParameterListRule`

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

- class: `Symplify\PHPStanRules\Rules\ExcessivePublicCountRule`

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

- class: `Symplify\PHPStanRules\Rules\ExclusiveDependencyRule`

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

## ForbiddenAnonymousClassRule

Anonymous class is not allowed.

- class: `Symplify\PHPStanRules\Rules\ForbiddenAnonymousClassRule`

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

- class: `Symplify\PHPStanRules\Rules\ForbiddenArrayDestructRule`

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

- class: `Symplify\PHPStanRules\Rules\ForbiddenArrayWithStringKeysRule`

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

Assignment inside if is not allowed. `Extract` condition to extra variable on line above

- class: `Symplify\PHPStanRules\Rules\ForbiddenAssignInIfRule`

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

## ForbiddenCallOnTypeRule

Method call or Static Call on %s is not allowed

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\ForbiddenCallOnTypeRule`

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

- class: `Symplify\PHPStanRules\Rules\ForbiddenComplexArrayConfigInSetRule`

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

## ForbiddenDependencyByTypeRule

Object instance of "%s" is forbidden to be passed to constructor

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\ForbiddenDependencyByTypeRule`

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

- class: `Symplify\PHPStanRules\Rules\ForbiddenFuncCallRule`

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

- class: `Symplify\PHPStanRules\Rules\ForbiddenMethodCallOnNewRule`

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

- class: `Symplify\PHPStanRules\Rules\ForbiddenMethodCallOnTypeRule`

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

Method nor static call in foreach is not allowed. `Extract` expression to a new variable assign on line before

- class: `Symplify\PHPStanRules\Rules\ForbiddenMethodOrStaticCallInForeachRule`

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

Method nor static call in `if()` or `elseif()` is not allowed. `Extract` expression to a new variable assign on line before

- class: `Symplify\PHPStanRules\Rules\ForbiddenMethodOrStaticCallInIfRule`

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

- class: `Symplify\PHPStanRules\Rules\ForbiddenMultipleClassLikeInOneFileRule`

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

## ForbiddenNestedForeachWithEmptyStatementRule

Nested foreach with empty statement is not allowed

- class: `Symplify\PHPStanRules\Rules\ForbiddenNestedForeachWithEmptyStatementRule`

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

- class: `Symplify\PHPStanRules\Rules\ForbiddenNewInMethodRule`

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

- class: `Symplify\PHPStanRules\Rules\ForbiddenNewOutsideFactoryServiceRule`

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

- class: `Symplify\PHPStanRules\Rules\ForbiddenNodeRule`

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

- class: `Symplify\PHPStanRules\Rules\ForbiddenNullableParameterRule`

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ForbiddenNullableParameterRule
        tags: [phpstan.rules.rule]
        arguments:
            forbidddenTypes:
                - PhpParser\Node
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

## ForbiddenParentClassRule

Class "%s" inherits from forbidden parent class "%s". Use "%s" instead

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\ForbiddenParentClassRule`

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ForbiddenParentClassRule
        tags: [phpstan.rules.rule]
        arguments:
            forbiddenParentClasses:
                - ParentClass
```

↓

```php
class SomeClass extends ParentClass
{
}
```

:x:

<br>

```php
class SomeClass
{
    public function __construct(DecoupledClass $decoupledClass)
    {
        $this->decoupledClass = $decoupledClass;
    }
}
```

:+1:

<br>

## ForbiddenPrivateMethodByTypeRule

Private method in is not allowed here - it should only delegate to others. Decouple the private method to a new service class

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\ForbiddenPrivateMethodByTypeRule`

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

- class: `Symplify\PHPStanRules\Rules\ForbiddenProtectedPropertyRule`

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

- class: `Symplify\PHPStanRules\Rules\ForbiddenReturnValueOfIncludeOnceRule`

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

- class: `Symplify\PHPStanRules\Rules\ForbiddenSpreadOperatorRule`

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

- class: `Symplify\PHPStanRules\Rules\ForbiddenTestsNamespaceOutsideTestsDirectoryRule`

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

## FunctionLikeCognitiveComplexityRule

Cognitive complexity of function/method must be under specific limit

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\CognitiveComplexity\Rules\FunctionLikeCognitiveComplexityRule`

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

- class: `Symplify\PHPStanRules\Rules\IfImplementsInterfaceThenNewTypeRule`

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

- class: `Symplify\PHPStanRules\Rules\IfNewTypeThenImplementInterfaceRule`

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

- class: `Symplify\PHPStanRules\Rules\InvokableControllerByRouteNamingRule`

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

`Constant` type should be "%s", but is "%s"

- class: `Symplify\PHPStanRules\Rules\MatchingTypeConstantRule`

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

- class: `Symplify\PHPStanRules\Rules\NoAbstractMethodRule`

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

- class: `Symplify\PHPStanRules\Rules\NoArrayAccessOnObjectRule`

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

- class: `Symplify\PHPStanRules\Rules\NoArrayStringObjectReturnRule`

```php
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

<br>

```php
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

## NoChainMethodCallRule

Do not use chained method calls. Put `each` on separated lines.

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\ObjectCalisthenics\Rules\NoChainMethodCallRule`

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

- class: `Symplify\PHPStanRules\Rules\NoClassWithStaticMethodWithoutStaticNameRule`

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

## NoConstructorInTestRule

Do not use constructor in tests. Move to `setUp()` method

- class: `Symplify\PHPStanRules\Rules\NoConstructorInTestRule`

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

- class: `Symplify\PHPStanRules\Rules\NoContainerInjectionInConstructorRule`

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

- class: `Symplify\PHPStanRules\Rules\NoDefaultExceptionRule`

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

- class: `Symplify\PHPStanRules\Rules\NoDefaultParameterValueRule`

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

## NoDuplicatedShortClassNameRule

Class with base "%s" name is already used in "%s". Use unique name to make classes easy to recognize

- class: `Symplify\PHPStanRules\Rules\NoDuplicatedShortClassNameRule`

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

- class: `Symplify\PHPStanRules\Rules\NoDynamicNameRule`

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

<br>

```php
if (...) {
    return 1;
}

return 2;
```

:+1:

<br>

## NoFactoryInConstructorRule

Do not use factory/method call in constructor. Put factory in config and get service with dependency injection

- class: `Symplify\PHPStanRules\Rules\NoFactoryInConstructorRule`

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

- class: `Symplify\PHPStanRules\Rules\NoFuncCallInMethodCallRule`

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

- class: `Symplify\PHPStanRules\Rules\NoGetRepositoryOutsideConstructorRule`

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

## NoInlineStringRegexRule

Use local named constant instead of inline string for regex to explain meaning by constant name

- class: `Symplify\PHPStanRules\Rules\NoInlineStringRegexRule`

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

- class: `Symplify\PHPStanRules\Rules\NoIssetOnObjectRule`

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

## NoMethodTagInClassDocblockRule

Do not use `@method` tag in class docblock

- class: `Symplify\PHPStanRules\Rules\NoMethodTagInClassDocblockRule`

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

- class: `Symplify\PHPStanRules\Rules\NoMissingDirPathRule`

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

## NoMultiArrayAssignRule

Use value object over multi array assign

- class: `Symplify\PHPStanRules\Rules\NoMultiArrayAssignRule`

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

## NoNewOutsideFactoryRule

Use decoupled factory service to create "%s" object

- class: `Symplify\PHPStanRules\Rules\NoNewOutsideFactoryRule`

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

Use required typed property over of nullable property

- class: `Symplify\PHPStanRules\Rules\NoNullableArrayPropertyRule`

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

## NoParentMethodCallOnEmptyStatementInParentMethodRule

Do not call parent method if parent method is empty

- class: `Symplify\PHPStanRules\Rules\NoParentMethodCallOnEmptyStatementInParentMethodRule`

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

- class: `Symplify\PHPStanRules\Rules\NoParentMethodCallOnNoOverrideProcessRule`

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

- class: `Symplify\PHPStanRules\Rules\NoPostIncPostDecRule`

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

- class: `Symplify\PHPStanRules\Rules\NoProtectedElementInFinalClassRule`

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

- class: `Symplify\PHPStanRules\Rules\NoReferenceRule`

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

- class: `Symplify\PHPStanRules\Rules\NoReturnArrayVariableListRule`

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

## NoScalarAndArrayConstructorParameterRule

Do not use scalar or array as constructor parameter. Use "Symplify\PackageBuilder\Parameter\ParameterProvider" service instead

- class: `Symplify\PHPStanRules\Rules\NoScalarAndArrayConstructorParameterRule`

```php
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

<br>

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

## NoSetterClassMethodRule

Setter `"%s()"` is not allowed. Use constructor injection or behavior name instead, e.g. `"changeName()"`

- class: `Symplify\PHPStanRules\ObjectCalisthenics\Rules\NoSetterClassMethodRule`

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

- class: `Symplify\PHPStanRules\Rules\NoSetterOnServiceRule`

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

- class: `Symplify\PHPStanRules\ObjectCalisthenics\Rules\NoShortNameRule`

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

- class: `Symplify\PHPStanRules\Rules\NoStaticPropertyRule`

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

- class: `Symplify\PHPStanRules\Rules\NoSuffixValueObjectClassRule`

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

## NoTraitExceptRequiredAutowireRule

Do not use trait

- class: `Symplify\PHPStanRules\Rules\NoTraitExceptRequiredAutowireRule`

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

## OnlyOneClassMethodRule

Allow only one of methods to be implemented on type

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\OnlyOneClassMethodRule`

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

## PreferredAttributeOverAnnotationRule

Use attribute instead of "%s" annotation

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\PreferredAttributeOverAnnotationRule`

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

- class: `Symplify\PHPStanRules\Rules\PreferredClassRule`

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

- class: `Symplify\PHPStanRules\Rules\PreferredMethodCallOverFuncCallRule`

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\PreferredMethodCallOverFuncCallRule
        tags: [phpstan.rules.rule]
        arguments:
            funcCallToPreferredMethodCalls:
                strlen:
                    - Nette\Utils\Strings
                    - lenght
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
        return $this->strings->lenght($value);
    }
}
```

:+1:

<br>

## PreferredRawDataInTestDataProviderRule

Code configured at `setUp()` cannot be used in data provider. Move it to `test()` method

- class: `Symplify\PHPStanRules\Rules\PreferredRawDataInTestDataProviderRule`

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

- class: `Symplify\PHPStanRules\Rules\PreferredStaticCallOverFuncCallRule`

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\PreferredStaticCallOverFuncCallRule
        tags: [phpstan.rules.rule]
        arguments:
            funcCallToPreferredStaticCalls:
                strlen:
                    - Nette\Utils\Strings
                    - lenght
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
        return Strings::lenght($value);
    }
}
```

:+1:

<br>

## PrefixAbstractClassRule

Abstract class name "%s" must be prefixed with "Abstract"

- class: `Symplify\PHPStanRules\Rules\PrefixAbstractClassRule`

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

Set param "%s" value is duplicated, use unique value instead

- class: `Symplify\PHPStanRules\Rules\PreventDoubleSetParameterRule`

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

Content of method `"%s()"` is duplicated with method in "%s" class. Use unique content or abstract service instead

- class: `Symplify\PHPStanRules\Rules\PreventDuplicateClassMethodRule`

```php
class A
{
    public function someMethod()
    {
        echo 'statement';
        (new SmartFinder())->run('.php');
    }
}

class B
{
    public function someMethod()
    {
        echo 'statement';
        (new SmartFinder())->run('.php');
    }
}
```

:x:

<br>

```php
class A
{
    public function someMethod()
    {
        echo 'statement';
        (new SmartFinder())->run('.php');
    }
}

class B
{
    public function someMethod()
    {
        echo 'statement';
        (new SmartFinder())->run('.js');
    }
}
```

:+1:

<br>

## PreventParentMethodVisibilityOverrideRule

Change `"%s()"` method visibility to "%s" to respect parent method visibility.

- class: `Symplify\PHPStanRules\Rules\PreventParentMethodVisibilityOverrideRule`

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

- class: `Symplify\PHPStanRules\Rules\RegexSuffixInRegexConstantRule`

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

## RequireClassTypeInClassMethodByTypeRule

Required specific class-string types in defined methods

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\RequireClassTypeInClassMethodByTypeRule`

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

## RequireConstantInMethodCallPositionRule

Parameter argument on position %d must use %s constant

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\RequireConstantInMethodCallPositionRule`

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

- class: `Symplify\PHPStanRules\Rules\RequireDataProviderTestMethodRule`

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

Use invokable controller with `__invoke()` method instead

- class: `Symplify\PHPStanRules\Rules\RequireInvokableControllerRule`

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

- class: `Symplify\PHPStanRules\Rules\RequireMethodCallArgumentConstantRule`

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

- class: `Symplify\PHPStanRules\Rules\RequireNewArgumentConstantRule`

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

- class: `Symplify\PHPStanRules\Rules\RequireQuoteStringValueSprintfRule`

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

`File` "%s" should have prefix "Skip" prefix

- class: `Symplify\PHPStanRules\Rules\RequireSkipPrefixForRuleSkippedFixtureRule`

```php
use PHPStan\Testing\RuleTestCase;

final class SomeRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
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

## RequireStringArgumentInMethodCallRule

Use quoted string in method call `"%s()"` argument on position %d instead of "::class. It prevent scoping of the class in building prefixed package.

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\RequireStringArgumentInMethodCallRule`

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\RequireStringArgumentInMethodCallRule
        tags: [phpstan.rules.rule]
        arguments:
            stringArgByMethodByType:
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

"%s" regex need to use string named capture group instead of numeric

- class: `Symplify\PHPStanRules\Rules\RequireStringRegexMatchKeyRule`

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
    private const REGEX = '#(?<c>a content)#';

    public function run()
    {
        $matches = Strings::match('a content', self::REGEX);
        if ($matches) {
            echo $matches['c'];
        }
    }
}
```

:+1:

<br>

## RequireThisCallOnLocalMethodRule

Use "$this-><method>()" instead of "self::<method>()" to call local method

- class: `Symplify\PHPStanRules\Rules\RequireThisCallOnLocalMethodRule`

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

- class: `Symplify\PHPStanRules\Rules\RequireThisOnParentMethodCallRule`

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

## SeeAnnotationToTestRule

Class "%s" is missing `@see` annotation with test case class reference

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\SeeAnnotationToTestRule`

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

## SingleIndentationInMethodRule

Do not indent more than %dx in class methods

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\ObjectCalisthenics\Rules\SingleIndentationInMethodRule`

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

## SuffixInterfaceRule

Interface name "%s" must be suffixed with "Interface"

- class: `Symplify\PHPStanRules\Rules\SuffixInterfaceRule`

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

- class: `Symplify\PHPStanRules\Rules\SuffixTraitRule`

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

new <class> is limited to %d "new <class>(new <class>))" nesting to `each` other. You have %d nesting.

:wrench: **configure it!**

- class: `Symplify\PHPStanRules\Rules\TooDeepNewClassNestingRule`

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

- class: `Symplify\PHPStanRules\ObjectCalisthenics\Rules\TooLongClassLikeRule`

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

- class: `Symplify\PHPStanRules\ObjectCalisthenics\Rules\TooLongFunctionLikeRule`

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

- class: `Symplify\PHPStanRules\Rules\TooLongVariableRule`

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

- class: `Symplify\PHPStanRules\ObjectCalisthenics\Rules\TooManyMethodsRule`

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

- class: `Symplify\PHPStanRules\ObjectCalisthenics\Rules\TooManyPropertiesRule`

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

`Constant` "%s" must be uppercase

- class: `Symplify\PHPStanRules\Rules\UppercaseConstantRule`

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
