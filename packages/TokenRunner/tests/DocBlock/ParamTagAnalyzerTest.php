<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Tests\DocBlock;

use PHPUnit\Framework\TestCase;
use Symplify\TokenRunner\DocBlock\ParamAndReturnTagAnalyzer;

final class ParamTagAnalyzerTest extends TestCase
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
        $isUseful = $this->paramAndReturnTagAnalyzer->isTagUseful(
            $docType,
            $docDescription,
            $paramType
        );

        $this->assertSame($expectedIsUseful, $isUseful);
    }

    /**
     * @return string[][]|bool[][]
     */
    public function provideDocTypeDocDescriptionParamTypeAndResult(): array
    {
        return [
            # useful
            ['boolean', 'some description', 'bool', true],
            # not useful
            ['boolean', null, 'bool', false],
            ['integer', null, 'int', false],
        ];
    }
}
