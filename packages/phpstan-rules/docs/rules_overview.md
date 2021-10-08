# 162 Rules Overview

## [AnnotateRegexClassConstWithRegexLinkRule](../src/Rules/AnnotateRegexClassConstWithRegexLinkRule.php)

Add regex101.com link to that shows the regex in practise, so it will be easier to maintain in case of bug/extension in the future



- class:

```

Symplify\PHPStanRules\Rules\AnnotateRegexClassConstWithRegexLinkRule

```



- example-diff:

```php
<?php
class SomeClass
{
    private const COMPLICATED_REGEX = '#some_complicated_stu|ff#';
}
```

:x:

<br>

```php
<?php
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

## [BoolishClassMethodPrefixRule](../src/Rules/BoolishClassMethodPrefixRule.php)

Method `"%s()"` returns bool type, so the name should start with is/has/was...



- class:

```

Symplify\PHPStanRules\Rules\BoolishClassMethodPrefixRule

```



- example-diff:

```php
<?php
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
<?php
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

## [CheckAttributteArgumentClassExistsRule](../src/Rules/CheckAttributteArgumentClassExistsRule.php)

Class was not found



- class:

```

Symplify\PHPStanRules\Rules\CheckAttributteArgumentClassExistsRule

```



- example-diff:

```php
<?php
#[SomeAttribute(firstName: 'MissingClass::class')]
class SomeClass
{
}
```

:x:

<br>

```php
<?php
#[SomeAttribute(firstName: ExistingClass::class)]
class SomeClass
{
}
```

:+1:

<br>

## [CheckClassNamespaceFollowPsr4Rule](../src/Rules/CheckClassNamespaceFollowPsr4Rule.php)

Class like namespace "%s" does not follow PSR-4 configuration in `composer.json`



- class:

```

Symplify\PHPStanRules\Rules\CheckClassNamespaceFollowPsr4Rule

```



- example-diff:

```php
<?php
// defined "Foo\Bar" namespace in composer.json > autoload > psr-4
namespace Foo;

class Baz
{
}
```

:x:

<br>

```php
<?php
// defined "Foo\Bar" namespace in composer.json > autoload > psr-4
namespace Foo\Bar;

class Baz
{
}
```

:+1:

<br>

## [CheckConstantExpressionDefinedInConstructOrSetupRule](../src/Rules/CheckConstantExpressionDefinedInConstructOrSetupRule.php)

Move constant expression to `__construct()`, `setUp()` method or constant



- class:

```

Symplify\PHPStanRules\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule

```



- example-diff:

```php
<?php
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
<?php
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

## [CheckNotTestsNamespaceOutsideTestsDirectoryRule](../src/Rules/CheckNotTestsNamespaceOutsideTestsDirectoryRule.php)

"*Test.php" file cannot be located outside "Tests" namespace



- class:

```

Symplify\PHPStanRules\Rules\CheckNotTestsNamespaceOutsideTestsDirectoryRule

```



- example-diff:

```php
<?php
// file: "SomeTest.php
namespace App;

class SomeTest
{
}
```

:x:

<br>

```php
<?php
// file: "SomeTest.php
namespace App\Tests;

class SomeTest
{
}
```

:+1:

<br>

## [CheckOptionArgumentCommandRule](../packages/symfony/src/Rules/CheckOptionArgumentCommandRule.php)

Argument and options "%s" got confused



- class:

```

Symplify\PHPStanRules\Symfony\Rules\CheckOptionArgumentCommandRule

```



- example-diff:

```php
<?php
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
<?php
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

## [CheckParentChildMethodParameterTypeCompatibleRule](../src/Rules/CheckParentChildMethodParameterTypeCompatibleRule.php)

Method parameters must be compatible with its parent



- class:

```

Symplify\PHPStanRules\Rules\CheckParentChildMethodParameterTypeCompatibleRule

```



- example-diff:

```php
<?php
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
<?php
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

## [CheckReferencedClassInAnnotationRule](../src/Rules/Missing/CheckReferencedClassInAnnotationRule.php)

Class "%s" used in annotation is missing



- class:

```

Symplify\PHPStanRules\Rules\Missing\CheckReferencedClassInAnnotationRule

```



- example-diff:

```php
<?php
/**
 * @SomeAnnotation(value=MissingClass::class)
 */
class SomeClass
{
}
```

:x:

<br>

```php
<?php
/**
 * @SomeAnnotation(value=ExistingClass::class)
 */
class SomeClass
{
}
```

:+1:

<br>

## [CheckRequiredInterfaceInContractNamespaceRule](../src/Rules/CheckRequiredInterfaceInContractNamespaceRule.php)

Interface must be located in "Contract" namespace



- class:

```

Symplify\PHPStanRules\Rules\CheckRequiredInterfaceInContractNamespaceRule

```



- example-diff:

```php
<?php
namespace App\Repository;

interface ProductRepositoryInterface
{
}
```

:x:

<br>

```php
<?php
namespace App\Contract\Repository;

interface ProductRepositoryInterface
{
}
```

:+1:

<br>

## [CheckRequiredMethodNamingRule](../src/Rules/CheckRequiredMethodNamingRule.php)

Autowired/inject method name must respect "autowire/inject" + class name



- class:

```

Symplify\PHPStanRules\Rules\CheckRequiredMethodNamingRule

```



- example-diff:

```php
<?php
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
<?php
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

## [CheckSprinfMatchingTypesRule](../src/Rules/Missing/CheckSprinfMatchingTypesRule.php)

`sprintf()` call mask types does not match provided arguments types



- class:

```

Symplify\PHPStanRules\Rules\Missing\CheckSprinfMatchingTypesRule

```



- example-diff:

```php
<?php
echo sprintf('My name is %s and I have %d children', 10, 'Tomas');
```

:x:

<br>

```php
<?php
echo sprintf('My name is %s and I have %d children', 'Tomas', 10);
```

:+1:

<br>

## [CheckSymfonyConfigDefaultsRule](../packages/symfony/src/Rules/CheckSymfonyConfigDefaultsRule.php)

`autowire()`, `autoconfigure()`, and `public()` are required in config service



- class:

```

Symplify\PHPStanRules\Symfony\Rules\CheckSymfonyConfigDefaultsRule

```



- example-diff:

```php
<?php
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
<?php
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

## [CheckTypehintCallerTypeRule](../src/Rules/CheckTypehintCallerTypeRule.php)

Parameter %d should use "%s" type as the only type passed to this method



- class:

```

Symplify\PHPStanRules\Rules\CheckTypehintCallerTypeRule

```



- example-diff:

```php
<?php
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
<?php
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

## [ClassLikeCognitiveComplexityRule](../packages/cognitive-complexity/src/Rules/ClassLikeCognitiveComplexityRule.php)

Cognitive complexity of class/trait must be under specific limit

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\CognitiveComplexity\Rules\ClassLikeCognitiveComplexityRule

```



- example-diff:

```yaml
services:
    -
        class: Symplify\PHPStanRules\CognitiveComplexity\Rules\ClassLikeCognitiveComplexityRule
        tags: [phpstan.rules.rule]
        arguments:
            maxClassCognitiveComplexity: 10
            scoreCompositionOverInheritance: true
```

↓

```php
<?php
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
<?php
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
<?php
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
<?php
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

## [ClassNameRespectsParentSuffixRule](../src/Rules/ClassNameRespectsParentSuffixRule.php)

Class should have suffix "%s" to respect parent type

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\ClassNameRespectsParentSuffixRule

```



- example-diff:

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
<?php
class Some extends Command
{
}
```

:x:

<br>

```php
<?php
class SomeCommand extends Command
{
}
```

:+1:

<br>

## [ConstantMapRuleRule](../src/Rules/ConstantMapRuleRule.php)

Static constant map should be extracted from this method



- class:

```

Symplify\PHPStanRules\Rules\ConstantMapRuleRule

```



- example-diff:

```php
<?php
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
<?php
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

## [DibiMaskMatchesVariableTypeRule](../packages/nette/src/Rules/DibiMaskMatchesVariableTypeRule.php)

Modifier "%s" is not matching passed variable type "%s". The "%s" type is expected - see https://dibiphp.com/en/documentation#toc-modifiers-for-arrays



- class:

```

Symplify\PHPStanRules\Nette\Rules\DibiMaskMatchesVariableTypeRule

```



- example-diff:

```php
<?php
$database->query('INSERT INTO table %v', 'string');
```

:x:

<br>

```php
<?php
$database->query('INSERT INTO table %v', [
    'name' => 'Matthias',
]);
```

:+1:

<br>

## [DifferentMethodNameToParameterRule](../src/Rules/DifferentMethodNameToParameterRule.php)

Method name should be different to its parameter name, in a verb form



- class:

```

