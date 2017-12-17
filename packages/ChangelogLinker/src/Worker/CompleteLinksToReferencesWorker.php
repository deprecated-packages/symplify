<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

final class CompleteLinksToReferencesWorker implements WorkerInterface
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
     * @var string[]
     */
    private $linkedIds = [];

    public function processContent(string $content, string $repositoryLink): string
    {
        $this->resolveLinkedElements($content);

        $linksToAppend = [];

        $matches = Strings::matchAll($content, self::UNLINKED_ID_PATTERN);
        foreach ($matches as $match) {
            if (array_key_exists($match['id'], $linksToAppend)) {
                continue;
            }

            if (in_array($match['id'], $this->linkedIds, true)) {
                continue;
            }

            $possibleUrls = [
                $repositoryLink . '/pull/' . $match['id'],
                $repositoryLink . '/issues/' . $match['id'],
            ];

            foreach ($possibleUrls as $possibleUrl) {
                if ($this->doesUrlExist($possibleUrl)) {
                    $markdownLink = sprintf(
                        '[#%d]: %s',
                        $match['id'],
                        $possibleUrl
                    );

                    $linksToAppend[$match['id']] = $markdownLink;
                }
            }
        }

        dump($linksToAppend);
        die;
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

    private function resolveLinkedElements(string $content): void
    {
        $matches = Strings::matchAll($content, self::LINKED_ID_PATTERN);
        foreach ($matches as $match) {
            $this->linkedIds[] = $match['id'];
        }
    }
}
