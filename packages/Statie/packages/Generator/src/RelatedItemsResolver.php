<?php declare(strict_types=1);

namespace Symplify\Statie\Generator;

use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Generator\Configuration\GeneratorConfiguration;
use Symplify\Statie\Generator\Renderable\File\AbstractGeneratorFile;

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
     * @return AbstractGeneratorFile[]
     */
    public function resolveForFile(AbstractGeneratorFile $generatorFile): array
    {
        if (! $generatorFile->getRelatedItemsIds()) {
            return [];
        }

        $relatedGeneratorFiles = [];
        foreach ($this->getItemsByFile($generatorFile) as $item) {
            if (in_array($item->getId(), $generatorFile->getRelatedItemsIds(), true)) {
                $relatedGeneratorFiles[] = $item;
            }
        }

        return $relatedGeneratorFiles;
    }

    /**
     * @return AbstractGeneratorFile[]
     */
    private function getItemsByFile(AbstractGeneratorFile $generatorFile): array
    {
        foreach ($this->generatorConfiguration->getGeneratorElements() as $generatorElement) {
            if (! is_a($generatorFile, $generatorElement->getObject(), true)) {
                continue;
            }

            return $this->configuration->getOption($generatorElement->getVariableGlobal());
        }

        return [];
    }
}
