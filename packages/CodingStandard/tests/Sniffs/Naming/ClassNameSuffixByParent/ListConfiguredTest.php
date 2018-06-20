<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Naming\ClassNameSuffixByParent;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Sniffs\Naming\ClassNameSuffixByParentSniff
 */
final class ListConfiguredTest extends AbstractCheckerTestCase
{
    public function testWrong(): void
    {
        $this->doTestWrongFile(__DIR__ . '/wrong/wrong5.php.inc');
    }

    public function testCorrect(): void
    {
        $this->doTestCorrectFile(__DIR__ . '/correct/correct5.php.inc');
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/list-configured-config.yml';
    }
}
