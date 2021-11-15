# 3 Rules Overview

## NoTwigMissingVariableRule

Variable "%s" is used in template but missing in `render()` method

- class: [`Symplify\PHPStanTwigRules\Rules\NoTwigMissingVariableRule`](../src/Rules/NoTwigMissingVariableRule.php)

```php
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

## NoTwigRenderUnusedVariableRule

Passed "%s" variable is not used in the template

- class: [`Symplify\PHPStanTwigRules\Rules\NoTwigRenderUnusedVariableRule`](../src/Rules/NoTwigRenderUnusedVariableRule.php)

```php
use Twig\Environment;

$environment = new Environment();
$environment->render(__DIR__ . '/some_file.twig', [
    'unused_variable' => 'value',
]);
```

:x:

<br>

```php
use Twig\Environment;

$environment = new Environment();
$environment->render(__DIR__ . '/some_file.twig', [
    'used_variable' => 'value',
]);
```

:+1:

<br>

## TwigCompleteCheckRule

Complete analysis of PHP code generated from Twig template

- class: [`Symplify\PHPStanTwigRules\Rules\TwigCompleteCheckRule`](../src/Rules/TwigCompleteCheckRule.php)

```php
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
