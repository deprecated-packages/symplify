<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable\ApiGenerator\Source;

use Symplify\Statie\Contract\Api\ApiItemDecoratorInterface;

final class BookApiItemDecorator implements ApiItemDecoratorInterface
{
    public function getName(): string
    {
        return 'books';
    }

    /**
     * @param mixed[] $items
     * @return mixed[]
     */
    public function decorate(array $items): array
    {
        $items['hello'] = 'world';

        return $items;
    }
}
