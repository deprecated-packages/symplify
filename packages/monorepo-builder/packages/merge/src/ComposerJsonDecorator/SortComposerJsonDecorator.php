<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\ComposerJsonDecorator;

use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerJsonDecoratorInterface;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

/**
 * @see \Symplify\MonorepoBuilder\Merge\Tests\ComposerJsonDecorator\SortComposerJsonDecorator\SortComposerJsonDecoratorTest
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

        usort($orderedKeys, function ($key1, $key2): int {
            return array_search($key1, $this->sectionOrder, true) <=> array_search(
                $key2,
                $this->sectionOrder,
                true
            );
        });

        $composerJson->setOrderedKeys($orderedKeys);
    }
}
