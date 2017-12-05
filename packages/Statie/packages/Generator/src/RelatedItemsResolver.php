<?php declare(strict_types=1);

namespace Symplify\Statie\Generator;

use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Renderable\File\PostFile;

final class RelatedItemsResolver
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return PostFile[]
     */
    public function resolveForFile(AbstractFile $file): array
    {
        if (! $file->getRelatedItemsIds()) {
            return [];
        }

        $relatedPosts = [];
        foreach ($this->getItems() as $post) {
            if (in_array($post->getId(), $file->getRelatedItemsIds(), true)) {
                $relatedPosts[] = $post;
            }
        }

        return $relatedPosts;
    }

    /**
     * @return PostFile[]
     */
    private function getItems(): array
    {
        // @todo resolve variable_global name by object,
        // get generator in here.
        return $this->configuration->getOption('posts');
    }
}
