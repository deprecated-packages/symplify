<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Domain\ForbiddenAlwaysSetterCallRule;

use Iterator;
use PHPStan\Collectors\Collector;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Collector\ClassMethod\FormTypeClassCollector;
use Symplify\PHPStanRules\Collector\ClassMethod\NewAndSetterCallsCollector;
use Symplify\PHPStanRules\Rules\Domain\ForbiddenAlwaysSetterCallRule;
use Symplify\PHPStanRules\Tests\Rules\Domain\ForbiddenAlwaysSetterCallRule\Source\FirstClassIdea;

/**
 * @extends RuleTestCase<ForbiddenAlwaysSetterCallRule>
 */
final class ForbiddenAlwaysSetterCallRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     *
     * @param string[] $filePaths
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    public function testRule(array $filePaths, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse($filePaths, $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        $errorMessage = sprintf(
            ForbiddenAlwaysSetterCallRule::ERROR_MESSAGE,
            FirstClassIdea::class,
            'addMotivation'
        );

        yield [[__DIR__ . '/Fixture/AlwaysTheSame.php'], [[$errorMessage, -1]]];

        yield [[__DIR__ . '/Fixture/SkipConstructorPassed.php'], []];
        yield [[__DIR__ . '/Fixture/SkipSometimesCalled.php'], []];
        yield [[__DIR__ . '/Fixture/SkipVendorLocated.php'], []];

        yield [[
            __DIR__ . '/Fixture/SkipUsedAsFormTypeDefaultClass.php',
            __DIR__ . '/Source/UsedInFormType.php',
        ], []];
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
        return self::getContainer()->getByType(ForbiddenAlwaysSetterCallRule::class);
    }

    /**
     * @return array<Collector>
     */
    protected function getCollectors(): array
    {
        $newAndSetterCallsCollector = self::getContainer()->getByType(NewAndSetterCallsCollector::class);
        $formTypeClassCollector = self::getContainer()->getByType(FormTypeClassCollector::class);

        return [$newAndSetterCallsCollector, $formTypeClassCollector];
    }
}
