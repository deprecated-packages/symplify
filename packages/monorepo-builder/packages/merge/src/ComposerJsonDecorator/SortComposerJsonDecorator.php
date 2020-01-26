<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\ComposerJsonDecorator;

use Symplify\MonorepoBuilder\ComposerJsonObject\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerJsonDecoratorInterface;

final class SortComposerJsonDecorator implements ComposerJsonDecoratorInterface
{
    /**
     * @var string[]
     */
    private $sectionOrder = [];

    /**
     * @param string[] $sectionOrder
     */
    public function __construct(array $sectionOrder)
    {
        $this->sectionOrder = $sectionOrder;
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
