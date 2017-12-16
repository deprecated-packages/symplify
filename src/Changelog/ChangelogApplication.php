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

        rsort($this->linksToAppend);

        // append new links to the file
        $this->content .= PHP_EOL . PHP_EOL . implode(PHP_EOL, $this->linksToAppend);
        file_put_contents($this->filePath, $this->content);
    }

    /**
     * @worker
     */
    public function completeDiffLinksToVersions(): void
    {
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
}
