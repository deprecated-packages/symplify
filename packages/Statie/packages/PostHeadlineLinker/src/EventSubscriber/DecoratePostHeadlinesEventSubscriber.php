<?php declare(strict_types=1);

namespace Symplify\Statie\PostHeadlineLinker\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\Statie\Event\BeforeRenderEvent;
use Symplify\Statie\PostHeadlineLinker\PostHeadlineLinker;
use Symplify\Statie\Renderable\File\PostFile;

final class DecoratePostHeadlinesEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var bool
     */
    private $postHeadlineLinkerEnabled = false;

    /**
     * @var PostHeadlineLinker
     */
    private $postHeadlineLinker;

    public function __construct(PostHeadlineLinker $postHeadlineLinker, bool $postHeadlineLinkerEnabled)
    {
        $this->postHeadlineLinker = $postHeadlineLinker;
        $this->postHeadlineLinkerEnabled = $postHeadlineLinkerEnabled;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [BeforeRenderEvent::class => 'decoratePostHeadlines'];
    }

    public function decoratePostHeadlines(BeforeRenderEvent $beforeRenderEvent): void
    {
        if ($this->postHeadlineLinkerEnabled === false) {
            return;
        }

        $generatorFilesByType = $beforeRenderEvent->getGeneratorFilesByType();

        /** @var PostFile[] $postFiles */
        $postFiles = $generatorFilesByType['posts'];

        foreach ($postFiles as $postFile) {
            $newContent = $this->postHeadlineLinker->processContent($postFile->getContent());

            $postFile->changeContent($newContent);
        }
    }
}
