<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;
use Symplify\ChangelogLinker\LinkAppender;
use Symplify\ChangelogLinker\ValueObject\RegexPattern;

/**
 * Completes link to @user mentions
 */
final class UserReferencesWorker implements WorkerInterface
{
    /**
     * @var string
     */
    private const REFERENCE = 'reference';

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
        $matches = Strings::matchAll($content, '#\[' . RegexPattern::USER_REGEX . '\]#');
        foreach ($matches as $match) {
            if ($this->shouldSkip($match)) {
                continue;
            }

            $markdownUserLink = sprintf(
                '[%s]: https://github.com/%s',
                $match[self::REFERENCE],
                ltrim($match[self::REFERENCE], '@')
            );

            $this->linkAppender->add($match[self::REFERENCE], $markdownUserLink);
        }

        return $content;
    }

    public function getPriority(): int
    {
        return 500;
    }

    /**
     * @param mixed[] $match
     */
    private function shouldSkip(array $match): bool
    {
        return $this->linkAppender->hasId($match[self::REFERENCE]);
    }
}
