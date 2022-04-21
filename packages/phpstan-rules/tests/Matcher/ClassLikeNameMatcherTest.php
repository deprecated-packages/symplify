<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Matcher;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\PHPStanRules\Matcher\ClassLikeNameMatcher;

final class ClassLikeNameMatcherTest extends TestCase
{
    private ClassLikeNameMatcher $classLikeNameMatcher;

    protected function setUp(): void
    {
        $this->classLikeNameMatcher = new ClassLikeNameMatcher();
    }

    /**
     * @dataProvider provideData()
     */
    public function test(string $classLikeName, string $namespaceWildcardPattern, bool $expectedIsMatched): void
    {
        $actualIsMatched = $this->classLikeNameMatcher->isClassLikeNameMatchedAgainstPattern(
            $classLikeName,
            $namespaceWildcardPattern
        );

        $this->assertSame($expectedIsMatched, $actualIsMatched);
    }

    /**
     * @return Iterator<string, array<string|bool>>
     */
    public function provideData(): Iterator
    {
        yield 'identical class like names should match' => ['App\Form\MyForm', 'App\Form\MyForm', true];
        yield '* should match MyForm in the end of the namespace' => ['App\Form\MyForm', 'App\Form\*', true];
        yield '* should match Form in the middle of the namespace' => ['App\Form\MyForm', 'App\*\MyForm', true];
        yield '* should match App in the beginning of the namespace' => ['App\Form\MyForm', '*\Form\MyForm', true];
        yield '* should not match Form\Admin in the middle of the namespace' => [
            'App\Form\Admin\MyForm',
            'App\*\MyForm',
            false,
        ];
        yield '** should match Form\Admin in the middle of the namespace' => [
            'App\Form\Admin\MyForm',
            'App\**\MyForm',
            true,
        ];
        yield '** should match Admin\MyForm in the end of the namespace' => [
            'App\Form\Admin\MyForm',
            'App\Form\**',
            true,
        ];
        yield '** should match App\Form in the beginning of the namespace' => [
            'App\Form\Admin\MyForm',
            '**\Admin\MyForm',
            true,
        ];
        yield 'first * should match Advanced and second * should match Form at the end of the namespace' => [
            'App\Form\Admin\AdvancedForm',
            'App\Form\*\Advanced*',
            true,
        ];
        yield 'first ? should match F and second ? should match o in MyForm' => [
            'App\Form\MyForm',
            'App\*\My??rm',
            true,
        ];
        yield '** should match Form\Admin, first ? should match F and second ? should match o in MyForm, * should match m' => [
            'App\Form\Admin\MyForm',
            'App\**\My??r*',
            true,
        ];
        yield 'The escaping of regular expression characters should work correctly' => [
            'App\.+[^]$(){}=!<>|:-#\Form\MyForm',
            'App\.+[^]$(){}=!<>|:-#\Form\**',
            true,
        ];
    }
}
