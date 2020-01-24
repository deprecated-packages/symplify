<?php

declare(strict_types=1);

namespace Symplify\Statie\HeadlineAnchorLinker\Renderable;

use Symplify\Statie\Contract\Renderable\FileDecoratorInterface;
use Symplify\Statie\Generator\Configuration\GeneratorElement;
use Symplify\Statie\Generator\Renderable\File\AbstractGeneratorFile;
use Symplify\Statie\HeadlineAnchorLinker\HeadlineAnchorLinker;
use Symplify\Statie\Renderable\File\AbstractFile;

final class HeadlineAnchorFileDecorator implements FileDecoratorInterface
{
    /**
     * @var HeadlineAnchorLinker
     */
    private $headlineAnchorLinker;

    public function __construct(HeadlineAnchorLinker $headlineAnchorLinker)
    {
        $this->headlineAnchorLinker = $headlineAnchorLinker;
    }

    /**
     * @param AbstractFile[]|AbstractGeneratorFile[] $files
     * @return AbstractFile[]|AbstractGeneratorFile[]
     */
    public function decorateFiles(array $files): array
    {
        return $files;
    }

    /**
     * @param AbstractFile[]|AbstractGeneratorFile[] $files
     * @return AbstractFile[]|AbstractGeneratorFile[]
     */
    public function decorateFilesWithGeneratorElement(array $files, GeneratorElement $generatorElement): array
    {
        if (! $generatorElement->hasHeadlineAnchors()) {
            return $files;
        }

        foreach ($files as $generatorFile) {
            $newContent = $this->headlineAnchorLinker->processContent($generatorFile->getContent());
            $generatorFile->changeContent($newContent);
        }

        return $files;
    }

    /**
     * Higher priorities are executed first.
     */
    public function getPriority(): int
    {
        return 500;
    }
}
