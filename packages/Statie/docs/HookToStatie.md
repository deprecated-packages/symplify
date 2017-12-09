## Hook To Statie

Statie use using common [EventDispatcher](https://symfony.com/doc/current/components/event_dispatcher.html) that allows you to get into application cycle in few spots.

### Available Events

You can find all events as [standalone classes](https://pehapkari.cz/blog/2017/07/12/the-bulletproof-event-naming-for-symfony-event-dispatcher/) in [/src/Event](/../src/Event) directory:

- `BeforeRenderEvent` - is called after all GeneratorElement and Files and before they will be saved to file system


### How to Hook In?

1. Just create your subscriber

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

2. And register as service in `statie.yml`

```yml
# statie.yml
services:
   App\Statie\Twitter\TweetNewPostsSubscriber: ~ 
```

3. Your subscriber will be called right when `BeforeRenderEvent` occurs!
