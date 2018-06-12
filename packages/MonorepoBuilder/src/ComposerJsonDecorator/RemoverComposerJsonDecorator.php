<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\ComposerJsonDecorator;

use Symplify\MonorepoBuilder\Composer\Section;
use Symplify\MonorepoBuilder\Contract\ComposerJsonDecoratorInterface;

final class RemoverComposerJsonDecorator implements ComposerJsonDecoratorInterface
{
    /**
     * @var mixed[]
     */
    private $dataToRemove = [];

    /**
     * @param mixed[] $dataToRemove
     */
    public function __construct(array $dataToRemove)
    {
        $this->dataToRemove = $dataToRemove;
    }

    /**
     * @param mixed[] $composerJson
     * @return mixed[]
     */
    public function decorate(array $composerJson): array
    {
        foreach ($composerJson as $key => $values) {
            if (! isset($this->dataToRemove[$key])) {
                continue;
            }

            $composerJson = $this->processRequires($composerJson, $key);
            $composerJson = $this->processAutoloads($composerJson, $key);
        }

        return $composerJson;
    }

    /**
     * @param mixed[] $composerJson
     * @return mixed[]
     */
    private function processRequires(array $composerJson, string $key): array
    {
        if (! in_array($key, [Section::REQUIRE, Section::REQUIRE_DEV], true)) {
            return $composerJson;
        }

        foreach ($this->dataToRemove[$key] as $package => $version) {
            unset($composerJson[$key][$package]);
        }

        return $composerJson;
    }

    /**
     * @param mixed[] $composerJson
     * @return mixed[]
     */
    private function processAutoloads(array $composerJson, string $key): array
    {
        if (! in_array($key, [Section::AUTOLOAD, Section::AUTOLOAD_DEV], true)) {
            return $composerJson;
        }

        foreach ($this->dataToRemove[$key] as $type => $autoloadList) {
            if (! isset($composerJson[$key][$type])) {
                continue;
            }

            if (is_array($autoloadList)) {
                foreach ($autoloadList as $namespace => $path) {
                    unset($composerJson[$key][$type][$namespace]);
                }
            }
        }

        return $composerJson;
    }
}
