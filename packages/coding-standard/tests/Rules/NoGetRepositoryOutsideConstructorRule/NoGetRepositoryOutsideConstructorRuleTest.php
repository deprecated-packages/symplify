<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoGetRepositoryOutsideConstructorRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\NoGetRepositoryOutsideConstructorRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class NoGetRepositoryOutsideConstructorRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [
            __DIR__ . '/Fixture/OneTestRepository.php',
            [[NoGetRepositoryOutsideConstructorRule::ERROR_MESSAGE, 25]],
        ];
        yield [__DIR__ . '/Fixture/TwoTestRepository.php', []];

        yield [__DIR__ . '/Fixture/SkipTestCase.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoGetRepositoryOutsideConstructorRule();
    }
}