Symplify\PHPStanRules\Rules\DifferentMethodNameToParameterRule

```



- example-diff:

```php
<?php
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
<?php
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

## [DifferentMethodNameToReturnTypeRule](../src/Rules/Naming/DifferentMethodNameToReturnTypeRule.php)

Method name should be different to its return type, in a verb form



- class:

```

Symplify\PHPStanRules\Rules\Naming\DifferentMethodNameToReturnTypeRule

```



- example-diff:

```php
<?php
class SomeClass
{
    public function apple(): Apple
    {
        // ...
    }
}
```

:x:

<br>

```php
<?php
class SomeClass
{
    public function getApple(): Apple
    {
        // ...
    }
}
```

:+1:

<br>

## [EmbeddedEnumClassConstSpotterRule](../src/Rules/Enum/EmbeddedEnumClassConstSpotterRule.php)

Constants "%s" should be extract to standalone enum class

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\Enum\EmbeddedEnumClassConstSpotterRule

```



- example-diff:

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
<?php
class SomeProduct extends AbstractObject
{
    public const STATUS_ENABLED = 1;

    public const STATUS_DISABLED = 0;
}
```

:x:

<br>

```php
<?php
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

## [EnumSpotterRule](../src/Rules/Domain/EnumSpotterRule.php)

The string value "%s" is repeated %d times. Refactor to enum to avoid typos and make clear allowed values



- class:

```

Symplify\PHPStanRules\Rules\Domain\EnumSpotterRule

```



- example-diff:

```php
<?php
$this->addFlash('info', 'Some message');
$this->addFlash('info', 'Another message');
```

:x:

<br>

```php
<?php
$this->addFlash(FlashType::INFO, 'Some message');
$this->addFlash(FlashType::INFO, 'Another message');
```

:+1:

<br>

## [ExclusiveDependencyRule](../src/Rules/ExclusiveDependencyRule.php)

Dependency of specific type can be used only in specific class types

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\ExclusiveDependencyRule

```



- example-diff:

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
<?php
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
<?php
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

## [ExclusiveNamespaceRule](../src/Rules/ExclusiveNamespaceRule.php)

Exclusive namespace can only contain classes of specific type, nothing else

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\ExclusiveNamespaceRule

```



- example-diff:

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
<?php
namespace App\Presenter;

class SomeRepository
{
}
```

:x:

<br>

```php
<?php
namespace App\Presenter;

class SomePresenter
{
}
```

:+1:

<br>

## [ExplicitMethodCallOverMagicGetSetRule](../src/Rules/Explicit/ExplicitMethodCallOverMagicGetSetRule.php)

Instead of magic property "%s" access use direct explicit `"%s->%s()"` method call



- class:

```

Symplify\PHPStanRules\Rules\Explicit\ExplicitMethodCallOverMagicGetSetRule

```



- example-diff:

```php
<?php
class MagicCallsObject
{
    // adds magic __get() and __set() methods
    use \Nette\SmartObject;

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
<?php
class MagicCallsObject
{
    // adds magic __get() and __set() methods
    use \Nette\SmartObject;

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

## [ForbiddenAnonymousClassRule](../src/Rules/ForbiddenAnonymousClassRule.php)

Anonymous class is not allowed.



- class:

```

Symplify\PHPStanRules\Rules\ForbiddenAnonymousClassRule

```



- example-diff:

```php
<?php
new class() {
};
```

:x:

<br>

```php
<?php
class SomeClass
{
}

new SomeClass();
```

:+1:

<br>

## [ForbiddenArrayDestructRule](../src/Rules/ForbiddenArrayDestructRule.php)

Array destruct is not allowed. Use value object to pass data instead



- class:

```

Symplify\PHPStanRules\Rules\ForbiddenArrayDestructRule

```



- example-diff:

```php
<?php
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
<?php
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

## [ForbiddenArrayMethodCallRule](../src/Rules/Complexity/ForbiddenArrayMethodCallRule.php)

Array method calls [$this, "method"] are not allowed. Use explicit method instead to help PhpStorm, PHPStan and Rector understand your code



- class:

```

Symplify\PHPStanRules\Rules\Complexity\ForbiddenArrayMethodCallRule

```



- example-diff:

```php
<?php
usort($items, [$this, 'method']);
```

:x:

<br>

```php
<?php
usort($items, function (array $apples) {
    return $this->method($apples);
};
```

:+1:

<br>

## [ForbiddenArrayWithStringKeysRule](../src/Rules/ForbiddenArrayWithStringKeysRule.php)

Array with keys is not allowed. Use value object to pass data instead



- class:

```

Symplify\PHPStanRules\Rules\ForbiddenArrayWithStringKeysRule

```



- example-diff:

```php
<?php
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
<?php
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

## [ForbiddenAttributteArgumentRule](../src/Rules/ForbiddenAttributteArgumentRule.php)

Attribute key "%s" cannot be used

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\ForbiddenAttributteArgumentRule

```



- example-diff:

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\ForbiddenAttributteArgumentRule
        tags: [phpstan.rules.rule]
        arguments:
            argumentsByAttributes:
                Doctrine\ORM\Mapping\Entity:
                    - repositoryClass
```

↓

```php
<?php
use Doctrine\ORM\Mapping\Entity;

#[Entity(repositoryClass: SomeRepository::class)]
class SomeClass
{
}
```

:x:

<br>

```php
<?php
use Doctrine\ORM\Mapping\Entity;

#[Entity]
class SomeClass
{
}
```

:+1:

<br>

## [ForbiddenBinaryMethodCallRule](../src/Rules/Domain/ForbiddenBinaryMethodCallRule.php)

This call cannot be used in binary compare. Use direct method instead

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\Domain\ForbiddenBinaryMethodCallRule

```



- example-diff:

```yaml
services:
    -
        class: Symplify\PHPStanRules\Rules\Domain\ForbiddenBinaryMethodCallRule
        tags: [phpstan.rules.rule]
        arguments:
            SomeType:
                - getId
```

↓

```php
<?php
$someType = new SomeType();
if ($someType->getId() !== null) {
    return $someType->getId();
}
```

:x:

<br>

```php
<?php
$someType = new SomeType();
if ($someType->hasId()) {
    return $someType->getId();
}
```

:+1:

<br>

## [ForbiddenClassConstRule](../src/Rules/Enum/ForbiddenClassConstRule.php)

Constants in this class are not allowed, move them to custom Enum class instead

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\Enum\ForbiddenClassConstRule

```



- example-diff:

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
<?php
final class Product extends AbstractEntity
{
    public const TYPE_HIDDEN = 0;

    public const TYPE_VISIBLE = 1;
}
```

:x:

<br>

```php
<?php
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

## [ForbiddenComplexForeachIfExprRule](../src/Rules/Complexity/ForbiddenComplexForeachIfExprRule.php)

`foreach()`, `while()`, `for()` or `if()` cannot contain a complex expression. Extract it to a new variable on a line before



- class:

```

Symplify\PHPStanRules\Rules\Complexity\ForbiddenComplexForeachIfExprRule

```



- example-diff:

```php
<?php
foreach ($this->getData($arg) as $key => $item) {
    // ...
}
```

:x:

<br>

```php
<?php
$data = $this->getData($arg);
foreach ($arg as $key => $item) {
    // ...
}
```

:+1:

<br>

## [ForbiddenComplexFuncCallRule](../src/Rules/ForbiddenComplexFuncCallRule.php)

Do not use "%s" function with complex content, make it more readable with extracted method or single-line statement

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\ForbiddenComplexFuncCallRule

```



