<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangeTree\ChangeFactory;

use Symplify\ChangelogLinker\ChangeTree\ChangeFactory;
use Symplify\ChangelogLinker\HttpKernel\ChangelogLinkerKernel;
use Symplify\ChangelogLinker\ValueObject\ChangeTree\Change;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

abstract class AbstractChangeFactoryTest extends AbstractKernelTestCase
{
    /**
     * @var ChangeFactory
     */
    protected $changeFactory;

    /**
     * @var mixed[]
     */
    private $pullRequest = [
        'number' => null,
        'title' => 'Blind title',
        'merge_commit_sha' => 'random',
    ];

    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(ChangelogLinkerKernel::class, [__DIR__ . '/config/config.php']);
        $this->changeFactory = self::$container->get(ChangeFactory::class);
    }

    protected function createChangeForTitle(string $title): Change
    {
        $this->pullRequest['title'] = $title;
        return $this->changeFactory->createFromPullRequest($this->pullRequest);
    }
}
