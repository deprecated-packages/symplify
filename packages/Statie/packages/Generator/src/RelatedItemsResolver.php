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
        foreach ($this->getPosts() as $post) {
            if (in_array($post->getId(), $file->getRelatedItemsIds(), true)) {
                $relatedPosts[] = $post;
            }
        }

        return $relatedPosts;
    }

    /**
     * @return PostFile[]
     */
    private function getPosts(): array
    {
        return $this->configuration->getOptions()['posts'];
    }
}
