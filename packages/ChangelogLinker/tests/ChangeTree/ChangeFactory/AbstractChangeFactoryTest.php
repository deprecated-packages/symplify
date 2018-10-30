<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangeTree\ChangeFactory;

use Symplify\ChangelogLinker\ChangeTree\Change;
use Symplify\ChangelogLinker\ChangeTree\ChangeFactory;
use Symplify\ChangelogLinker\Tests\AbstractConfigAwareContainerTestCase;

abstract class AbstractChangeFactoryTest extends AbstractConfigAwareContainerTestCase
{
    /**
     * @var mixed[]
     */
    protected $pullRequest = [
        'number' => null,
        'title' => 'Blind title',
        'merge_commit_sha' => 'random',
    ];

    /**
     * @var ChangeFactory
     */
    protected $changeFactory;

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
            $this->changeFactory = $this->container->get(ChangeFactory::class);
            self::$cachedChangeFactory = $this->changeFactory;
        }
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/config.yml';
    }

    protected function createChangeForTitle(string $title): Change
    {
        $this->pullRequest['title'] = $title;

        return $this->changeFactory->createFromPullRequest($this->pullRequest);
    }
}