- example-diff:

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
<?php
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
<?php
$filteredElements = array_filter($elemnets, function ($item) {
    return $item instanceof KeepItSimple;
};
```

:+1:

<br>

## [ForbiddenDependencyByTypeRule](../src/Rules/ForbiddenDependencyByTypeRule.php)

Object instance of "%s" is forbidden to be passed to constructor

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\ForbiddenDependencyByTypeRule

```



- example-diff:

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
<?php
class SomeClass
{
    public function __construct(
        private EntityManager $entityManager
    ) {
        // ...
    }
}
```

:x:

<br>

```php
<?php
class SomeClass
{
    public function __construct(
        private ProductRepository $productRepository
    ) {
        // ...
    }
}
```

:+1:

<br>

## [ForbiddenForeachEmptyMissingArrayRule](../src/Rules/ForbiddenForeachEmptyMissingArrayRule.php)

Foreach over empty missing array is not allowed. Use isset check early instead.



- class:

```

Symplify\PHPStanRules\Rules\ForbiddenForeachEmptyMissingArrayRule

```



- example-diff:

```php
<?php
final class SomeClass
{
    public function run(): void
    {
        foreach ($data ?? [] as $value) {
            // ...
        }
    }
}
```

:x:

<br>

```php
<?php
final class SomeClass
{
    public function run(): void
    {
        if (! isset($data)) {
            return;
        }

        foreach ($data as $value) {
            // ...
        }
    }
}
```

:+1:

<br>

## [ForbiddenFuncCallRule](../src/Rules/ForbiddenFuncCallRule.php)

Function `"%s()"` cannot be used/left in the code

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\ForbiddenFuncCallRule

```



- example-diff:

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
<?php
class SomeClass
{
    return eval('...');
}
```

:x:

<br>

```php
<?php
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
<?php
class SomeClass
{
    dump('hello world');
    return true;
}
```

:x:

<br>

```php
<?php
class SomeClass
{
    return true;
}
```

:+1:

<br>

## [ForbiddenInlineClassMethodRule](../src/Rules/Complexity/ForbiddenInlineClassMethodRule.php)

Method `"%s()"` only calling another method call and has no added value. Use the inlined call instead



- class:

```

Symplify\PHPStanRules\Rules\Complexity\ForbiddenInlineClassMethodRule

```



- example-diff:

```php
<?php
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
<?php
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

## [ForbiddenMethodCallOnNewRule](../src/Rules/ForbiddenMethodCallOnNewRule.php)

Method call on new expression is not allowed.



- class:

```

Symplify\PHPStanRules\Rules\ForbiddenMethodCallOnNewRule

```



- example-diff:

```php
<?php
(new SomeClass())->run();
```

:x:

<br>

```php
<?php
$someClass = new SomeClass();
$someClass->run();
```

:+1:

<br>

## [ForbiddenMethodCallOnTypeRule](../src/Rules/ForbiddenMethodCallOnTypeRule.php)

Prevent using certain method calls on certains types

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\ForbiddenMethodCallOnTypeRule

```



- example-diff:

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
<?php
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
<?php
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

## [ForbiddenMultipleClassLikeInOneFileRule](../src/Rules/ForbiddenMultipleClassLikeInOneFileRule.php)

Multiple class/interface/trait is not allowed in single file



- class:

```

Symplify\PHPStanRules\Rules\ForbiddenMultipleClassLikeInOneFileRule

```



- example-diff:

```php
<?php
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
<?php
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

## [ForbiddenNestedCallInAssertMethodCallRule](../src/Rules/ForbiddenNestedCallInAssertMethodCallRule.php)

Decouple method call in assert to standalone line to make test core more readable



- class:

```

Symplify\PHPStanRules\Rules\ForbiddenNestedCallInAssertMethodCallRule

```



- example-diff:

```php
<?php
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
<?php
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

## [ForbiddenNestedForeachWithEmptyStatementRule](../src/Rules/ForbiddenNestedForeachWithEmptyStatementRule.php)

Nested foreach with empty statement is not allowed



- class:

```

Symplify\PHPStanRules\Rules\ForbiddenNestedForeachWithEmptyStatementRule

```



- example-diff:

```php
<?php
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
<?php
$collectedFileErrors = [];

foreach ($fileErrors as $fileError) {
    $collectedFileErrors[] = $fileError;
}
```

:+1:

<br>

## [ForbiddenNetteInjectOverrideRule](../packages/nette/src/Rules/ForbiddenNetteInjectOverrideRule.php)

Assign to already injected property is not allowed



- class:

```

Symplify\PHPStanRules\Nette\Rules\ForbiddenNetteInjectOverrideRule

```



- example-diff:

```php
<?php
abstract class AbstractParent
{
    /**
     * @inject
     * @var SomeType
     */
    protected $someType;
}

final class SomeChild extends AbstractParent
{
    public function __construct(AnotherType $anotherType)
    {
        $this->someType = $anotherType;
    }
}
```

:x:

<br>

```php
<?php
abstract class AbstractParent
{
    /**
     * @inject
     * @var SomeType
     */
    protected $someType;
}

final class SomeChild extends AbstractParent
{
}
```

:+1:

<br>

## [ForbiddenNodeRule](../src/Rules/ForbiddenNodeRule.php)

"%s" is forbidden to use

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\ForbiddenNodeRule

```



- example-diff:

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
<?php
return @strlen('...');
```

:x:

<br>

```php
<?php
return strlen('...');
```

:+1:

<br>

## [ForbiddenNullableParameterRule](../src/Rules/ForbiddenNullableParameterRule.php)

Parameter "%s" cannot be nullable

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\ForbiddenNullableParameterRule

```



- example-diff:

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
<?php
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
<?php
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

## [ForbiddenParamTypeRemovalRule](../src/Rules/ForbiddenParamTypeRemovalRule.php)

Removing parent param type is forbidden



- class:

```

Symplify\PHPStanRules\Rules\ForbiddenParamTypeRemovalRule

```



- example-diff:

```php
<?php
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
<?php
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

## [ForbiddenPrivateMethodByTypeRule](../src/Rules/ForbiddenPrivateMethodByTypeRule.php)

Private method in is not allowed here - it should only delegate to others. Decouple the private method to a new service class

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\ForbiddenPrivateMethodByTypeRule

```



- example-diff:

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
<?php
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
<?php
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

## [ForbiddenProtectedPropertyRule](../src/Rules/ForbiddenProtectedPropertyRule.php)

Property with protected modifier is not allowed. Use interface contract method instead



- class:

```

Symplify\PHPStanRules\Rules\ForbiddenProtectedPropertyRule

```



- example-diff:

```php
<?php
class SomeClass
{
    protected $repository;
}
```

:x:

<br>

```php
<?php
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

## [ForbiddenReturnValueOfIncludeOnceRule](../src/Rules/ForbiddenReturnValueOfIncludeOnceRule.php)

Cannot return include_once/require_once



- class:

```

Symplify\PHPStanRules\Rules\ForbiddenReturnValueOfIncludeOnceRule

```



- example-diff:

```php
<?php
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
<?php
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

## [ForbiddenSameNamedAssignRule](../src/Rules/Complexity/ForbiddenSameNamedAssignRule.php)

Variables "%s" are overridden. This can lead to unwanted bugs, please pick a different name to avoid it.



- class:

```

Symplify\PHPStanRules\Rules\Complexity\ForbiddenSameNamedAssignRule

```



- example-diff:

```php
<?php
$value = 1000;
$value = 2000;
```

:x:

<br>

```php
<?php
$value = 1000;
$anotherValue = 2000;
```

:+1:

<br>

## [ForbiddenSameNamedNewInstanceRule](../src/Rules/Complexity/ForbiddenSameNamedNewInstanceRule.php)

New objects with "%s" name are overridden. This can lead to unwanted bugs, please pick a different name to avoid it.



- class:

```

Symplify\PHPStanRules\Rules\Complexity\ForbiddenSameNamedNewInstanceRule

```



- example-diff:

```php
<?php
$product = new Product();
$product = new Product();

$this->productRepository->save($product);
```

:x:

<br>

```php
<?php
$firstProduct = new Product();
$secondProduct = new Product();

$this->productRepository->save($firstProduct);
```

:+1:

<br>

## [ForbiddenSpreadOperatorRule](../src/Rules/ForbiddenSpreadOperatorRule.php)

Spread operator is not allowed.



- class:

```

Symplify\PHPStanRules\Rules\ForbiddenSpreadOperatorRule

```



- example-diff:

```php
<?php
$args = [$firstValue, $secondValue];
$message = sprintf('%s', ...$args);
```

:x:

<br>

```php
<?php
$message = sprintf('%s', $firstValue, $secondValue);
```

:+1:

<br>

## [ForbiddenTestsNamespaceOutsideTestsDirectoryRule](../src/Rules/ForbiddenTestsNamespaceOutsideTestsDirectoryRule.php)

"Tests" namespace can be only in "/tests" directory



- class:

```

Symplify\PHPStanRules\Rules\ForbiddenTestsNamespaceOutsideTestsDirectoryRule

```



- example-diff:

```php
<?php
// file path: "src/SomeClass.php

namespace App\Tests;

class SomeClass
{
}
```

:x:

<br>

```php
<?php
// file path: "tests/SomeClass.php

namespace App\Tests;

class SomeClass
{
}
```

:+1:

<br>

## [ForbiddenThisArgumentRule](../src/Rules/ForbiddenThisArgumentRule.php)

`$this` as argument is not allowed. Refactor method to service composition



- class:

```

Symplify\PHPStanRules\Rules\ForbiddenThisArgumentRule

```



- example-diff:

```php
<?php
$this->someService->process($this, ...);
```

:x:

<br>

```php
<?php
$this->someService->process($value, ...);
```

:+1:

<br>

## [FunctionLikeCognitiveComplexityRule](../packages/cognitive-complexity/src/Rules/FunctionLikeCognitiveComplexityRule.php)

Cognitive complexity of function/method must be under specific limit

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\CognitiveComplexity\Rules\FunctionLikeCognitiveComplexityRule

```



- example-diff:

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
<?php
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
<?php
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

## [IfElseToMatchSpotterRule](../src/Rules/Spotter/IfElseToMatchSpotterRule.php)

If/else construction can be replace with more robust `match()`



- class:

```

Symplify\PHPStanRules\Rules\Spotter\IfElseToMatchSpotterRule

```



- example-diff:

```php
<?php
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
<?php
class SomeClass
{
    public function spot($value)
    {
        return match ($value) {
            100 => ['yes'],
            default => ['no'],
        };
    }
}
```

:+1:

<br>

## [IfImplementsInterfaceThenNewTypeRule](../src/Rules/IfImplementsInterfaceThenNewTypeRule.php)

Class that implements specific interface, must use related class in `new SomeClass`

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\IfImplementsInterfaceThenNewTypeRule

```



- example-diff:

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
<?php
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
<?php
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

## [IfNewTypeThenImplementInterfaceRule](../src/Rules/IfNewTypeThenImplementInterfaceRule.php)

Class must implement "%s" interface

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\IfNewTypeThenImplementInterfaceRule

```



- example-diff:

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
<?php
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
<?php
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

## [InvokableControllerByRouteNamingRule](../packages/symfony/src/Rules/InvokableControllerByRouteNamingRule.php)

Use controller class name based on route name instead



- class:

```

Symplify\PHPStanRules\Symfony\Rules\InvokableControllerByRouteNamingRule

```



- example-diff:

```php
<?php
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
<?php
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

## [LatteCompleteCheckRule](../packages/nette/src/Rules/LatteCompleteCheckRule.php)

Complete analysis of PHP code generated from Latte template



- class:

```

Symplify\PHPStanRules\Nette\Rules\LatteCompleteCheckRule

```



- example-diff:

```php
<?php
use Nette\Application\UI\Control;

class SomeClass extends Control
{
    public function render()
    {
        $this->template->render(__DIR__ . '/some_control.latte', [
            'some_type' => new SomeType
        ]);
    }
}

// some_control.latte
{$some_type->missingMethod()}
```

:x:

<br>

```php
<?php
use Nette\Application\UI\Control;

class SomeClass extends Control
{
    public function render()
    {
        $this->template->render(__DIR__ . '/some_control.latte', [
            'some_type' => new SomeType
        ]);
    }
}


// some_control.latte
{$some_type->existingMethod()}
```

:+1:

<br>

## [MatchingTypeConstantRule](../src/Rules/MatchingTypeConstantRule.php)

Constant type should be "%s", but is "%s"



- class:

```

Symplify\PHPStanRules\Rules\MatchingTypeConstantRule

```



- example-diff:

```php
<?php
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
<?php
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

## [NoAbstractMethodRule](../src/Rules/NoAbstractMethodRule.php)

Use explicit interface contract or a service over unclear abstract methods



- class:

```

Symplify\PHPStanRules\Rules\NoAbstractMethodRule

```



- example-diff:

```php
<?php
abstract class SomeClass
{
    abstract public function run();
}
```

:x:

<br>

```php
<?php
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

## [NoAbstractRule](../src/Rules/Complexity/NoAbstractRule.php)

Instead of abstract class, use specific service with composition



- class:

```

Symplify\PHPStanRules\Rules\Complexity\NoAbstractRule

```



- example-diff:

```php
<?php
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
<?php
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

## [NoArrayAccessOnObjectRule](../src/Rules/NoArrayAccessOnObjectRule.php)

Use explicit methods over array access on object



- class:

```

Symplify\PHPStanRules\Rules\NoArrayAccessOnObjectRule

```



- example-diff:

```php
<?php
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
<?php
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

## [NoArrayStringObjectReturnRule](../src/Rules/NoArrayStringObjectReturnRule.php)

Use another value object over array with string-keys and objects, array<string, ValueObject>



- class:

```

Symplify\PHPStanRules\Rules\NoArrayStringObjectReturnRule

```



- example-diff:

```php
<?php
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
<?php
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

## [NoBinaryOpCallCompareRule](../src/Rules/NoBinaryOpCallCompareRule.php)

No magic closure function call is allowed, use explicit class with method instead



- class:

```

Symplify\PHPStanRules\Rules\NoBinaryOpCallCompareRule

```



- example-diff:

```php
<?php
return array_filter($items, function ($item) {
}) !== [];
```

:x:

<br>

```php
<?php
$values = array_filter($items, function ($item) {
});
return $values !== [];
```

:+1:

<br>

## [NoChainMethodCallRule](../packages/object-calisthenics/src/Rules/NoChainMethodCallRule.php)

Do not use chained method calls. Put each on separated lines.

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\ObjectCalisthenics\Rules\NoChainMethodCallRule

```



- example-diff:

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
<?php
$this->runThis()
    ->runThat();

$fluentClass = new AllowedFluent();
$fluentClass->one()
    ->two();
```

:x:

<br>

```php
<?php
$this->runThis();
$this->runThat();

$fluentClass = new AllowedFluent();
$fluentClass->one()
    ->two();
```

:+1:

<br>

## [NoClassWithStaticMethodWithoutStaticNameRule](../src/Rules/NoClassWithStaticMethodWithoutStaticNameRule.php)

Class has a static method must so must contains "Static" in its name



- class:

```

Symplify\PHPStanRules\Rules\NoClassWithStaticMethodWithoutStaticNameRule

```



- example-diff:

```php
<?php
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
<?php
class SomeStaticClass
{
    public static function getSome()
    {
    }
}
```

:+1:

<br>

## [NoConstantInterfaceRule](../src/Rules/Enum/NoConstantInterfaceRule.php)

Reserve interface for contract only. Move constant holder to a class soon-to-be Enum



- class:

```

Symplify\PHPStanRules\Rules\Enum\NoConstantInterfaceRule

```



- example-diff:

```php
<?php
interface SomeContract
{
    public const YES = 'yes';

    public const NO = 'ne';
}
```

:x:

<br>

```php
<?php
class SomeValues
{
    public const YES = 'yes';

    public const NO = 'ne';
}
```

:+1:

<br>

## [NoConstructorInTestRule](../src/Rules/NoConstructorInTestRule.php)

Do not use constructor in tests. Move to `setUp()` method



- class:

```

Symplify\PHPStanRules\Rules\NoConstructorInTestRule

```



- example-diff:

```php
<?php
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
<?php
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

## [NoContainerInjectionInConstructorRule](../src/Rules/NoContainerInjectionInConstructorRule.php)

Instead of container injection, use specific service



- class:

```

Symplify\PHPStanRules\Rules\NoContainerInjectionInConstructorRule

```



- example-diff:

```php
<?php
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
<?php
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

## [NoDefaultExceptionRule](../src/Rules/NoDefaultExceptionRule.php)

Use custom exceptions instead of native "%s"



- class:

```

Symplify\PHPStanRules\Rules\NoDefaultExceptionRule

```



- example-diff:

```php
<?php
throw new RuntimeException('...');
```

:x:

<br>

```php
<?php
use App\Exception\FileNotFoundException;

throw new FileNotFoundException('...');
```

:+1:

<br>

## [NoDefaultParameterValueRule](../src/Rules/NoDefaultParameterValueRule.php)

Parameter "%s" cannot have default value



- class:

```

Symplify\PHPStanRules\Rules\NoDefaultParameterValueRule

```



- example-diff:

```php
<?php
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
<?php
class SomeClass
{
    public function run($value): void
    {
    }
}
```

:+1:

<br>

## [NoDependencyJugglingRule](../src/Rules/NoDependencyJugglingRule.php)

Use dependency injection instead of dependency juggling



- class:

```

Symplify\PHPStanRules\Rules\NoDependencyJugglingRule

```



- example-diff:

```php
<?php
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
<?php
public function run($someObject)
{
    return $someObject->someMethod();
}
```

:+1:

<br>

## [NoDuplicatedArgumentRule](../src/Rules/Complexity/NoDuplicatedArgumentRule.php)

This call has duplicate argument



- class:

```

Symplify\PHPStanRules\Rules\Complexity\NoDuplicatedArgumentRule

```



- example-diff:

```php
<?php
function run($one, $one);
```

:x:

<br>

```php
<?php
function run($one, $two);
```

:+1:

<br>

## [NoDuplicatedShortClassNameRule](../src/Rules/NoDuplicatedShortClassNameRule.php)

Class with base "%s" name is already used in "%s". Use unique name to make classes easy to recognize

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\NoDuplicatedShortClassNameRule

```



- example-diff:

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
<?php
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
<?php
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

## [NoDynamicNameRule](../src/Rules/NoDynamicNameRule.php)

Use explicit names over dynamic ones



- class:

```

Symplify\PHPStanRules\Rules\NoDynamicNameRule

```



- example-diff:

```php
<?php
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
<?php
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

## [NoDynamicPropertyOnStaticCallRule](../src/Rules/NoDynamicPropertyOnStaticCallRule.php)

Use non-dynamic property on static calls or class const fetches



- class:

```

Symplify\PHPStanRules\Rules\NoDynamicPropertyOnStaticCallRule

```



- example-diff:

```php
<?php
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
<?php
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

## [NoElseAndElseIfRule](../packages/object-calisthenics/src/Rules/NoElseAndElseIfRule.php)

Do not use "else/elseif". Refactor to early return



- class:

```

Symplify\PHPStanRules\ObjectCalisthenics\Rules\NoElseAndElseIfRule

```



- example-diff:

```php
<?php
if (...) {
    return 1;
} else {
    return 2;
}
```

:x:

<br>

```php
<?php
if (...) {
    return 1;
}

return 2;
```

:+1:

<br>

## [NoEmptyClassRule](../src/Rules/NoEmptyClassRule.php)

There should be no empty class



- class:

```

Symplify\PHPStanRules\Rules\NoEmptyClassRule

```



- example-diff:

```php
<?php
class SomeClass
{
}
```

:x:

<br>

```php
<?php
class SomeClass
{
    public function getSome()
    {
    }
}
```

:+1:

<br>

## [NoFactoryInConstructorRule](../src/Rules/NoFactoryInConstructorRule.php)

Do not use factory/method call in constructor. Put factory in config and get service with dependency injection



- class:

```

Symplify\PHPStanRules\Rules\NoFactoryInConstructorRule

```



- example-diff:

```php
<?php
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
<?php
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

## [NoFuncCallInMethodCallRule](../src/Rules/NoFuncCallInMethodCallRule.php)

Separate function `"%s()"` in method call to standalone row to improve readability



- class:

```

Symplify\PHPStanRules\Rules\NoFuncCallInMethodCallRule

```



- example-diff:

```php
<?php
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
<?php
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

## [NoGetRepositoryOutsideConstructorRule](../src/Rules/NoGetRepositoryOutsideConstructorRule.php)

Do not use `"$entityManager->getRepository()"` outside of the constructor of repository service or `setUp()` method in test case



- class:

```

Symplify\PHPStanRules\Rules\NoGetRepositoryOutsideConstructorRule

```



- example-diff:

```php
<?php
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
<?php
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

## [NoInjectOnFinalRule](../packages/nette/src/Rules/NoInjectOnFinalRule.php)

Use constructor on final classes, instead of property injection



- class:

```

Symplify\PHPStanRules\Nette\Rules\NoInjectOnFinalRule

```



- example-diff:

```php
<?php
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
<?php
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

## [NoInlineStringRegexRule](../src/Rules/NoInlineStringRegexRule.php)

Use local named constant instead of inline string for regex to explain meaning by constant name



- class:

```

Symplify\PHPStanRules\Rules\NoInlineStringRegexRule

```



- example-diff:

```php
<?php
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
<?php
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

## [NoIssetOnObjectRule](../src/Rules/NoIssetOnObjectRule.php)

Use default null value and nullable compare instead of isset on object



- class:

```

Symplify\PHPStanRules\Rules\NoIssetOnObjectRule

```



- example-diff:

```php
<?php
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
<?php
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

## [NoMagicClosureRule](../src/Rules/NoMagicClosureRule.php)

No magic closure function call is allowed, use explicit class with method instead



- class:

```

Symplify\PHPStanRules\Rules\NoMagicClosureRule

```



- example-diff:

```php
<?php
(static function () {
    // ...
})
```

:x:

<br>

```php
<?php
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

## [NoMaskWithoutSprintfRule](../src/Rules/NoMaskWithoutSprintfRule.php)

Missing `sprintf()` function for a mask



- class:

```

Symplify\PHPStanRules\Rules\NoMaskWithoutSprintfRule

```



- example-diff:

```php
<?php
return 'Hey %s';
```

:x:

<br>

```php
<?php
return sprintf('Hey %s', 'Matthias');
```

:+1:

<br>

## [NoMethodTagInClassDocblockRule](../src/Rules/NoMethodTagInClassDocblockRule.php)

Do not use `@method` tag in class docblock



- class:

```

Symplify\PHPStanRules\Rules\NoMethodTagInClassDocblockRule

```



- example-diff:

```php
<?php
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
<?php
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

## [NoMirrorAssertRule](../src/Rules/Complexity/NoMirrorAssertRule.php)

The assert is tautology that compares to itself. Fix it to different values



- class:

```

Symplify\PHPStanRules\Rules\Complexity\NoMirrorAssertRule

```



- example-diff:

```php
<?php
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
<?php
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

## [NoMissingDirPathRule](../src/Rules/NoMissingDirPathRule.php)

The path "%s" was not found



- class:

```

Symplify\PHPStanRules\Rules\NoMissingDirPathRule

```



- example-diff:

```php
<?php
$filePath = __DIR__ . '/missing_location.txt';
```

:x:

<br>

```php
<?php
$filePath = __DIR__ . '/existing_location.txt';
```

:+1:

<br>

## [NoModifyAndReturnSelfObjectRule](../src/Rules/NoModifyAndReturnSelfObjectRule.php)

Use void instead of modify and return self object



- class:

```

Symplify\PHPStanRules\Rules\NoModifyAndReturnSelfObjectRule

```



- example-diff:

```php
<?php
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
<?php
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

## [NoMultiArrayAssignRule](../src/Rules/NoMultiArrayAssignRule.php)

Use value object over multi array assign



- class:

```

Symplify\PHPStanRules\Rules\NoMultiArrayAssignRule

```



- example-diff:

```php
<?php
$values = [];
$values['person']['name'] = 'Tom';
$values['person']['surname'] = 'Dev';
```

:x:

<br>

```php
<?php
$values = [];
$values[] = new Person('Tom', 'Dev');
```

:+1:

<br>

## [NoNestedFuncCallRule](../src/Rules/NoNestedFuncCallRule.php)

Use separate function calls with readable variable names



- class:

```

Symplify\PHPStanRules\Rules\NoNestedFuncCallRule

```



- example-diff:

```php
<?php
$filteredValues = array_filter(array_map($callback, $items));
```

:x:

<br>

```php
<?php
$mappedItems = array_map($callback, $items);
$filteredValues = array_filter($mappedItems);
```

:+1:

<br>

## [NoNetteArrayAccessInControlRule](../packages/nette/src/Rules/NoNetteArrayAccessInControlRule.php)

Avoid using magical unclear array access and use explicit `"$this->getComponent()"` instead



- class:

```

Symplify\PHPStanRules\Nette\Rules\NoNetteArrayAccessInControlRule

```



- example-diff:

```php
<?php
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
<?php
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

## [NoNetteDoubleTemplateAssignRule](../packages/nette/src/Rules/NoNetteDoubleTemplateAssignRule.php)

Avoid double template variable override of "%s"



- class:

```

Symplify\PHPStanRules\Nette\Rules\NoNetteDoubleTemplateAssignRule

```



- example-diff:

```php
<?php
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
<?php
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

## [NoNetteInjectAndConstructorRule](../packages/nette/src/Rules/NoNetteInjectAndConstructorRule.php)

Use either `__construct()` or @inject, not both together



- class:

```

Symplify\PHPStanRules\Nette\Rules\NoNetteInjectAndConstructorRule

```



- example-diff:

```php
<?php
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
<?php
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

## [NoNetteRenderMissingVariableRule](../packages/nette/src/Rules/NoNetteRenderMissingVariableRule.php)

Passed "%s" variable that are not used in the template



- class:

```

Symplify\PHPStanRules\Nette\Rules\NoNetteRenderMissingVariableRule

```



- example-diff:

```php
<?php
use Nette\Application\UI\Control;

final class SomeControl extends Control
{
    public function render()
    {
        $this->template->render(__DIR__ . '/some_file.latte');
    }
}

// some_file.latte
{$usedValue}
```

:x:

<br>

```php
<?php
use Nette\Application\UI\Control;

final class SomeControl extends Control
{
    public function render()
    {
        $this->template->render(__DIR__ . '/some_file.latte', [
            'usedValue' => 'value'
        ]);
    }
}

// some_file.latte
{$usedValue}
```

:+1:

<br>

## [NoNetteRenderUnusedVariableRule](../packages/nette/src/Rules/NoNetteRenderUnusedVariableRule.php)

Extra variables "%s" are passed to the template but never used there



- class:

```

Symplify\PHPStanRules\Nette\Rules\NoNetteRenderUnusedVariableRule

```



- example-diff:

```php
<?php
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
<?php
use Nette\Application\UI\Control;

final class SomeControl extends Control
{
    public function render()
    {
        $this->template->render(__DIR__ . '/some_file.latte', [
            'never_used_in_template' => 'value',
        ]);
    }
}
```

:+1:

<br>

## [NoNetteTemplateVariableReadRule](../packages/nette/src/Rules/NoNetteTemplateVariableReadRule.php)

Avoid `$this->template->variable` for read access, as it can be defined anywhere. Use local `$variable` instead



- class:

```

Symplify\PHPStanRules\Nette\Rules\NoNetteTemplateVariableReadRule

```



- example-diff:

```php
<?php
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
<?php
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

## [NoNullableArrayPropertyRule](../src/Rules/NoNullableArrayPropertyRule.php)

Use required typed property over of nullable array property



- class:

```

Symplify\PHPStanRules\Rules\NoNullableArrayPropertyRule

```



- example-diff:

```php
<?php
final class SomeClass
{
    private ?array $property = null;
}
```

:x:

<br>

```php
<?php
final class SomeClass
{
    private array $property = [];
}
```

:+1:

<br>

## [NoNullablePropertyRule](../src/Rules/NoNullablePropertyRule.php)

Use required typed property over of nullable property



- class:

```

Symplify\PHPStanRules\Rules\NoNullablePropertyRule

```



- example-diff:

```php
<?php
final class SomeClass
{
    private ?DateTime $property = null;
}
```

:x:

<br>

```php
<?php
final class SomeClass
{
    private DateTime $property;
}
```

:+1:

<br>

## [NoParentDuplicatedTraitUseRule](../src/Rules/Complexity/NoParentDuplicatedTraitUseRule.php)

The "%s" trait is already used in parent class. Remove it here



- class:

```

Symplify\PHPStanRules\Rules\Complexity\NoParentDuplicatedTraitUseRule

```



- example-diff:

```php
<?php
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
<?php
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

## [NoParentMethodCallOnEmptyStatementInParentMethodRule](../src/Rules/NoParentMethodCallOnEmptyStatementInParentMethodRule.php)

Do not call parent method if parent method is empty



- class:

```

Symplify\PHPStanRules\Rules\NoParentMethodCallOnEmptyStatementInParentMethodRule

```



- example-diff:

```php
<?php
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
<?php
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

## [NoParentMethodCallOnNoOverrideProcessRule](../src/Rules/NoParentMethodCallOnNoOverrideProcessRule.php)

Do not call parent method if no override process



- class:

```

Symplify\PHPStanRules\Rules\NoParentMethodCallOnNoOverrideProcessRule

```



- example-diff:

```php
<?php
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
<?php
class SomeClass extends Printer
{
}
```

:+1:

<br>

## [NoPostIncPostDecRule](../src/Rules/NoPostIncPostDecRule.php)

Post operation are forbidden, as they make 2 values at the same line. Use pre instead



- class:

```

Symplify\PHPStanRules\Rules\NoPostIncPostDecRule

```



- example-diff:

```php
<?php
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
<?php
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

## [NoProtectedElementInFinalClassRule](../src/Rules/NoProtectedElementInFinalClassRule.php)

Instead of protected element in final class use private element or contract method



- class:

```

Symplify\PHPStanRules\Rules\NoProtectedElementInFinalClassRule

```



- example-diff:

```php
<?php
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
<?php
final class SomeClass
{
    private function run()
    {
    }
}
```

:+1:

<br>

## [NoReferenceRule](../src/Rules/NoReferenceRule.php)

Use explicit return value over magic &reference



- class:

```

Symplify\PHPStanRules\Rules\NoReferenceRule

```



- example-diff:

```php
<?php
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
<?php
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

## [NoReturnArrayVariableListRule](../src/Rules/NoReturnArrayVariableListRule.php)

Use value object over return of values



- class:

```

Symplify\PHPStanRules\Rules\NoReturnArrayVariableListRule

```



- example-diff:

```php
<?php
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
<?php
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

## [NoReturnSetterMethodRule](../src/Rules/NoReturnSetterMethodRule.php)

Setter method cannot return anything, only set value



- class:

```

Symplify\PHPStanRules\Rules\NoReturnSetterMethodRule

```



- example-diff:

```php
<?php
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
<?php
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

## [NoSetterOnServiceRule](../src/Rules/NoSetterOnServiceRule.php)

Do not use setter on a service



- class:

```

Symplify\PHPStanRules\Rules\NoSetterOnServiceRule

```



- example-diff:

```php
<?php
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
<?php
class SomeEntity
{
    public function setSomeValue($value)
    {
    }
}
```

:+1:

<br>

## [NoShortNameRule](../packages/object-calisthenics/src/Rules/NoShortNameRule.php)

Do not name "%s", shorter than %d chars

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\ObjectCalisthenics\Rules\NoShortNameRule

```



- example-diff:

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
<?php
function is()
{
}
```

:x:

<br>

```php
<?php
function isClass()
{
}
```

:+1:

<br>

## [NoStaticPropertyRule](../src/Rules/NoStaticPropertyRule.php)

Do not use static property



- class:

```

Symplify\PHPStanRules\Rules\NoStaticPropertyRule

```



- example-diff:

```php
<?php
final class SomeClass
{
    private static $customFileNames = [];
}
```

:x:

<br>

```php
<?php
final class SomeClass
{
    private $customFileNames = [];
}
```

:+1:

<br>

## [NoSuffixValueObjectClassRule](../src/Rules/NoSuffixValueObjectClassRule.php)

Value Object class name "%s" must be without "ValueObject" suffix.



- class:

```

Symplify\PHPStanRules\Rules\NoSuffixValueObjectClassRule

```



- example-diff:

```php
<?php
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
<?php
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

## [NoTemplateMagicAssignInControlRule](../packages/nette/src/Rules/NoTemplateMagicAssignInControlRule.php)

Instead of magic template assign use `render()` param and explicit variable



- class:

```

Symplify\PHPStanRules\Nette\Rules\NoTemplateMagicAssignInControlRule

```



- example-diff:

```php
<?php
use Nette\Application\UI\Control;

final class SomeControl extends Control
{
    public function render()
    {
        $this->template->value = 1000;

        $this->template->render(__DIR__ . '/some_file.latte');
    }
}
```

:x:

<br>

```php
<?php
use Nette\Application\UI\Control;

final class SomeControl extends Control
{
    public function render()
    {
        $this->template->render(__DIR__ . '/some_file.latte', [
            'value' => 1000,
        ]);
    }
}
```

:+1:

<br>

## [NoTraitRule](../src/Rules/NoTraitRule.php)

Do not use trait, extract to a service and dependency injection instead



- class:

```

Symplify\PHPStanRules\Rules\NoTraitRule

```



- example-diff:

```php
<?php
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
<?php
class SomeService
{
    public function run(...)
    {
    }
}
```

:+1:

<br>

## [NoTwigMissingVariableRule](../packages/symfony/src/Rules/NoTwigMissingVariableRule.php)

Variable "%s" is used in template but missing in `render()` method



- class:

```

Symplify\PHPStanRules\Symfony\Rules\NoTwigMissingVariableRule

```



- example-diff:

```php
<?php
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SomeController extends AbstractController
{
    public function __invoke()
    {
        return $this->render(__DIR__ . '/some_file.twig', [
            'non_existing_variable' => 'value',
        ]);
    }
}
```

:x:

<br>

```php
<?php
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SomeController extends AbstractController
{
    public function __invoke()
    {
        return $this->render(__DIR__ . '/some_file.twig', [
            'existing_variable' => 'value',
        ]);
    }
}
```

:+1:

<br>

## [NoTwigRenderUnusedVariableRule](../packages/symfony/src/Rules/NoTwigRenderUnusedVariableRule.php)

Passed "%s" variable is not used in the template



- class:

```

Symplify\PHPStanRules\Symfony\Rules\NoTwigRenderUnusedVariableRule

```



- example-diff:

```php
<?php
$environment = new Twig\Environment();
$environment->render(__DIR__ . '/some_file.twig', [
    'used_variable' => 'value',
]);
```

:x:

<br>

```php
<?php
$environment = new Twig\Environment();
$environment->render(__DIR__ . '/some_file.twig', [
    'unused_variable' => 'value',
]);
```

:+1:

<br>

## [NoVoidGetterMethodRule](../src/Rules/NoVoidGetterMethodRule.php)

Getter method must return something, not void



- class:

```

Symplify\PHPStanRules\Rules\NoVoidGetterMethodRule

```



- example-diff:

```php
<?php
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
<?php
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

## [PreferConstantValueRule](../src/Rules/PreferConstantValueRule.php)

Use defined constant %s::%s over string %s

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\PreferConstantValueRule

```



- example-diff:

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
<?php
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
<?php
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

## [PreferredAttributeOverAnnotationRule](../src/Rules/PreferredAttributeOverAnnotationRule.php)

Use attribute instead of "%s" annotation

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\PreferredAttributeOverAnnotationRule

```



- example-diff:

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
<?php
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
<?php
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

## [PreferredClassRule](../src/Rules/PreferredClassRule.php)

Instead of "%s" class/interface use "%s"

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\PreferredClassRule

```



- example-diff:

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
<?php
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
<?php
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

## [PreferredMethodCallOverFuncCallRule](../src/Rules/PreferredMethodCallOverFuncCallRule.php)

Use "%s" class and `"%s()"` method call over `"%s()"` func call

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\PreferredMethodCallOverFuncCallRule

```



- example-diff:

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
<?php
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
<?php
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

## [PreferredRawDataInTestDataProviderRule](../src/Rules/PreferredRawDataInTestDataProviderRule.php)

Code configured at `setUp()` cannot be used in data provider. Move it to `test()` method



- class:

```

Symplify\PHPStanRules\Rules\PreferredRawDataInTestDataProviderRule

```



- example-diff:

```php
<?php
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
<?php
use stdClass;

final class UseRawDataForTestDataProviderTest
{
    private $obj;

    protected function setUp()
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

## [PrefixAbstractClassRule](../src/Rules/PrefixAbstractClassRule.php)

Abstract class name "%s" must be prefixed with "Abstract"



- class:

```

Symplify\PHPStanRules\Rules\PrefixAbstractClassRule

```



- example-diff:

```php
<?php
abstract class SomeClass
{
}
```

:x:

<br>

```php
<?php
abstract class AbstractSomeClass
{
}
```

:+1:

<br>

## [PreventDoubleSetParameterRule](../src/Rules/PreventDoubleSetParameterRule.php)

Set param value is overriden. Merge it to previous set above



- class:

```

Symplify\PHPStanRules\Rules\PreventDoubleSetParameterRule

```



- example-diff:

```php
<?php
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
<?php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('some_param', [1, 2]);
};
```

:+1:

<br>

## [PreventDuplicateClassMethodRule](../src/Rules/PreventDuplicateClassMethodRule.php)

Content of method `"%s()"` is duplicated with method `"%s()"` in "%s" class. Use unique content or service instead

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\PreventDuplicateClassMethodRule

```



