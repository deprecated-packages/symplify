<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Github;

use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\Configuration\Configuration;
use Symplify\ChangelogLinker\Github\PullRequestMessageFactory;

final class PullRequestMessageFactoryTest extends TestCase
{
    /**
     * @var PullRequestMessageFactory
     */
    private $pullRequestMessageFactory;

    protected function setUp()
    {
        $configuration = new Configuration(['ego'], '', '', [], []);

        $this->pullRequestMessageFactory = new PullRequestMessageFactory($configuration);
    }

    public function test()
    {
        $pullRequest = [
            'number' => 10,
            'title' => 'Add cool feature',
            'user' => [
                'login' => 'me'
            ]
        ];

        $this->assertSame(
            '- [#10] Add cool feature, Thanks to @me',
            $this->pullRequestMessageFactory->createMessageFromPullRequest($pullRequest)
        );

        $pullRequest['user']['login'] = 'ego';

        $this->assertSame(
            '- [#10] Add cool feature',
            $this->pullRequestMessageFactory->createMessageFromPullRequest($pullRequest)
        );
    }
}
