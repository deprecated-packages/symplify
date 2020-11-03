<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenParentClassRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\ForbiddenParentClassRule;
use Symplify\CodingStandard\Tests\Rules\ForbiddenParentClassRule\Fixture\AnotherForbiddenParent;
use Symplify\CodingStandard\Tests\Rules\ForbiddenParentClassRule\Fixture\ClassForbiddenParent;
use Symplify\CodingStandard\Tests\Rules\ForbiddenParentClassRule\Fixture\HasParentWithPrefference;
use Symplify\CodingStandard\Tests\Rules\ForbiddenParentClassRule\Source\ForbiddenParent;
use Symplify\CodingStandard\Tests\Rules\ForbiddenParentClassRule\Source\PreferredClass;
use Symplify\CodingStandard\Tests\Rules\ForbiddenParentClassRule\Source\SomeFnMatched;
use Symplify\CodingStandard\Tests\Rules\ForbiddenParentClassRule\Source\UnwantedClass;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class ForbiddenParentClassRuleTest extends AbstractServiceAwareRuleTestCase
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
        $errorMessage = sprintf(
            ForbiddenParentClassRule::ERROR_MESSAGE,
            ClassForbiddenParent::class,
            ForbiddenParent::class,
            ForbiddenParentClassRule::COMPOSITION_OVER_INHERITANCE
        );
        yield [__DIR__ . '/Fixture/ClassForbiddenParent.php', [[$errorMessage, 9]]];

        $errorMessage = sprintf(
            ForbiddenParentClassRule::ERROR_MESSAGE,
            AnotherForbiddenParent::class,
            SomeFnMatched::class,
            ForbiddenParentClassRule::COMPOSITION_OVER_INHERITANCE
        );
        yield [__DIR__ . '/Fixture/AnotherForbiddenParent.php', [[$errorMessage, 9]]];

        yield [__DIR__ . '/Fixture/SkipParentClass.php', []];
        yield [__DIR__ . '/Fixture/SomeAbstractClassInheritingFromUnwantedClass.php', []];

        // test preference
        $errorMessage = sprintf(
            ForbiddenParentClassRule::ERROR_MESSAGE,
            HasParentWithPrefference::class,
            UnwantedClass::class,
            PreferredClass::class
        );
        yield [__DIR__ . '/Fixture/HasParentWithPrefference.php', [[$errorMessage, 9]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(ForbiddenParentClassRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
