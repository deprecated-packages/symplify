<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;

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
     * @var WorkerInterface[]
     */
    private $workers = [];

    public function __construct(string $repositoryLink)
    {
        $this->repositoryLink = $repositoryLink;
    }

    public function addWorker(WorkerInterface $worker): void
    {
        $this->workers[] = $worker;
    }

    public function processFile(string $filePath): string
    {
        $this->filePath = $filePath;
        $this->content = file_get_contents($filePath);

        $this->resolveLinkedElements();

        foreach ($this->workers as $worker) {
            $this->content = $worker->processContent($this->content, $this->repositoryLink);
        }

        return $this->content;
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

    public function saveContent(): void
    {
        file_put_contents($this->filePath, $this->content);
    }

    private function resolveLinkedElements(): void
    {
        $matches = Strings::matchAll($this->content, self::LINKED_ID_PATTERN);
        foreach ($matches as $match) {
            $this->linkedIds[] = $match['id'];
        }
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
