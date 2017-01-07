# The Simplest Symfony Event Dispatcher integration to frameworks

[![Build Status](https://img.shields.io/travis/Symplify/SymfonyEventDispatcher.svg?style=flat-square)](https://travis-ci.org/Symplify/SymfonyEventDispatcher)
[![Quality Score](https://img.shields.io/scrutinizer/g/Symplify/SymfonyEventDispatcher.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/SymfonyEventDispatcher)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Symplify/SymfonyEventDispatcher.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/SymfonyEventDispatcher)
[![Downloads](https://img.shields.io/packagist/dt/symplify/symfony-event-dispatcher.svg?style=flat-square)](https://packagist.org/packages/symplify/symfony-event-dispatcher)
[![Latest stable](https://img.shields.io/packagist/v/symplify/symfony-event-dispatcher.svg?style=flat-square)](https://packagist.org/packages/symplify/symfony-event-dispatcher)


## Install

```sh
$ composer require symplify/symfony-event-dispatcher
```

### Nette

Register the extension in `config.neon`:

```yaml
// app/config/config.neon
extensions:
	- Symplify\SymfonyEventDispatcher\Adapter\Nette\DI\SymfonyEventDispatcherExtension
```


### Symfony

Register the Bundle to `AppKernel`:

```php
class AppKernel extends Kernel
{
    public function registerBundles() : array
    {
        $bundles = [
            new Symplify\SymfonyEventDispatcher\Adapter\Symfony\SymfonyEventDispatcherBundle(), 
            // ...
        ];
    }
}
```


## Usage

See [short article about EventDispatcher](http://pehapkari.cz/blog/2016/12/05/symfony-event-dispatcher).
**This article is tested** &ndash; it will be still up-to-date with Symfony 4+. 


### 1. Basically create class that implements `Symfony\Component\EventDispatcher\SubscriberInterface`:

```php
// app/EventSubscriber/CheckRequestEventSubscriber.php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class CheckRequestEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var bool
     */
    public $isUserNotified = false;

    public static function getSubscribedEvents() : array
    {
        // in format ['event name' => 'public function name that will be called']
        return [KernelEvents::REQUEST => 'validateRequest'];
    }

    public function validateRequest()
    {
        // some logic to send notification
        $this->isUserNotified = true;
    }
}
```

### 2. Register it to services

**In Nette**

```yaml
// app/config/config.neon
services:
    - App\EventSubscriber\CheckRequestEventSubscriber
```

**In Symfony**

```yaml
// app/config/service.yaml
services:
    event_subscriber.check_request:
        class: App\EventSubscriber\CheckRequestEventSubscriber
```

And it works :)

That's all!

## Testing

```sh
vendor/bin/phpunit
composer check-cs
```
