# 3 Rules Overview

## LatteCompleteCheckRule

Complete analysis of PHP code generated from Latte template

- class: [`Symplify\PHPStanLatteRules\Rules\LatteCompleteCheckRule`](../src/Rules/LatteCompleteCheckRule.php)

```php
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

## NoNetteRenderMissingVariableRule

Passed "%s" variable that are not used in the template

- class: [`Symplify\PHPStanLatteRules\Rules\NoNetteRenderMissingVariableRule`](../src/Rules/NoNetteRenderMissingVariableRule.php)

```php
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

## NoNetteRenderUnusedVariableRule

Extra variables "%s" are passed to the template but never used there

- class: [`Symplify\PHPStanLatteRules\Rules\NoNetteRenderUnusedVariableRule`](../src/Rules/NoNetteRenderUnusedVariableRule.php)

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
            'never_used_in_template' => 'value'
        ]);
    }
}
```

:+1:

<br>