- example-diff:

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
<?php
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
<?php
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

## [PreventParentMethodVisibilityOverrideRule](../src/Rules/PreventParentMethodVisibilityOverrideRule.php)

Change `"%s()"` method visibility to "%s" to respect parent method visibility.



- class:

```

Symplify\PHPStanRules\Rules\PreventParentMethodVisibilityOverrideRule

```



- example-diff:

```php
<?php
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
<?php
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

## [RegexSuffixInRegexConstantRule](../src/Rules/RegexSuffixInRegexConstantRule.php)

Name your constant with "_REGEX" suffix, instead of "%s"



- class:

```

Symplify\PHPStanRules\Rules\RegexSuffixInRegexConstantRule

```



- example-diff:

```php
<?php
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
<?php
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

## [RequireAttributeNameRule](../src/Rules/RequireAttributeNameRule.php)

Attribute must have all names explicitly defined



- class:

```

Symplify\PHPStanRules\Rules\RequireAttributeNameRule

```



- example-diff:

```php
<?php
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
<?php
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

## [RequireAttributeNamespaceRule](../src/Rules/Domain/RequireAttributeNamespaceRule.php)

Attribute must be located in "Attribute" namespace



- class:

```

Symplify\PHPStanRules\Rules\Domain\RequireAttributeNamespaceRule

