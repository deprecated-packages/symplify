---
title: Hooks
id: 8
---

Statie uses common [EventDispatcher](https://symfony.com/doc/current/components/event_dispatcher.html) that allows you to get into application cycle in few spots.

### Available Events

You can find all events as [standalone classes](https://pehapkari.cz/blog/2017/07/12/the-bulletproof-event-naming-for-symfony-event-dispatcher/) in [/src/Event](https://github.com/Symplify/Statie/blob/src/Event) directory:

- `BeforeRenderEvent` - is called after all GeneratorElement and Files and before they will be saved to file system

### How to Hook In?

Just create your subscriber

```php
namespace App\Statie\Twitter;

use Symfony\Component\EventDispatcher\SubscriberInterface;
use Symplify\Statie\Event\BeforeRenderEvent;

final class TweetNewPostsSubscriber implements SubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [BeforeRenderEvent::class => 'tweetNewPosts'];
    }

    public function tweetNewPosts(BeforeRenderEvent $beforeRenderEvent): void
    {
        // ...
    }
}
```

And register as service in `statie.yml`

```yml
# statie.yml
services:
   App\Statie\Twitter\TweetNewPostsSubscriber: ~
```

Your subscriber will be called right when `BeforeRenderEvent` occurs!
