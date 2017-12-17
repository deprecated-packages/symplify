<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;

final class ShortenReferencesWorker implements WorkerInterface
{
    public function processContent(string $content, string $repositoryLink): string
    {
        $content = Strings::replace($content, '#\[(?<commit>[0-9a-z]{40})\]#', function (array $match) {
            return sprintf('[%s]', substr($match['commit'],  0, 6));
        });

        return $content;
    }
}
