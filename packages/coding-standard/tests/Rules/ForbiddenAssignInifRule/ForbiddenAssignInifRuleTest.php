<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenAssignInifRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\ForbiddenAssignInifRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class ForbiddenAssignInifRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/AssignBeforeIf.php', []];
        yield [__DIR__ . '/Fixture/AssignAfterIf.php', []];
        yield [__DIR__ . '/Fixture/AssignInsideIf.php', [[ForbiddenAssignInifRule::ERROR_MESSAGE, 12]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenAssignInifRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
