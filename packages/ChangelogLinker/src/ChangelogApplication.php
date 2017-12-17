<?php declare(strict_types=1);

namespace Symplify\Changelog;

use Nette\Utils\Strings;

final class ChangelogApplication
{
    /**
     * @var string
     */
    private const ID_PATTERN = '\#(?<id>[0-9]+)';

    /**
     * @var string
     */
    private const UNLINKED_ID_PATTERN = '#\[' . self::ID_PATTERN . '\]\s+#';

    /**
     * @var string
     */
    private const LINKED_ID_PATTERN = '#\[' . self::ID_PATTERN . '\]:\s+#';

    /**
     * @var string
     */
    private $content;

    /**
     * @var int[]
     */
    private $linkedIds = [];

    /**
     * @var string
     */
    private $filePath;

    /**
     * @var string
     */
    private $repositoryLink;

    /**
     * @var string[]
     */
    private $linksToAppend = [];

    /**
     * @var string[]
     */
    private $versionIds = [];

    /**
     * @var string[]
     */
    private $linkedVersionIds = [];

    /**
     * @var string[]
     */
    private $unwrappedReferences = [];

    public function __construct(string $repositoryLink = 'https://github.com/Symplify/Symplify')
    {
        $this->repositoryLink = $repositoryLink;
    }

    public function loadFile(string $filePath): void
    {
        $this->filePath = $filePath;
        $this->content = file_get_contents($filePath);
        $this->resolveLinkedElements();
    }

    private function resolveLinkedElements(): void
    {
        $matches = Strings::matchAll($this->content, self::LINKED_ID_PATTERN);
        foreach ($matches as $match) {
            $this->linkedIds[] = $match['id'];
        }

        $matches = Strings::matchAll($this->content, '#\[(?<versionId>(v|[0-9])[a-zA-Z0-9\.-]+)\]: #');
        foreach ($matches as $match) {
            $this->linkedVersionIds[] = $match['versionId'];
        }
    }

    /**
     * Comletes [] around commit, pull-request, issues and version references
     * @worker
     */
    public function completeBracketsAroundReferences(): void
    {
        // issue or PR references
        $this->content = Strings::replace($this->content, '# (?<reference>\#(v|[0-9])[a-zA-Z0-9\.-]+) #', function (array $match): string {
            return sprintf(' [%s] ', $match['reference']);
        });

        // version references
        $this->content = Strings::replace($this->content, '#\#\# (?<versionId>(v|[0-9])[a-zA-Z0-9\.-]+)#', function (array $match): string {
            return sprintf('## [%s]', $match['versionId']);
        });

        $this->saveContent();
    }

    /**
     * @worker
     */
    public function completeLinksToIds(): void
    {
        $matches = Strings::matchAll($this->content, self::UNLINKED_ID_PATTERN);
        foreach ($matches as $match) {
            if (array_key_exists($match['id'], $this->linksToAppend)) {
                continue;
            }

            if (in_array($match['id'], $this->linkedIds, true)) {
                continue;
            }

            $possibleUrls = [
                $this->repositoryLink . '/pull/' . $match['id'],
                $this->repositoryLink . '/issues/' . $match['id'],
            ];

            foreach ($possibleUrls as $possibleUrl) {
                if ($this->doesUrlExist($possibleUrl)) {
                    $markdownLink = sprintf(
                        '[#%d]: %s',
                        $match['id'],
                        $possibleUrl
                    );

                    $this->linksToAppend[$match['id']] = $markdownLink;
                }
            }
        }
    }

    /**
     * @worker
     */
    public function completeDiffLinksToVersions(): void
    {
        $matches = Strings::matchAll($this->content, '#\#\# \[(?<versionId>(v|[0-9])[a-zA-Z0-9\.-]+)\]#');
        foreach ($matches as $match) {
            $this->versionIds[] = $match['versionId'];
        }

        foreach ($this->versionIds as $index => $versionId) {
            if (in_array($versionId, $this->linkedVersionIds, true)) {
                continue;
            }

            // last version, no previous one
            if (! isset($this->versionIds[$index + 1])) {
                continue;
            }

            $this->linksToAppend[] = sprintf(
                '[%s]: %s/compare/%s...%s',
                $versionId,
                $this->repositoryLink,
                $this->versionIds[$index + 1],
                $versionId
            );
        }
    }

    public function appendLinks(): void
    {
        if (! count($this->linksToAppend)) {
            return;
        }

        rsort($this->linksToAppend);

        // append new links to the file
        $this->content .= PHP_EOL . implode(PHP_EOL, $this->linksToAppend);
        $this->saveContent();
    }

    private function doesUrlExist(string $url): bool
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true); // set to HEAD request
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // don't output the response
        curl_exec($ch);
        $doesUrlExist = curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200;
        curl_close($ch);

        return $doesUrlExist;
    }

    private function saveContent(): void
    {
        file_put_contents($this->filePath, $this->content);
    }
}