```



- example-diff:

```php
<?php
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
<?php
// app/Attribute/SomeAttribute.php
namespace App\Attribute;

#[\Attribute]
final class SomeAttribute
{
}
```

:+1:

<br>

## [RequireConstantInAttributeArgumentRule](../src/Rules/RequireConstantInAttributeArgumentRule.php)

Argument "%s" must be a constant

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\RequireConstantInAttributeArgumentRule

```



- example-diff:

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
<?php
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
<?php
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

## [RequireConstantInMethodCallPositionRule](../src/Rules/Enum/RequireConstantInMethodCallPositionRule.php)

Parameter argument on position %d must use constant

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\Enum\RequireConstantInMethodCallPositionRule

```



- example-diff:

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
<?php
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
<?php
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

## [RequireDataProviderTestMethodRule](../src/Rules/RequireDataProviderTestMethodRule.php)

The `"%s()"` method must use data provider

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\RequireDataProviderTestMethodRule

```



- example-diff:

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
<?php
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
<?php
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

## [RequireExceptionNamespaceRule](../src/Rules/Domain/RequireExceptionNamespaceRule.php)

`Exception` must be located in "Exception" namespace



- class:

```

Symplify\PHPStanRules\Rules\Domain\RequireExceptionNamespaceRule

```



- example-diff:

