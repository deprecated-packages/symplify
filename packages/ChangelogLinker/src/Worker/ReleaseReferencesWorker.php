<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;
use Symplify\ChangelogLinker\Regex\RegexPattern;

final class ReleaseReferencesWorker implements WorkerInterface
{

    /**
     * @var resource
     */
    private $curl;

    public function __construct()
    {
        $this->curl = $this->createCurl();
    }

    public function processContent(string $content, string $repositoryLink): string
    {
        $unreleasedMatch = Strings::match($content, '#\#\#\s+Unreleased#');

        if (empty($unreleasedMatch)) {
            return $content;
        }

        // get last tagged version
        $lastTag = exec('git describe --tags');
        // no tag
        if (empty($lastTag)) {
            return $content;
        }

        // get tagged version date
        $lastTagDate = exec('git log -1 --format=%ai ' . $lastTag);
        $lastTagDateTime = DateTime::from($lastTagDate);

        return Strings::replace($content, '#\#\#\s+Unreleased#', function (array $match) use ($lastTag, $lastTagDateTime) {
            return '## ' . $lastTag . ' - ' . $lastTagDateTime->format('Y-m-d');
        });
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
}
