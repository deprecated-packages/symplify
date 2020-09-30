<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\PrefferedStaticCallOverFuncCallRule;

use Iterator;
use Nette\Utils\Strings;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\PrefferedStaticCallOverFuncCallRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class PrefferedStaticCallOverFuncCallRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        $errorMessage = sprintf(
            PrefferedStaticCallOverFuncCallRule::ERROR_MESSAGE,
            Strings::class,
            'match',
            'preg_match'
        );
        yield [__DIR__ . '/Fixture/PregMatchCalled.php', [[$errorMessage, 11]]];
    }

    protected function getRule(): Rule
    {
        return new PrefferedStaticCallOverFuncCallRule([
            'preg_match' => [Strings::class, 'match'],
            'preg_matchAll' => [Strings::class, 'match'],
            'preg_replace' => [Strings::class, 'replace'],
            'preg_split' => [Strings::class, 'split'],
        ]);
    }
}
