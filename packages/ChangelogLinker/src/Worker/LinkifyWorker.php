<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Configuration\Configuration;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;
use Symplify\ChangelogLinker\LinkAppender;

final class LinkifyWorker implements WorkerInterface
{
    /**
     * @var LinkAppender
     */
    private $linkAppender;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(LinkAppender $linkAppender, Configuration $configuration)
    {
        $this->linkAppender = $linkAppender;
        $this->configuration = $configuration;
    }

    public function processContent(string $content): string
    {
        foreach ($this->configuration->getNameToUrls() as $name => $url) {
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
