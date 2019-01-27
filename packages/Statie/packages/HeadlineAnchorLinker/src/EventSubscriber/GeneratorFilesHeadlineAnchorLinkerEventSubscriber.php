<?php declare(strict_types=1);

namespace Symplify\Statie\HeadlineAnchorLinker\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\Statie\Event\BeforeRenderEvent;
use Symplify\Statie\Generator\Configuration\GeneratorConfiguration;
use Symplify\Statie\Generator\Renderable\File\AbstractGeneratorFile;
use Symplify\Statie\HeadlineAnchorLinker\HeadlineAnchorLinker;

final class GeneratorFilesHeadlineAnchorLinkerEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var HeadlineAnchorLinker
     */
    private $headlineAnchorLinker;

    /**
     * @var GeneratorConfiguration
     */
    private $generatorConfiguration;

    public function __construct(
        HeadlineAnchorLinker $headlineAnchorLinker,
        GeneratorConfiguration $generatorConfiguration
    ) {
        $this->headlineAnchorLinker = $headlineAnchorLinker;
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

            if ($generatorElement->hasHeadlineAnchors() === false) {
                continue;
            }

            foreach ($generatorFiles as $generatorFile) {
                $newContent = $this->headlineAnchorLinker->processContent($generatorFile->getContent());
                $generatorFile->changeContent($newContent);
            }
        }
    }
}
