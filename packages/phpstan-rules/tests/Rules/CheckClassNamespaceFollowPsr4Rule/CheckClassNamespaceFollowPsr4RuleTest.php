<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckClassNamespaceFollowPsr4Rule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\CheckClassNamespaceFollowPsr4Rule;

final class CheckClassNamespaceFollowPsr4RuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/ValidNamespace.php', []];
        yield [__DIR__ . '/Fixture/InvalidNamespace.php', [
            [
                sprintf(CheckClassNamespaceFollowPsr4Rule::ERROR_MESSAGE, 'Foo\Bar', 'Symplify\PHPStanRules\Tests\Rules\CheckClassNamespaceFollowPsr4Rule\Fixture'),
                7
            ]
        ]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            CheckClassNamespaceFollowPsr4Rule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
