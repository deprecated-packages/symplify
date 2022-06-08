# 5 Rules Overview

## CheckOptionArgumentCommandRule

Argument and options "%s" got confused

- class: [`Symplify\PHPStanRules\Symfony\Rules\CheckOptionArgumentCommandRule`](../packages/symfony/src/Rules/CheckOptionArgumentCommandRule.php)

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

## PreventDoubleSetParameterRule

Set param value is overriden. Merge it to previous set above

- class: [`Symplify\PHPStanRules\Symfony\Rules\PreventDoubleSetParameterRule`](../packages/symfony/src/Rules/PreventDoubleSetParameterRule.php)

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

## RequireInvokableControllerRule

Use invokable controller with `__invoke()` method instead of named action method

- class: [`Symplify\PHPStanRules\Symfony\Rules\RequireInvokableControllerRule`](../packages/symfony/src/Rules/RequireInvokableControllerRule.php)

```php
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

## RequireNamedCommandRule

The command is missing `$this->setName("...")` in `configure()` method

- class: [`Symplify\PHPStanRules\Symfony\Rules\RequireNamedCommandRule`](../packages/symfony/src/Rules/RequireNamedCommandRule.php)

```php
use Symfony\Component\Console\Command\Command;

final class SomeCommand extends Command
{
    public function configure()
    {
    }
}
```

:x:

<br>

```php
use Symfony\Component\Console\Command\Command;

final class SomeCommand extends Command
{
    public function configure()
    {
        $this->setName('some');
    }
}
```

:+1:

<br>

## RequireNativeArraySymfonyRenderCallRule

Second argument of `$this->render("template.twig",` [...]) method should be explicit array, to avoid accidental variable override, see https://tomasvotruba.com/blog/2021/02/15/how-dangerous-is-your-nette-template-assign/

- class: [`Symplify\PHPStanRules\Symfony\Rules\RequireNativeArraySymfonyRenderCallRule`](../packages/symfony/src/Rules/RequireNativeArraySymfonyRenderCallRule.php)

```php
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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SomeController extends AbstractController
{
    public function default()
    {
        return $this->render('...', [
            'name' => 'John'
        ]);
    }
}
```

:+1:

<br>
