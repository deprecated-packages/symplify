<?php declare(strict_types=1);

namespace Symplify\Statie\Contract\Api;

interface ApiItemDecoratorInterface
{
    public function getName(): string;

    /**
     * @param mixed[] $items
     * @return mixed[]
     */
    public function decorate(array $items): array;
}
