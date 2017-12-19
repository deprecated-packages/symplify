<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Tests\Naming;

use Symplify\GitWrapper\Naming\NameParser;
use Symplify\GitWrapper\Tests\AbstractContainerAwareTestCase;

final class NameParserTest extends AbstractContainerAwareTestCase
{
    /**
     * @var NameParser
     */
    private $nameParser;

    protected function setUp(): void
    {
        $this->nameParser = $this->container->get(NameParser::class);
    }

    /**
     * @dataProvider provideRepositoryUrls()
     */
    public function testParseRepositoryName(string $repositoryUrl, string $expectedName): void
    {
        $this->assertSame($expectedName, $this->nameParser->parseRepositoryName($repositoryUrl));
    }

    /**
     * @return string[][]
     */
    public function provideRepositoryUrls(): array
    {
        return [
            ['git@github.com:cpliakas/git-wrapper.git', 'git-wrapper'],
            ['https://github.com/cpliakas/git-wrapper.git', 'git-wrapper'],
        ];
    }
}
