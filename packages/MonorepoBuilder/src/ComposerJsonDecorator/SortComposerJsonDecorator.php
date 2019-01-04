<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\ComposerJsonDecorator;

use Symplify\MonorepoBuilder\Contract\ComposerJsonDecoratorInterface;
use function Safe\uksort;

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

    /**
     * @param mixed[] $composerJson
     * @return mixed[]
     */
    public function decorate(array $composerJson): array
    {
        uksort($composerJson, function ($key1, $key2) {
            return array_search($key1, $this->sectionOrder, true) <=> array_search(
                $key2,
                $this->sectionOrder,
                true
            );
        });

        return $composerJson;
    }
}