```php
<?php
// app/Controller/SomeException.php
namespace App\Controller;

final class SomeException extends Exception
{
}
```

:x:

<br>

```php
<?php
// app/Exception/SomeException.php
namespace App\Exception;

final class SomeException extends Exception
{
}
```

:+1:

<br>

## [RequireInvokableControllerRule](../packages/symfony/src/Rules/RequireInvokableControllerRule.php)

Use invokable controller with `__invoke()` method instead of named action method



- class:

```

Symplify\PHPStanRules\Symfony\Rules\RequireInvokableControllerRule

```



- example-diff:

```php
<?php
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class SomeController extends AbstractController
{
    #[Route()]
    public function someMethod()
    {
    }
}
```

:x:

<br>

```php
<?php
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class SomeController extends AbstractController
{
    #[Route()]
    public function __invoke()
    {
    }
}
```

:+1:

<br>

## [RequireMethodCallArgumentConstantRule](../src/Rules/RequireMethodCallArgumentConstantRule.php)

Method call argument on position %d must use constant (e.g. "Option::NAME") over value

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\RequireMethodCallArgumentConstantRule

```



- example-diff:

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
<?php
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
<?php
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

## [RequireNativeArraySymfonyRenderCallRule](../packages/symfony/src/Rules/RequireNativeArraySymfonyRenderCallRule.php)

Second argument of `$this->render("template.twig",` [...]) method should be explicit array, to avoid accidental variable override, see https://tomasvotruba.com/blog/2021/02/15/how-dangerous-is-your-nette-template-assign/



- class:

```

Symplify\PHPStanRules\Symfony\Rules\RequireNativeArraySymfonyRenderCallRule

```



- example-diff:

```php
<?php
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SomeController extends AbstractController
{
    public function default()
    {
        $parameters['name'] = 'John';
        $parameters['name'] = 'Doe';
        return $this->render('...', $parameters);
    }
}
```

:x:

<br>

```php
<?php
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SomeController extends AbstractController
{
    public function default()
    {
        return $this->render('...', [
            'name' => 'John',
        ]);
    }
}
```

:+1:

<br>

## [RequireNewArgumentConstantRule](../src/Rules/Enum/RequireNewArgumentConstantRule.php)

New expression argument on position %d must use constant over value

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\Enum\RequireNewArgumentConstantRule

```



- example-diff:

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
<?php
use Symfony\Component\Console\Input\InputOption;

$inputOption = new InputOption('name', null, 2);
```

:x:

<br>

```php
<?php
use Symfony\Component\Console\Input\InputOption;

$inputOption = new InputOption('name', null, InputOption::VALUE_REQUIRED);
```

:+1:

<br>

## [RequireQuoteStringValueSprintfRule](../src/Rules/RequireQuoteStringValueSprintfRule.php)

"%s" in `sprintf()` format must be quoted



- class:

```

Symplify\PHPStanRules\Rules\RequireQuoteStringValueSprintfRule

```



- example-diff:

```php
<?php
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
<?php
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

## [RequireSkipPrefixForRuleSkippedFixtureRule](../src/Rules/RequireSkipPrefixForRuleSkippedFixtureRule.php)

Skipped tested file must start with "Skip" prefix



- class:

```

Symplify\PHPStanRules\Rules\RequireSkipPrefixForRuleSkippedFixtureRule

```



- example-diff:

```php
<?php
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
<?php
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

## [RequireSpecificReturnTypeOverAbstractRule](../src/Rules/Explicit/RequireSpecificReturnTypeOverAbstractRule.php)

Provide more specific return type "%s" over abstract one



- class:

```

Symplify\PHPStanRules\Rules\Explicit\RequireSpecificReturnTypeOverAbstractRule

```



- example-diff:

```php
<?php
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
<?php
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

## [RequireStringArgumentInConstructorRule](../src/Rules/RequireStringArgumentInConstructorRule.php)

Use quoted string in constructor "new `%s()"` argument on position %d instead of "::class. It prevent scoping of the class in building prefixed package.

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\RequireStringArgumentInConstructorRule

```



- example-diff:

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
<?php
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
<?php
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

## [RequireStringArgumentInMethodCallRule](../src/Rules/RequireStringArgumentInMethodCallRule.php)

Use quoted string in method call `"%s()"` argument on position %d instead of "::class. It prevent scoping of the class in building prefixed package.

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\RequireStringArgumentInMethodCallRule

```



- example-diff:

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
<?php
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
<?php
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

## [RequireStringRegexMatchKeyRule](../src/Rules/RequireStringRegexMatchKeyRule.php)

Regex must use string named capture groups instead of numeric



- class:

```

Symplify\PHPStanRules\Rules\RequireStringRegexMatchKeyRule

```



- example-diff:

```php
<?php
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
<?php
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

## [RequireTemplateInNetteControlRule](../packages/nette/src/Rules/RequireTemplateInNetteControlRule.php)

Set control template explicitly in `$this->template->setFile(...)` or `$this->template->render(...)`



- class:

```

Symplify\PHPStanRules\Nette\Rules\RequireTemplateInNetteControlRule

```



- example-diff:

```php
<?php
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
<?php
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

## [RequireThisCallOnLocalMethodRule](../src/Rules/RequireThisCallOnLocalMethodRule.php)

Use "$this-><method>()" instead of "self::<method>()" to call local method



- class:

```

Symplify\PHPStanRules\Rules\RequireThisCallOnLocalMethodRule

```



- example-diff:

```php
<?php
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
<?php
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

## [RequireThisOnParentMethodCallRule](../src/Rules/RequireThisOnParentMethodCallRule.php)

Use "$this-><method>()" instead of "parent::<method>()" unless in the same named method



- class:

```

Symplify\PHPStanRules\Rules\RequireThisOnParentMethodCallRule

```



- example-diff:

```php
<?php
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
<?php
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

## [RequireUniqueEnumConstantRule](../src/Rules/Enum/RequireUniqueEnumConstantRule.php)

Enum constants "%s" are duplicated. Make them unique instead



- class:

```

Symplify\PHPStanRules\Rules\Enum\RequireUniqueEnumConstantRule

```



- example-diff:

```php
<?php
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
<?php
use MyCLabs\Enum\Enum;

class SomeClass extends Enum
{
    private const YES = 'yes';

    private const NO = 'no';
}
```

:+1:

<br>

## [RequiredAbstractClassKeywordRule](../src/Rules/RequiredAbstractClassKeywordRule.php)

Class name starting with "Abstract" must have an `abstract` keyword



- class:

```

Symplify\PHPStanRules\Rules\RequiredAbstractClassKeywordRule

```



- example-diff:

```php
<?php
class AbstractClass
{
}
```

:x:

<br>

```php
<?php
abstract class AbstractClass
{
}
```

:+1:

<br>

## [SameNamedParamFamilyRule](../src/Rules/Explicit/SameNamedParamFamilyRule.php)

Arguments names conflicts with parent class method: %s. This will break named arguments



- class:

```

Symplify\PHPStanRules\Rules\Explicit\SameNamedParamFamilyRule

```



- example-diff:

```php
<?php
interface SomeInterface
{
    public function run($value);
}

final class SomeClass implements SomeInterface
{
    public function run($differentValue)
    {
    }
}
```

:x:

<br>

```php
<?php
interface SomeInterface
{
    public function run($value);
}

final class SomeClass implements SomeInterface
{
    public function run($value)
    {
    }
}
```

:+1:

<br>

## [SeeAnnotationToTestRule](../src/Rules/SeeAnnotationToTestRule.php)

Class "%s" is missing `@see` annotation with test case class reference

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\SeeAnnotationToTestRule

```



- example-diff:

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
<?php
class SomeClass extends Rule
{
}
```

:x:

<br>

```php
<?php
/**
 * @see SomeClassTest
 */
class SomeClass extends Rule
{
}
```

:+1:

<br>

## [SingleNetteInjectMethodRule](../packages/nette/src/Rules/SingleNetteInjectMethodRule.php)

Use single inject*() class method per class



- class:

```

Symplify\PHPStanRules\Nette\Rules\SingleNetteInjectMethodRule

```



- example-diff:

```php
<?php
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
<?php
class SomeClass
{
    private $type;

    private $anotherType;

    public function injectSomeClass(
        Type $type,
        AnotherType $anotherType
    ) {
        $this->type = $type;
        $this->anotherType = $anotherType;
    }
}
```

:+1:

<br>

## [SuffixInterfaceRule](../src/Rules/SuffixInterfaceRule.php)

Interface must be suffixed with "Interface" exclusively



- class:

```

Symplify\PHPStanRules\Rules\SuffixInterfaceRule

```



- example-diff:

```php
<?php
interface SomeClass
{
}
```

:x:

<br>

```php
<?php
interface SomeInterface
{
}
```

:+1:

<br>

## [SuffixTraitRule](../src/Rules/SuffixTraitRule.php)

Trait must be suffixed by "Trait" exclusively



- class:

```

Symplify\PHPStanRules\Rules\SuffixTraitRule

```



- example-diff:

```php
<?php
trait SomeClass
{
}
```

:x:

<br>

```php
<?php
trait SomeTrait
{
}
```

:+1:

<br>

## [TooDeepNewClassNestingRule](../src/Rules/TooDeepNewClassNestingRule.php)

new <class> is limited to %d "new <class>(new <class>))" nesting to each other. You have %d nesting.

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\TooDeepNewClassNestingRule

```



- example-diff:

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
<?php
$someObject = new A(new B(new C()));
```

:x:

<br>

```php
<?php
$firstObject = new B(new C());
$someObject = new A($firstObject);
```

:+1:

<br>

## [TooLongVariableRule](../src/Rules/TooLongVariableRule.php)

Variable "$%s" is too long with %d chars. Narrow it under %d chars

:wrench: **configure it!**



- class:

```

Symplify\PHPStanRules\Rules\TooLongVariableRule

```



- example-diff:

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
<?php
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
<?php
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

## [TwigCompleteCheckRule](../packages/symfony/src/Rules/TwigCompleteCheckRule.php)

Complete analysis of PHP code generated from Twig template



- class:

```

Symplify\PHPStanRules\Symfony\Rules\TwigCompleteCheckRule

```



- example-diff:

```php
<?php
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SomeController extends AbstractController
{
    public function __invoke()
    {
        return $this->render(__DIR__ . '/some_file.twig', [
            'some' => new SomeObject()
        ]);
    }
}

// some_file.twig
{{ some.non_existing_method }}
```

:x:

<br>

```php
<?php
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SomeController extends AbstractController
{
    public function __invoke()
    {
        return $this->render(__DIR__ . '/some_file.twig', [
            'some' => new SomeObject()
        ]);
    }
}

// some_file.twig
{{ some.existing_method }}
```

:+1:

<br>

## [UppercaseConstantRule](../src/Rules/UppercaseConstantRule.php)

Constant "%s" must be uppercase



- class:

```

Symplify\PHPStanRules\Rules\UppercaseConstantRule

```



- example-diff:

```php
<?php
final class SomeClass
{
    public const some = 'value';
}
```

:x:

<br>

```php
<?php
final class SomeClass
{
    public const SOME = 'value';
}
```

:+1:

<br>

## [ValidNetteInjectRule](../packages/nette/src/Rules/ValidNetteInjectRule.php)

Nette `@inject` annotation/#[Inject] must be valid



- class:

```

Symplify\PHPStanRules\Nette\Rules\ValidNetteInjectRule

```



- example-diff:

```php
<?php
class SomeClass
{
    /**
     * @inject
     */
    private $someDependency;
}
```

:x:

<br>

```php
<?php
class SomeClass
{
    /**
     * @inject
     */
    public $someDependency;
}
```

:+1:

<br>

## [ValueObjectOverArrayShapeRule](../src/Rules/Explicit/ValueObjectOverArrayShapeRule.php)

Instead of array shape, use value object with specific types in constructor and getters



- class:

```

Symplify\PHPStanRules\Rules\Explicit\ValueObjectOverArrayShapeRule

```



- example-diff:

```php
<?php
/**
 * @return array{line: int}
 */
function createConfiguration()
{
    return [
        'line' => 100,
    ];
}
```

:x:

<br>

```php
<?php
/**
 * @return array{line: int}
 */
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
