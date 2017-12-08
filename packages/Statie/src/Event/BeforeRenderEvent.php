<?php declare(strict_types=1);

namespace Symplify\Statie\Event;

use Symfony\Component\EventDispatcher\Event;
use Symplify\Statie\Renderable\File\AbstractFile;

final class BeforeRenderEvent extends Event
{
    /**
     * @var AbstractFile[]
     */
    private $objectsToRender = [];

    /**
     * @param AbstractFile[] $objectsToRender
     */
    public function __construct(array $objectsToRender)
    {
        $this->objectsToRender = $objectsToRender;
    }

    /**
     * @return AbstractFile[]
     */
    public function getObjectsToRender(): array
    {
        return $this->objectsToRender;
    }
}
