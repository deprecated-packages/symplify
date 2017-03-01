# Controller Autowire

[![Build Status](https://img.shields.io/travis/Symplify/ControllerAutowire.svg?style=flat-square)](https://travis-ci.org/Symplify/ControllerAutowire)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Symplify/ControllerAutowire.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/ControllerAutowire)
[![Downloads](https://img.shields.io/packagist/dt/symplify/controller-autowire.svg?style=flat-square)](https://packagist.org/packages/symplify/controller-autowire)


This bundle does only 2 things. But does them well:

- **1. registers controllers as services and**
- **2. enables constructor autowiring for them**


Still wondering **why use controller as services**? Check [this](http://richardmiller.co.uk/2011/04/15/symfony2-controller-as-service) and
[this](http://php-and-symfony.matthiasnoback.nl/2014/06/how-to-create-framework-independent-controllers/) article.

## Install

```bash
composer require symplify/controller-autowire
```

Add bundle to `AppKernel.php`:

```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symplify\ControllerAutowire\SymplifyControllerAutowireBundle(),
            // ...
        ];
    }
}
```


## Usage

```php
class SomeController
{
    private $someClass;

    public function __construct(SomeClass $someClass)
    {
        $this->someClass = $someClass;
    }
}
```


## Used to FrameworkBundle's controller? Use helpers traits!

Inspired by [pull](https://github.com/symfony/symfony/pull/18193) [requests](https://github.com/symfony/symfony/pull/20493) to Symfony and setter injection that are currently on-hold, **here are the traits you can use right now**:

```php
use Symplify\ControllerAutowire\Controller\Routing\ControllerAwareTrait;

final class SomeController
{
    use ControllerAwareTrait;

    public function someAction()
    {
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        // ...

        return $this->redirectToRoute('my_route');
    }
}
```
 

### Do you prefer only traits you use?
 
```php
use Symplify\ControllerAutowire\Controller\Routing\ControllerRoutingTrait;

final class SomeController
{
    use ControllerRoutingTrait;

    public function someAction()
    {
        return $this->redirectToRoute('my_route');
    }
}
```

Just type `Controller*Trait` in your IDE to autocomplete any of these traits.


That's all :)


## Contributing

Send [issue](https://github.com/Symplify/Symplify/issues) or [pull-request](https://github.com/Symplify/Symplify/pulls) to main repository.