# 11 Rules Overview

## DibiMaskMatchesVariableTypeRule

Modifier "%s" is not matching passed variable type "%s". The "%s" type is expected - see https://dibiphp.com/en/documentation#toc-modifiers-for-arrays

- class: [`Symplify\PHPStanRules\Nette\Rules\DibiMaskMatchesVariableTypeRule`](../packages/nette/src/Rules/DibiMaskMatchesVariableTypeRule.php)

```php
$database->query('INSERT INTO table %v', 'string');
```

:x:

<br>

```php
$database->query('INSERT INTO table %v', ['name' => 'Matthias']);
```

:+1:

<br>

## ForbiddenNetteInjectOverrideRule

Assign to already injected property is not allowed

- class: [`Symplify\PHPStanRules\Nette\Rules\ForbiddenNetteInjectOverrideRule`](../packages/nette/src/Rules/ForbiddenNetteInjectOverrideRule.php)

```php
use Nette\DI\Attributes\Inject;

abstract class AbstractParent
{
    /**
     * @var SomeType
     */
    #[Inject]
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
use Nette\DI\Attributes\Inject;

abstract class AbstractParent
{
    /**
     * @var SomeType
     */
    #[Inject]
    protected $someType;
}

final class SomeChild extends AbstractParent
{
}
```

:+1:

<br>

## NoInjectOnFinalRule

Use constructor on final classes, instead of property injection

- class: [`Symplify\PHPStanRules\Nette\Rules\NoInjectOnFinalRule`](../packages/nette/src/Rules/NoInjectOnFinalRule.php)

```php
use Nette\DI\Attributes\Inject;

final class SomePresenter
{
     #[Inject]
    public $property;
}
```

:x:

<br>

```php
use Nette\DI\Attributes\Inject;

abstract class SomePresenter
{
    #[Inject]
    public $property;
}
```

:+1:

<br>

## NoNetteArrayAccessInControlRule

Avoid using magical unclear array access and use explicit `"$this->getComponent()"` instead

- class: [`Symplify\PHPStanRules\Nette\Rules\NoNetteArrayAccessInControlRule`](../packages/nette/src/Rules/NoNetteArrayAccessInControlRule.php)

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

- class: [`Symplify\PHPStanRules\Nette\Rules\NoNetteDoubleTemplateAssignRule`](../packages/nette/src/Rules/NoNetteDoubleTemplateAssignRule.php)

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

Use either `__construct()` or @inject, not both together

- class: [`Symplify\PHPStanRules\Nette\Rules\NoNetteInjectAndConstructorRule`](../packages/nette/src/Rules/NoNetteInjectAndConstructorRule.php)

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

## NoNetteTemplateVariableReadRule

Avoid `$this->template->variable` for read access, as it can be defined anywhere. Use local `$variable` instead

- class: [`Symplify\PHPStanRules\Nette\Rules\NoNetteTemplateVariableReadRule`](../packages/nette/src/Rules/NoNetteTemplateVariableReadRule.php)

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

## NoTemplateMagicAssignInControlRule

Instead of magic template assign use `render()` param and explicit variable

- class: [`Symplify\PHPStanRules\Nette\Rules\NoTemplateMagicAssignInControlRule`](../packages/nette/src/Rules/NoTemplateMagicAssignInControlRule.php)

```php
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
use Nette\Application\UI\Control;

final class SomeControl extends Control
{
    public function render()
    {
        $this->template->render(__DIR__ . '/some_file.latte', [
            'value' => 1000
        ]);
    }
}
```

:+1:

<br>

## RequireTemplateInNetteControlRule

Set control template explicitly in `$this->template->setFile(...)` or `$this->template->render(...)`

- class: [`Symplify\PHPStanRules\Nette\Rules\RequireTemplateInNetteControlRule`](../packages/nette/src/Rules/RequireTemplateInNetteControlRule.php)

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

## SingleNetteInjectMethodRule

Use single inject*() class method per class

- class: [`Symplify\PHPStanRules\Nette\Rules\SingleNetteInjectMethodRule`](../packages/nette/src/Rules/SingleNetteInjectMethodRule.php)

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

## ValidNetteInjectRule

Property with `@inject` annotation or #[Nette\DI\Attributes\Inject] attribute must be public

- class: [`Symplify\PHPStanRules\Nette\Rules\ValidNetteInjectRule`](../packages/nette/src/Rules/ValidNetteInjectRule.php)

```php
use Nette\DI\Attributes\Inject;

class SomeClass
{
    #[Inject]
    private $someDependency;
}
```

:x:

<br>

```php
use Nette\DI\Attributes\Inject;

class SomeClass
{
    #[Inject]
    public $someDependency;
}
```

:+1:

<br>
