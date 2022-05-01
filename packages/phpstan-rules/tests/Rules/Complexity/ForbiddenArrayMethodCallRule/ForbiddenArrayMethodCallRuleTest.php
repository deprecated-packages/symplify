<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenArrayMethodCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\Complexity\ForbiddenArrayMethodCallRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<ForbiddenArrayMethodCallRule>
 */
final class ForbiddenArrayMethodCallRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipNormalArray.php', []];
        yield [__DIR__ . '/Fixture/ReturnCallable.php', [[ForbiddenArrayMethodCallRule::ERROR_MESSAGE, 11]]];
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(ForbiddenArrayMethodCallRule::class);
    }
}
