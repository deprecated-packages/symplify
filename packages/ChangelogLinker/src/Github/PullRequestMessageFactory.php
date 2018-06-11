<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Github;

use Symplify\ChangelogLinker\Configuration\Configuration;

final class PullRequestMessageFactory
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param mixed[] $pullRequest
     */
    public function createMessageFromPullRequest(array $pullRequest): string
    {
        $message = sprintf('- [#%s] %s', $pullRequest['number'], $pullRequest['title']);
        $author = $pullRequest['user']['login'];

        // skip the main maintainer to prevent self-thanking floods
        if (! in_array($author, $this->configuration->getAuthorsToIgnore(), true)) {
            $message .= ', Thanks to @' . $author;
        }

        return $message;
    }
}
