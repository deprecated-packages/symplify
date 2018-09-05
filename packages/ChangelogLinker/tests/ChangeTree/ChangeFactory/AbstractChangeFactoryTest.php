<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangeTree\ChangeFactory;

use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\ChangeTree\ChangeFactory;
use Symplify\ChangelogLinker\ChangeTree\Resolver\CategoryResolver;
use Symplify\ChangelogLinker\ChangeTree\Resolver\PackageResolver;
use Symplify\ChangelogLinker\Git\GitCommitDateTagResolver;

abstract class AbstractChangeFactoryTest extends TestCase
{
    /**
     * @var ChangeFactory
     */
    protected $changeFactory;

    /**
     * @var mixed[]
     */
    protected $pullRequest = [
        'number' => null,
        'title' => 'Blind title',
        'merge_commit_sha' => 'random',
    ];

    /**
     * @var ChangeFactory|null
     */
    private static $cachedChangeFactory;

    protected function setUp(): void
    {
        // this is needed, because every item in dataProviders resets $changeFactory property to null, dunno why
        if (self::$cachedChangeFactory) {
            $this->changeFactory = self::$cachedChangeFactory;
        } else {
            $this->changeFactory = new ChangeFactory(
                new GitCommitDateTagResolver(),
                new CategoryResolver(),
                new PackageResolver(['A' => 'Aliased']),
                ['ego']
            );

            self::$cachedChangeFactory = $this->changeFactory;
        }
    }
}
