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

    public function processContent(string $content, string $repositoryLink): string
    {
        $linksToAppend = [];

        $matches = Strings::matchAll($content, '#\[' . RegexPattern::USER . '\]#');
        foreach ($matches as $match) {
            if (isset($this->linksToPrepend[$match['name']])) {
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
        return $content . implode(PHP_EOL, $linksToAppend) . PHP_EOL;
    }
}
