<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoEmptyClassRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoEmptyClassRule;

final class NoEmptyClassRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipMarkerInterface.php', []];
        yield [__DIR__ . '/Fixture/SkipException.php', []];
        yield [__DIR__ . '/Fixture/SkipWithCommentInterface.php', []];
        yield [__DIR__ . '/Fixture/SkipWithContent.php', []];
        yield [__DIR__ . '/Fixture/SkipWithCommentAbove.php', []];
        yield [__DIR__ . '/Fixture/SkipFinalChildOfAbstract.php', []];
        yield [__DIR__ . '/Fixture/SkipEmptyClassWithImplements.php', []];

        yield [__DIR__ . '/Fixture/SomeEmptyClass.php', [[NoEmptyClassRule::ERROR_MESSAGE, 7]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(NoEmptyClassRule::class, __DIR__ . '/../../../config/symplify-rules.neon');
    }
}
