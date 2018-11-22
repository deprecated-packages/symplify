<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\ComposerJsonDecorator;

use Composer\Json\JsonManipulator;
use Symplify\MonorepoBuilder\Composer\Section;
use Symplify\MonorepoBuilder\Contract\ComposerJsonDecoratorInterface;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;

final class SortRequireComposerJsonDecorator implements ComposerJsonDecoratorInterface
{
    /**
     * @var PrivatesCaller
     */
    private $privatesCaller;

    public function __construct(PrivatesCaller $privatesCaller)
    {
        $this->privatesCaller = $privatesCaller;
    }

    /**
     * @param mixed[] $composerJson
     * @return mixed[]
     */
    public function decorate(array $composerJson): array
    {
        // no name of monorepo package => nothing to remove
        if (! isset($composerJson['config']['sort-packages'])) {
            return $composerJson;
        }

        foreach (array_keys($composerJson) as $key) {
            if (! in_array($key, [Section::REQUIRE, Section::REQUIRE_DEV], true)) {
                continue;
            }

            $composerJson[$key] = $this->sortPackages($composerJson[$key]);
        }

        return $composerJson;
    }

    /**
     * @param string[] $packages
     * @return string[]
     */
    private function sortPackages(array $packages): array
    {
        return $this->privatesCaller->callPrivateMethodWithReference(
            JsonManipulator::class,
            'sortPackages',
            $packages
        );
    }
}
