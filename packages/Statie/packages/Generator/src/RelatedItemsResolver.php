<?php declare(strict_types=1);

namespace Symplify\Statie\Generator;

use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Generator\Configuration\GeneratorConfiguration;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Renderable\File\PostFile;

final class RelatedItemsResolver
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var GeneratorConfiguration
     */
    private $generatorConfiguration;

    public function __construct(Configuration $configuration, GeneratorConfiguration $generatorConfiguration)
    {
        $this->configuration = $configuration;
        $this->generatorConfiguration = $generatorConfiguration;
    }

    /**
     * @return PostFile[]
     */
    public function resolveForFile(AbstractFile $file): array
    {
        if (! $file->getRelatedItemsIds()) {
            return [];
        }

        $relatedPosts = [];
        foreach ($this->getItemsByFile($file) as $item) {
            if (in_array($item->getId(), $file->getRelatedItemsIds(), true)) {
                $relatedPosts[] = $item;
            }
        }

        return $relatedPosts;
    }

    /**
     * @return AbstractFile[]
     */
    private function getItemsByFile(AbstractFile $file): array
    {
        foreach ($this->generatorConfiguration->getGeneratorElements() as $generatorElement) {
            if (! is_a($file, $generatorElement->getObject(), true)) {
                continue;
            }

            return $this->configuration->getOption($generatorElement->getVariableGlobal());
        }

        return [];
    }
}
