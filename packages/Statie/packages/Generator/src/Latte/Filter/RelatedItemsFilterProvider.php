<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Latte\Filter;

use Symplify\Statie\Contract\Templating\FilterProviderInterface;
use Symplify\Statie\Generator\RelatedItemsResolver;
use Symplify\Statie\Generator\Renderable\File\AbstractGeneratorFile;

final class RelatedItemsFilterProvider implements FilterProviderInterface
{
    /**
     * @var RelatedItemsResolver
     */
    private $relatedItemsResolver;

    public function __construct(RelatedItemsResolver $relatedItemsResolver)
    {
        $this->relatedItemsResolver = $relatedItemsResolver;
    }

    /**
     * @return callable[]
     */
    public function provide(): array
    {
        return [
            // use in Twig: {% set relatedPosts = related_items(post) %}
            // use in Latte: {var $relatedPosts = ($post|related_items)}
            'related_items' => function (AbstractGeneratorFile $generatorFile): array {
                return $this->relatedItemsResolver->resolveForFile($generatorFile);
            },

            // for BC
            'relatedItems' => function (AbstractGeneratorFile $generatorFile): array {
                return $this->relatedItemsResolver->resolveForFile($generatorFile);
            },
        ];
    }
}
