<?php declare(strict_types=1);

namespace Symplify\Statie\PostHeadlineLinker\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\Statie\Event\BeforeRenderEvent;
use Symplify\Statie\Generator\Configuration\GeneratorConfiguration;
use Symplify\Statie\Generator\Renderable\File\AbstractGeneratorFile;
use Symplify\Statie\PostHeadlineLinker\PostHeadlineLinker;

final class DecoratePostHeadlinesEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var PostHeadlineLinker
     */
    private $postHeadlineLinker;

    /**
     * @var GeneratorConfiguration
     */
    private $generatorConfiguration;

    public function __construct(PostHeadlineLinker $postHeadlineLinker, GeneratorConfiguration $generatorConfiguration)
    {
        $this->postHeadlineLinker = $postHeadlineLinker;
        $this->generatorConfiguration = $generatorConfiguration;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [BeforeRenderEvent::class => 'linkHeadlines'];
    }

    public function linkHeadlines(BeforeRenderEvent $beforeRenderEvent): void
    {
        $generatorFilesByType = $beforeRenderEvent->getGeneratorFilesByType();

        /** @var AbstractGeneratorFile[] $generatorFiles */
        foreach ($generatorFilesByType as $type => $generatorFiles) {
            $generatorElement = $this->generatorConfiguration->getGeneratorElementByVariableGlobal($type);
            if ($generatorElement === null) {
                // invalid $type value
                continue;
            }

            if ($generatorElement->hasLinkedHeadlines() === false) {
                continue;
            }

            foreach ($generatorFiles as $generatorFile) {
                $newContent = $this->postHeadlineLinker->processContent($generatorFile->getContent());
                $generatorFile->changeContent($newContent);
            }
        }
    }
}
