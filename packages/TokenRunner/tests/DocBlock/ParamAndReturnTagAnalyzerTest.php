<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Tests\DocBlock;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\TokenRunner\DocBlock\ParamAndReturnTagAnalyzer;

final class ParamAndReturnTagAnalyzerTest extends TestCase
{
    /**
     * @var ParamAndReturnTagAnalyzer
     */
    private $paramAndReturnTagAnalyzer;

    protected function setUp(): void
    {
        $this->paramAndReturnTagAnalyzer = new ParamAndReturnTagAnalyzer();
    }

    /**
     * @dataProvider provideDocTypeDocDescriptionParamTypeAndResult()
     */
    public function test(?string $docType, ?string $docDescription, string $paramType, bool $expectedIsUseful): void
    {
        $isUseful = $this->paramAndReturnTagAnalyzer->isTagUseful($docType, $docDescription, $paramType);

        $this->assertSame($expectedIsUseful, $isUseful);
    }

    public function provideDocTypeDocDescriptionParamTypeAndResult(): Iterator
    {
        # useful
        yield ['boolean', 'some description', 'bool', true];
        # not useful
        yield ['boolean', null, 'bool', false];
        yield ['integer', null, 'int', false];
    }
}
