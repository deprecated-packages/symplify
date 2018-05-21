<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;
use Symplify\ChangelogLinker\Regex\RegexPattern;

final class ShortenReferencesWorker implements WorkerInterface
{
    public function processContent(string $content): string
    {
        $content = Strings::replace($content, '#\[' . RegexPattern::COMMIT . '\]#', function (array $match): string {
            return sprintf('[%s]', substr($match['commit'], 0, 6));
        });

        return $content;
    }
}
