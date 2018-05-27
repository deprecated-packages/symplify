<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;
use Symplify\ChangelogLinker\LinkAppender;

final class LinkifyWorker implements WorkerInterface
{
    /**
     * @var string[]
     */
    private $nameToUrls = [];

    /**
     * @var LinkAppender
     */
    private $linkAppender;

    /**
     * @param string[] $nameToUrls
     */
    public function __construct(array $nameToUrls, LinkAppender $linkAppender)
    {
        $this->nameToUrls = $nameToUrls;
        $this->linkAppender = $linkAppender;
    }

    public function processContent(string $content): string
    {
        foreach ($this->nameToUrls as $name => $url) {
            $this->linkAppender->add($name, $url);
        }

        return $content;
    }

    public function getPriority(): int
    {
        return 900;
    }
}
