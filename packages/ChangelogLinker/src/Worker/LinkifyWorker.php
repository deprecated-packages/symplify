<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;
use Symplify\ChangelogLinker\LinkAppender;
use function Safe\sprintf;

final class LinkifyWorker implements WorkerInterface
{
    /**
     * @var string[]
     */
    private $namesToUrls = [];

    /**
     * @var LinkAppender
     */
    private $linkAppender;

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
            // https://regex101.com/r/4C9MwZ/3
            $pattern = '#([^-\[]\b)(' . preg_quote($name) . ')(\b[^-\]])#';
            if (! Strings::match($content, $pattern)) {
                continue;
            }

            $content = Strings::replace($content, $pattern, '$1[$2]$3');

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
