<?php declare(strict_types=1);

namespace Symplify\Statie\Twig;

use Iterator;
use Symplify\Statie\Generator\RelatedItemsResolver;
use Symplify\Statie\Generator\Renderable\File\AbstractGeneratorFile;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class RelatedItemsTwigExtension extends AbstractExtension
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
     * @return TwigFunction[]
     */
    public function getFunctions(): Iterator
    {
        // use in Twig: {% set relatedPosts = related_items(post) %}
        yield new TwigFunction('related_items', function (AbstractGeneratorFile $generatorFile): array {
            return $this->relatedItemsResolver->resolveForFile($generatorFile);
        });
    }
}
