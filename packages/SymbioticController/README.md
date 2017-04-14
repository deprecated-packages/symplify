# Symbiotic Controller

[![Build Status](https://img.shields.io/travis/Symplify/SymbioticController/master.svg?style=flat-square)](https://travis-ci.org/Symplify/SymbioticController)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Symplify/SymbioticController.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/SymbioticController)
[![Downloads](https://img.shields.io/packagist/dt/symplify/symbiotic-controller.svg?style=flat-square)](https://packagist.org/packages/symplify/symbiotic-controller)

*Write controller once and let many other frameworks use it.*

## Install

```bash
composer require symplify/symbiotic-controller
```


## Usage in Nette

### Register Extensions to you App

```yaml
# app/config/config.neon

extensions:
    - Symplify\SymbioticController\Adapter\Nette\DI\SymbioticControllerExtension
    - Symplify\SymfonyEventDispatcher\Adapter\Nette\DI\SymfonyEventDispatcherExtension
```


### 1. Create `app/Presenters/ContactPresenter.php` Presenter with `__invoke()` Method

#### A. Classic Render Action

This is what you use the most often.

```php
namespace App\Presenter;

use Symplify\SymbioticController\Contract\Template\TemplateRendererInterface;

final class StandalonePresenter
{
    /**
     * @var TemplateRendererInterface
     */
    private $templateRenderer;

    public function __construct(TemplateRendererInterface $templateRenderer)
    {
        $this->templateRenderer = $templateRenderer;
    }

    public function __invoke(): string
    {
        return $this->templateRenderer->renderFileWithParameters(
            __DIR__ . '/templates/Contact.latte'
        );
    }
}
```

#### B. The Simplest Response

```php
namespace App\Presenters;

use Nette\Application\Responses\TextResponse;

final class ContactPresenter
{
    public function __invoke(): TextResponse
    {
        return new TextResponse('Hi!');
    }
}
```

#### C. Or Json Response

```php
namespace App\Presenters;

use Nette\Application\Responses\JsonResponse;

final class ContactPresenter
{
    public function __invoke(): TextResponse
    {
        return new JsonResponse('Hi!');
    }
}
```

### 2. Create `app/templates/Contact.latte` template

```twig
Hey :-)
```

### 3. Register Presenter Route

```php
# app/Router/RouterFactory.php

namespace App\Router;

final class RouterFactory
{
    public function create(): RouteList
    {
        $routes = new RouteList;
        $routes[] = new PresenterRoute('/contact', ContactPresenter::class);
        $routes[] = new Route('<presenter>/<action>', 'Homepage:default');

        return $routes;
    }
}
```

### 4. Open page in Browser and Enjoy

That's all :)
