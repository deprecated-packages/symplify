<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\ChangeTree;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\ChangeTree\Resolver\CategoryResolver;
use Symplify\ChangelogLinker\ChangeTree\Resolver\PackageResolver;
use Symplify\ChangelogLinker\Git\GitCommitDateTagResolver;
use Symplify\ChangelogLinker\ValueObject\ChangeTree\Change;
use Symplify\ChangelogLinker\ValueObject\Option;
use Symplify\ChangelogLinker\ValueObject\PackageName;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

/**
 * @see \Symplify\ChangelogLinker\Tests\ChangeTree\ChangeFactory\ChangeFactoryTest
 */
final class ChangeFactory
{
    /**
     * @var string
     * @see https://regex101.com/r/QPRx0q/1
     */
    private const ASTERISK_REGEX = '#(\*)#';

    /**
     * @var string
     */
    private const TITLE = 'title';

    /**
     * @var string[]
     */
    private $authorsToIgnore = [];

    /**
     * @var GitCommitDateTagResolver
     */
    private $gitCommitDateTagResolver;

    /**
     * @var CategoryResolver
     */
    private $categoryResolver;

    /**
     * @var PackageResolver
     */
    private $packageResolver;

    public function __construct(
        GitCommitDateTagResolver $gitCommitDateTagResolver,
        CategoryResolver $categoryResolver,
        PackageResolver $packageResolver,
        ParameterProvider $parameterProvider
    ) {
        $this->gitCommitDateTagResolver = $gitCommitDateTagResolver;
        $this->categoryResolver = $categoryResolver;
        $this->authorsToIgnore = $parameterProvider->provideArrayParameter(Option::AUTHORS_TO_IGNORE);
        $this->packageResolver = $packageResolver;
    }

    /**
     * @param mixed[] $pullRequest
     */
    public function createFromPullRequest(array $pullRequest): Change
    {
        $message = sprintf('- [#%s] %s', $pullRequest['number'], $this->escapeMarkdown($pullRequest[self::TITLE]));

        $author = $pullRequest['user']['login'] ?? '';

        // skip the main maintainer to prevent self-thanking floods
        if ($author && ! in_array($author, $this->authorsToIgnore, true)) {
            $message .= ', Thanks to @' . $author;
        }

        $category = $this->categoryResolver->resolveCategory($pullRequest[self::TITLE]);
        $package = $this->packageResolver->resolvePackage($pullRequest[self::TITLE]);
        $messageWithoutPackage = $this->resolveMessageWithoutPackage($message, $package);

        // @todo 'merge_commit_sha' || 'head'
        $pullRequestTag = $this->gitCommitDateTagResolver->resolveCommitToTag($pullRequest['merge_commit_sha']);

        return new Change($message, $category, $package, $messageWithoutPackage, $pullRequestTag);
    }

    private function escapeMarkdown(string $content): string
    {
        $content = trim($content);

        return Strings::replace($content, self::ASTERISK_REGEX, '\\\$1');
    }

    private function resolveMessageWithoutPackage(string $message, string $package): string
    {
        if ($package === PackageName::UNKNOWN) {
            return $message;
        }

        // can be aliased (not the $package variable), so we need to check any naming
        return Strings::replace($message, PackageResolver::PACKAGE_NAME_REGEX, '');
    }
}
