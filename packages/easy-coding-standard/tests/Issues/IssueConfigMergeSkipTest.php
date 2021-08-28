<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Issues;

use Iterator;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractCheckerTestCase;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\SmartFileSystem\SmartFileInfo;

final class IssueConfigMergeSkipTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fileInfo): void
    {
        $this->doTestFileInfo($fileInfo);

        $parameterBag = $this->container->getParameterBag();
        $skip = $parameterBag->get(Option::SKIP);

        $this->assertEquals(
            [
                'PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\AssignmentInConditionSniff.FoundInWhileCondition' => null,
                'PhpCsFixer\Fixer\ReturnNotation\ReturnAssignmentFixer',
            ],
            $skip
        );
    }

    /**
     * @return Iterator<SmartFileInfo[]>
     */
    public function provideData(): Iterator
    {
        yield [new SmartFileInfo(__DIR__ . '/Fixture/fixture_config_skip_merge.php.inc')];
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/config_merge_skip.php';
    }
}
