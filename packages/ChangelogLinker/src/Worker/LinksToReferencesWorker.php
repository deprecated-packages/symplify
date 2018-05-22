<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;
use Symplify\ChangelogLinker\Regex\RegexPattern;

final class LinksToReferencesWorker implements WorkerInterface
{
    /**
     * @var string[]
     */
    private $linkedIds = [];

    /**
     * @var resource
     */
    private $curl;

    /**
     * @var string
     */
    private $repositoryLink;

    public function __construct(string $repositoryLink)
    {
        $this->curl = $this->createCurl();
        $this->repositoryLink = $repositoryLink;
    }

    public function processContent(string $content): string
    {
        $this->resolveLinkedElements($content);

        $linksToAppend = $this->processPullRequestAndIssueReferences($content);
        $linksToAppend = array_merge($linksToAppend, $this->processCommitReferences($content));

        if (! count($linksToAppend)) {
            return $content;
        }

        rsort($linksToAppend);

        // append new links to the file
        return $content . PHP_EOL . implode(PHP_EOL, $linksToAppend);
    }

    public function getPriority(): int
    {
        return 700;
    }

    /**
     * @return string[]
     */
    private function processPullRequestAndIssueReferences(string $content): array
    {
        $linksToAppend = [];

        $matches = Strings::matchAll($content, '#\[' . RegexPattern::PR_OR_ISSUE . '\][\s,]#');
        foreach ($matches as $match) {
            if ($this->shouldSkipPullRequestOrIssueReference($match, $linksToAppend)) {
                continue;
            }

            $possibleUrls = [
                $this->repositoryLink . '/pull/' . $match['id'],
                $this->repositoryLink . '/issues/' . $match['id'],
            ];

            foreach ($possibleUrls as $possibleUrl) {
                if ($this->doesUrlExist($possibleUrl)) {
                    $markdownLink = sprintf('[#%d]: %s', $match['id'], $possibleUrl);

                    $linksToAppend[$match['id']] = $markdownLink;
                    break;
                }
            }
        }

        return $linksToAppend;
    }

    /**
     * @return string[]
     */
    private function processCommitReferences(string $content): array
    {
        $linksToAppend = [];

        $matches = Strings::matchAll($content, '# \[' . RegexPattern::COMMIT . '\] #');
        foreach ($matches as $match) {
            $markdownLink = sprintf('[%s]: %s/commit/%s', $match['commit'], $this->repositoryLink, $match['commit']);

            $linksToAppend[$match['commit']] = $markdownLink;
        }

        return $linksToAppend;
    }

    private function doesUrlExist(string $url): bool
    {
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_exec($this->curl);

        return curl_getinfo($this->curl, CURLINFO_HTTP_CODE) === 200;
    }

    private function resolveLinkedElements(string $content): void
    {
        $matches = Strings::matchAll($content, '#\[' . RegexPattern::PR_OR_ISSUE . '\]:\s+#');
        foreach ($matches as $match) {
            $this->linkedIds[] = $match['id'];
        }
    }

    /**
     * @return resource
     */
    private function createCurl()
    {
        $curl = curl_init();

        // set to HEAD request
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        // don't output the response
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        return $curl;
    }

    /**
     * @param string[] $match
     * @param string[] $linksToAppend
     */
    private function shouldSkipPullRequestOrIssueReference(array $match, array $linksToAppend): bool
    {
        if (array_key_exists($match['id'], $linksToAppend)) {
            return true;
        }

        if (in_array($match['id'], $this->linkedIds, true)) {
            return true;
        }

        return false;
    }
}
