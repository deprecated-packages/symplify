<?php declare(strict_types=1);

namespace Symplify\Statie\Generator;

use Symplify\Statie\Generator\Configuration\GeneratorConfiguration;
use Symplify\Statie\Generator\Configuration\GeneratorElement;

final class Generator
{
    /**
     * @var GeneratorConfiguration
     */
    private $generatorConfiguration;

    public function __construct(GeneratorConfiguration $generatorConfiguration)
    {
        $this->generatorConfiguration = $generatorConfiguration;
    }

    public function run(): void
    {
        foreach ($this->generatorConfiguration->getGeneratorElements() as $generatorElement) {
            $this->processGeneratorElement($generatorElement);
        }
    }

    private function processGeneratorElement(GeneratorElement $generatorElement): void
    {
        dump($generatorElement);
        die;

        // find files in...
        // process object
        // save them to property
        // render them
    }
}
