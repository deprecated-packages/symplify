<?php declare(strict_types=1);

namespace Symplify\Monorepo\Configuration;

use GitWrapper\GitWrapper;
use Nette\Utils\Strings;
use Symplify\Monorepo\Exception\InvalidRepositoryFormatException;

final class RepositoryGuard
{
    const GIT_REPOSITORY_PATTERN = '#((git|ssh|http(s)?)|(git@[\w\.]+))(:(//)?)([\w\.@\:/\-~]+)(\.git)(/)?#';
    /**
     * @var GitWrapper
     */
    private $gitWrapper;

    public function __construct(GitWrapper $gitWrapper)
    {
        $this->gitWrapper = $gitWrapper;
    }

    public function ensureIsRepository(string $possibleRepository): void
    {
        if (Strings::match($possibleRepository, self::GIT_REPOSITORY_PATTERN)) {
            return;
        }

        throw new InvalidRepositoryFormatException(sprintf(
            '"%s" is not format for repository',
            $possibleRepository
        ));
    }
}
