<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Architecture\PreferredClassSniff;

use Symplify\CodingStandard\Sniffs\Architecture\PreferredClassSniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class PreferredClassSniffTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([
            __DIR__ . '/Fixture/wrong.php.inc',
            __DIR__ . '/Fixture/wrong2.php.inc',
            __DIR__ . '/Fixture/wrong3.php.inc',
        ]);
    }

    protected function getCheckerClass(): string
    {
        return PreferredClassSniff::class;
    }

    /**
     * @return mixed[]
     */
    protected function getCheckerConfiguration(): array
    {
        return [
            'oldToPreferredClasses' => [
                'Invalid\OldClass' => 'NewOne',
            ],
        ];
    }
}
