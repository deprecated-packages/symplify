<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenMethodCallByTypeInLocationRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenMethodCallByTypeInLocationRule;
use Symplify\PHPStanRules\Tests\Rules\ForbiddenMethodCallByTypeInLocationRule\Fixture\View\Helper\NumberHelper;

final class ForbiddenMethodCallByTypeInLocationRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    /**
     * @return Iterator<mixed>
     */
    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipNotSpecified.php', []];
        yield [__DIR__ . '/Fixture/Controller/AlbumController.php', [
            [
                sprintf(
                    ForbiddenMethodCallByTypeInLocationRule::ERROR_MESSAGE,
                    NumberHelper::class,
                    'get',
                    'Controller'
                ),
                12,
            ],
        ]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenMethodCallByTypeInLocationRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
