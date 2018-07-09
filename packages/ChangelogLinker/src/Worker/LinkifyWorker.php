<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;
use Symplify\ChangelogLinker\LinkAppender;

final class LinkifyWorker implements WorkerInterface
{
    /**
     * @var LinkAppender
     */
    private $linkAppender;

    /**
     * @var string[]
     */
    private $namesToUrls = [];

    /**
     * @param string[] $namesToUrls
     */
    public function __construct(LinkAppender $linkAppender, array $namesToUrls)
    {
        $this->linkAppender = $linkAppender;
        $this->namesToUrls = $namesToUrls;
    }

    public function processContent(string $content): string
    {
        foreach ($this->namesToUrls as $name => $url) {
            if (! Strings::match($content, sprintf('#(%s)#', $name))) {
                continue;
            }

            $content = Strings::replace($content, sprintf('#(%s)#', $name), '[$1]');

            $link = sprintf('[%s]: %s', $name, $url);
            $this->linkAppender->add($name, $link);
        }

        return $content;
    }

    public function getPriority(): int
    {
        return 900;
    }
}
