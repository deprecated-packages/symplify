<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Latte\Filter;

use Symplify\Statie\Contract\Templating\FilterProviderInterface;
use Symplify\Statie\Generator\RelatedItemsResolver;
use Symplify\Statie\Renderable\File\AbstractFile;

final class RelatedItemsFilter implements FilterProviderInterface
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
            // use in *.latte like this:
            // {var $relatedPosts = ($post|relatedItems)}
            'relatedItems' => function (AbstractFile $file) {
                return $this->relatedItemsResolver->resolveForFile($file);
            },
        ];
    }
}
