<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;
use Symplify\ChangelogLinker\Regex\RegexPattern;

/**
 * Completes link to @user mentions
 */
final class UserReferencesWorker implements WorkerInterface
{
    /**
     * @var string[]
     */
    private $linksToPrepend = [];

    /**
     * @var string[]
     */
    private $linkedUsers = [];

    public function processContent(string $content, string $repositoryLink): string
    {
        $this->collectLinkedUsers($content);

        $linksToAppend = [];

        $matches = Strings::matchAll($content, '#\[' . RegexPattern::USER . '\]#');
        foreach ($matches as $match) {
            if ($this->shouldSkip($match)) {
                continue;
            }

            $markdownUserLink = sprintf(
                '[@%s]: https://github.com/%s',
                $match['name'],
                $match['name']
            );

            $linksToAppend[$match['name']] = $markdownUserLink;
        }

        if (! count($linksToAppend)) {
            return $content;
        }

        rsort($linksToAppend);

        // append new links to the file
        return $content . PHP_EOL . implode(PHP_EOL, $linksToAppend);
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

        if (isset($this->linksToPrepend[$match['name']])) {
            return true;
        }

        return false;
    }
}
