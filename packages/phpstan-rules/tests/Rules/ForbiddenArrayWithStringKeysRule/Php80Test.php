<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenArrayWithStringKeysRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenArrayWithStringKeysRule;

final class Php80Test extends AbstractServiceAwareRuleTestCase
{
    /**
     * @requires PHP 8.0
     * @param array<int|string> $expectedErrorMessagesWithLines
     * @dataProvider provideData()
     */
    public function test(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/FixturePhp80/SkipAttributeArrayKey.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenArrayWithStringKeysRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
