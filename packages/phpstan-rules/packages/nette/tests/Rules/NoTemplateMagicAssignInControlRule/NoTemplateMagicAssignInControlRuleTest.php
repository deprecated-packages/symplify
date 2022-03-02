<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\NoTemplateMagicAssignInControlRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Nette\Rules\NoTemplateMagicAssignInControlRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoTemplateMagicAssignInControlRule>
 */
final class NoTemplateMagicAssignInControlRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/MagicTemplateAssign.php', [
            [NoTemplateMagicAssignInControlRule::ERROR_MESSAGE, 13],
        ]];

        yield [__DIR__ . '/Fixture/SkipPresenterTemplateAssign.php', []];
        yield [__DIR__ . '/Fixture/SkipControlApply.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoTemplateMagicAssignInControlRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
