<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;

/**
 * Completes link to @user mentions
 */
final class UserReferencesWorker implements WorkerInterface
{
    public function processContent(string $content, string $repositoryLink): string
    {
        dump($content);
        die;
        // @todo
    }
}
