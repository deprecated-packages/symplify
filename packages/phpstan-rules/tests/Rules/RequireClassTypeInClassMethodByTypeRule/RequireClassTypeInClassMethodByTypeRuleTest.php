<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireClassTypeInClassMethodByTypeRule;

use Iterator;
use PhpParser\Node;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\RequireClassTypeInClassMethodByTypeRule;

final class RequireClassTypeInClassMethodByTypeRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|string[]|int[]> $expectedErrorsWithLines
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipCorrectReturnRector.php', []];
        yield [__DIR__ . '/Fixture/SomeRector.php', []];
        yield [__DIR__ . '/Fixture/SkipInterface.php', []];

        $errorMessage = sprintf(
            RequireClassTypeInClassMethodByTypeRule::ERROR_MESSAGE,
            'getNodeTypes',
            Node::class
        );

        yield [__DIR__ . '/Fixture/IncorrectReturnRector.php', [[$errorMessage, 13]]];
        yield [__DIR__ . '/Fixture/IncorrectSingleReturnRector.php', [[$errorMessage, 12]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RequireClassTypeInClassMethodByTypeRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
