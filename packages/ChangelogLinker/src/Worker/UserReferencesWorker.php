<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;
use Symplify\ChangelogLinker\LinkAppender;
use Symplify\ChangelogLinker\Regex\RegexPattern;

/**
 * Completes link to @user mentions
 */
final class UserReferencesWorker implements WorkerInterface
{
    /**
     * @var string[]
     */
    private $linkedUsers = [];

    /**
     * @var LinkAppender
     */
    private $linkAppender;

    public function __construct(LinkAppender $linkAppender)
    {
        $this->linkAppender = $linkAppender;
    }

    public function processContent(string $content): string
    {
        $this->collectLinkedUsers($content);

        $matches = Strings::matchAll($content, '#\[' . RegexPattern::USER . '\]#');
        foreach ($matches as $match) {
            if ($this->shouldSkip($match)) {
                continue;
            }

            $markdownUserLink = sprintf('[@%s]: https://github.com/%s', $match['name'], $match['name']);

            $this->linkAppender->add('@' . $match['name'], $markdownUserLink);
        }

        return $content;
    }

    public function getPriority(): int
    {
        return 500;
    }

    private function collectLinkedUsers(string $content): void
    {
        $matches = Strings::matchAll($content, '#\[' . RegexPattern::USER . '\]: #');
        foreach ($matches as $match) {
            $this->linkedUsers[] = $match['name'];
        }
    }

    /**
     * @param mixed[] $match
     */
    private function shouldSkip(array $match): bool
    {
        if (in_array($match['name'], $this->linkedUsers, true)) {
            return true;
        }

        return $this->linkAppender->hasId($match['name']);
    }
}
