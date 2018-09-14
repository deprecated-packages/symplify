<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split\Configuration;

use Nette\Utils\Strings;
use Symplify\MonorepoBuilder\Split\Exception\DirectoryNotFoundException;
use Symplify\MonorepoBuilder\Split\Exception\InvalidGitRepositoryException;
use Symplify\MonorepoBuilder\Split\Exception\InvalidRepositoryFormatException;
use function Safe\sprintf;

final class RepositoryGuard
{
    /**
     * @var string
     */
    private const GIT_REPOSITORY_PATTERN = '#((git|ssh|http(s)?)|(git@[\w\.]+)|[\w]+)(:(//)?)([\w\.@\:/\-~]+)(\.git)(/)?#';

    public function ensureIsRepository(string $possibleRepository): void
    {
        // local repositorye
        if ($possibleRepository === '.git') {
            return;
        }

        if (Strings::match($possibleRepository, self::GIT_REPOSITORY_PATTERN)) {
            return;
        }

        throw new InvalidRepositoryFormatException(sprintf('"%s" is not format for repository', $possibleRepository));
    }

    public function ensureIsRepositoryDirectory(string $repositoryDirectory): void
    {
        if (! file_exists($repositoryDirectory)) {
            throw new DirectoryNotFoundException(sprintf(
                'Directory for repository "%s" was not found',
                $repositoryDirectory
            ));
        }

        if (! file_exists($repositoryDirectory . '/.git')) {
            throw new InvalidGitRepositoryException(sprintf(
                '.git was not found in "%s" directory',
                $repositoryDirectory
            ));
        }
    }
}
