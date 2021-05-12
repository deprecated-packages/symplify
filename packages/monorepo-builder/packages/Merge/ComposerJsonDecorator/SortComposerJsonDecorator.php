<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\ComposerJsonDecorator;

use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerJsonDecoratorInterface;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

/**
 * @see \Symplify\MonorepoBuilder\Tests\Merge\ComposerJsonDecorator\SortComposerJsonDecorator\SortComposerJsonDecoratorTest
 */
final class SortComposerJsonDecorator implements ComposerJsonDecoratorInterface
{
    /**
     * @var string[]
     */
    private $sectionOrder = [];

    public function __construct(ParameterProvider $parameterProvider)
    {
        $this->sectionOrder = $parameterProvider->provideArrayParameter(Option::SECTION_ORDER);
    }

    public function decorate(ComposerJson $composerJson): void
    {
        $orderedKeys = $composerJson->getOrderedKeys();

        usort($orderedKeys, function (string $key1, string $key2): int {
            return $this->findKeyPosition($key1) <=> $this->findKeyPosition($key2);
        });

        $composerJson->setOrderedKeys($orderedKeys);
    }

    /**
     * @return int|string|bool
     */
    private function findKeyPosition(string $key)
    {
        return array_search($key, $this->sectionOrder, true);
    }
}
