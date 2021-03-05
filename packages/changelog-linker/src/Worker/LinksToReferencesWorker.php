<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;
use Symplify\ChangelogLinker\LinkAppender;
use Symplify\ChangelogLinker\ValueObject\Option;
use Symplify\ChangelogLinker\ValueObject\RegexPattern;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

final class LinksToReferencesWorker implements WorkerInterface
{
    /**
     * @var string
     */
    private const ID = 'id';

    /**
     * @var string
     */
    private $repositoryUrl;

    /**
     * @var LinkAppender
     */
    private $linkAppender;

    public function __construct(ParameterProvider $parameterProvider, LinkAppender $linkAppender)
    {
        $this->linkAppender = $linkAppender;
        $this->repositoryUrl = $parameterProvider->provideStringParameter(Option::REPOSITORY_URL);
    }

    /**
     * Github can redirects PRs to issues, so no need to trouble with their separatoin
     *
     * @inspiration for Regex: https://stackoverflow.com/a/406408/1348344
     */
    public function processContent(string $content): string
    {
        $matches = Strings::matchAll($content, '#\[' . RegexPattern::PR_OR_ISSUE . '\]#m');
        foreach ($matches as $match) {
            $link = sprintf('[#%d]: %s/pull/%d', $match[self::ID], $this->repositoryUrl, $match[self::ID]);
            $this->linkAppender->add($match[self::ID], $link);
        }

        return $content;
    }

    public function getPriority(): int
    {
        return 700;
    }
}
