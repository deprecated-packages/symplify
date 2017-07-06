<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Tests\DependencyInjection;

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;

final class FixerServiceRegistrationTest extends TestCase
{
    public function test(): void
    {
        $container = (new ContainerFactory)->createWithConfig(
            __DIR__ . '/FixerServiceRegistrationSource/easy-coding-standard.neon'
        );

        $arraySyntaxFixer = $container->get(ArraySyntaxFixer::class);
        $this->assertSame(
            ['syntax' => 'short'],
            Assert::getObjectAttribute($arraySyntaxFixer, 'configuration')
        );

        $visibilityRequiredFixer = $container->get(VisibilityRequiredFixer::class);
        $this->assertSame(
            ['elements' => ['property']],
            Assert::getObjectAttribute($visibilityRequiredFixer, 'configuration')
        );
    }
}
