<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\Tests\LattePersistence;

use Iterator;
use Latte\Engine;
use Nette\Bridges\ApplicationLatte\UIMacros;
use PHPUnit\Framework\TestCase;
use Symplify\TemplateChecker\Tests\LattePersistence\Source\PlusFilterProvider;
use Symplify\TemplateChecker\Tests\LattePersistence\Source\SomePresenter;

/**
 * This is a meta test for @see \Symplify\TemplateChecker\StaticCallWithFilterReplacer
 * To verify that the filter behaves the same as static function
 */
final class LatteFilterPersistenceTest extends TestCase
{
    /**
     * @var Engine
     */
    private $latteEngine;

    protected function setUp(): void
    {
        $this->latteEngine = new Engine();

        // install nette/application macros, so we have {link} available
        UIMacros::install($this->latteEngine->getCompiler());

        $this->latteEngine->addProvider('uiControl', new SomePresenter());
        $this->latteEngine->addProvider('uiPresenter', new SomePresenter());

        $plusFilterProvider = new PlusFilterProvider();

        $this->latteEngine->addFilter($plusFilterProvider->getName(), $plusFilterProvider);
        $this->latteEngine->addFunction($plusFilterProvider->getName(), $plusFilterProvider);
    }

    /**
     * Fixture testing is based on @see https://github.com/symplify/easy-testing
     * @dataProvider provideData()
     * @dataProvider provideDataInArray()
     */
    public function testFilter(
        string $inputFilterFilePath,
        string $inputStaticCallFilePath,
        string $expectedContent
    ): void {
        $result = $this->latteEngine->renderToString($inputFilterFilePath);
        $contentWithoutSpaces = trim($result);

        $this->assertSame($expectedContent, $contentWithoutSpaces);

        $result = $this->latteEngine->renderToString($inputStaticCallFilePath);
        $contentWithoutSpaces = trim($result);

        $this->assertSame($expectedContent, $contentWithoutSpaces);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/latte_filter.latte', __DIR__ . '/Fixture/latte_static_call.latte', '7'];
        yield [__DIR__ . '/Fixture/latte_filter_with_bracket.latte', __DIR__ . '/Fixture/latte_static_call.latte', '7'];
        yield [__DIR__ . '/Fixture/latte_filter_around.latte', __DIR__ . '/Fixture/latte_static_call.latte', '7'];

        // function approach
        yield [__DIR__ . '/Fixture/latte_function.latte', __DIR__ . '/Fixture/latte_static_call.latte', '7'];
    }

    public function provideDataInArray(): Iterator
    {
        yield [
            __DIR__ . '/Fixture/latte_function_in_array.latte',
            __DIR__ . '/Fixture/latte_static_call_in_array.latte',
            '100:7',
        ];

        yield [
            __DIR__ . '/Fixture/latte_link_function_in_array.latte',
            __DIR__ . '/Fixture/latte_link_static_call_in_array.latte',
            'article/7',
        ];
    }
}
