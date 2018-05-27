<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Nette\Utils\Strings;
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
