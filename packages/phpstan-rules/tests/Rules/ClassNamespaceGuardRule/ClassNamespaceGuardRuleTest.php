<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ClassNamespaceGuardRule;

use Iterator;
use PHPStan\Rules\Rule;
use ReflectionClass;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ClassNamespaceGuardRule;
use Symplify\PHPStanRules\Tests\Rules\ClassNamespaceGuardRule\Fixture\App\CommandInUnauthorizedNamespace;
use Symplify\PHPStanRules\Tests\Rules\ClassNamespaceGuardRule\Fixture\App\ExceptionInUnauthorizedNamespace;
use Symplify\PHPStanRules\Tests\Rules\ClassNamespaceGuardRule\Fixture\App\FormInUnauthorizedNamespace;
use Symplify\PHPStanRules\Tests\Rules\ClassNamespaceGuardRule\Fixture\App\Model\Customer\CustomerRequestModelInUnauthorizedNamespace;
use Symplify\PHPStanRules\Tests\Rules\ClassNamespaceGuardRule\Fixture\App\Model\Order\OrderRequestModelInUnauthorizedNamespace;

/**
 * @extends AbstractServiceAwareRuleTestCase<ClassNamespaceGuardRule>
 */
final class ClassNamespaceGuardRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[] $expectedErrorMessagesWithLines
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
        yield [__DIR__ . '/Fixture/App/Command/SkipCommandInAuthorizedNamespace.php', []];
        yield [__DIR__ . '/Fixture/App/Form/SkipFormInAuthorizedNamespace.php', []];
        yield [__DIR__ . '/Fixture/App/Model/Customer/Request/SkipCustomerRequestModelInAuthorizedNamespace.php', []];
        yield [__DIR__ . '/Fixture/App/Model/Order/Request/SkipOrderRequestModelInAuthorizedNamespace.php', []];
        yield [__DIR__ . '/Fixture/App/Exception/SkipExceptionInAuthorizedNamespace.php', []];
        yield [__DIR__ . '/Fixture/App/Services/SkipExceptionInAuthorizedNamespace.php', []];

        $errorMessage = sprintf(
            ClassNamespaceGuardRule::ERROR_MESSAGE,
            CommandInUnauthorizedNamespace::class,
            json_encode(['Symplify\PHPStanRules\Tests\Rules\ClassNamespaceGuardRule\Fixture\App\Command\**']),
            (new ReflectionClass(CommandInUnauthorizedNamespace::class))->getNamespaceName(),
        );

        yield [__DIR__ . '/Fixture/App/CommandInUnauthorizedNamespace.php', [[$errorMessage, 9]]];

        $errorMessage = sprintf(
            ClassNamespaceGuardRule::ERROR_MESSAGE,
            FormInUnauthorizedNamespace::class,
            json_encode(['Symplify\PHPStanRules\Tests\Rules\ClassNamespaceGuardRule\Fixture\App\Form\**']),
            (new ReflectionClass(FormInUnauthorizedNamespace::class))->getNamespaceName(),
        );

        yield [__DIR__ . '/Fixture/App/FormInUnauthorizedNamespace.php', [[$errorMessage, 7]]];

        $errorMessage = sprintf(
            ClassNamespaceGuardRule::ERROR_MESSAGE,
            CustomerRequestModelInUnauthorizedNamespace::class,
            json_encode(['Symplify\PHPStanRules\Tests\Rules\ClassNamespaceGuardRule\Fixture\App\Model\**\Request\**']),
            (new ReflectionClass(CustomerRequestModelInUnauthorizedNamespace::class))->getNamespaceName(),
        );

        yield [
            __DIR__ . '/Fixture/App/Model/Customer/CustomerRequestModelInUnauthorizedNamespace.php',
            [[$errorMessage, 7]], ];

        $errorMessage = sprintf(
            ClassNamespaceGuardRule::ERROR_MESSAGE,
            OrderRequestModelInUnauthorizedNamespace::class,
            json_encode(['Symplify\PHPStanRules\Tests\Rules\ClassNamespaceGuardRule\Fixture\App\Model\**\Request\**']),
            (new ReflectionClass(OrderRequestModelInUnauthorizedNamespace::class))->getNamespaceName(),
        );

        yield [__DIR__ . '/Fixture/App/Model/Order/OrderRequestModelInUnauthorizedNamespace.php', [[$errorMessage, 7]]];

        $errorMessage = sprintf(
            ClassNamespaceGuardRule::ERROR_MESSAGE,
            ExceptionInUnauthorizedNamespace::class,
            json_encode([
                'Symplify\PHPStanRules\Tests\Rules\ClassNamespaceGuardRule\Fixture\App\Exception\**',
                'Symplify\PHPStanRules\Tests\Rules\ClassNamespaceGuardRule\Fixture\App\Services\**',
            ]),
            (new ReflectionClass(ExceptionInUnauthorizedNamespace::class))->getNamespaceName(),
        );

        yield [__DIR__ . '/Fixture/App/ExceptionInUnauthorizedNamespace.php', [[$errorMessage, 9]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(ClassNamespaceGuardRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
