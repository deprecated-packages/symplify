<?php

declare(strict_types=1);

namespace Symplify\Astral\Tests\PhpDocParser\SimplePhpDocParser;

use Symplify\Astral\PhpDocParser\SimplePhpDocParser;
use Symplify\Astral\PhpDocParser\ValueObject\Ast\PhpDoc\SimplePhpDocNode;
use Symplify\Astral\Tests\PhpDocParser\HttpKernel\SimplePhpDocParserKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SimplePhpDocParserTest extends AbstractKernelTestCase
{
    private SimplePhpDocParser $simplePhpDocParser;

    protected function setUp(): void
    {
        $this->bootKernel(SimplePhpDocParserKernel::class);
        $this->simplePhpDocParser = $this->getService(SimplePhpDocParser::class);
    }

    public function testVar(): void
    {
        $smartFileInfo = new SmartFileInfo(__DIR__ . '/Fixture/var_int.txt');

        $simplePhpDocNode = $this->simplePhpDocParser->parseDocBlock($smartFileInfo->getContents());
        $this->assertInstanceOf(SimplePhpDocNode::class, $simplePhpDocNode);

        $varTagValues = $simplePhpDocNode->getVarTagValues();
        $this->assertCount(1, $varTagValues);
    }

    public function testParam(): void
    {
        $smartFileInfo = new SmartFileInfo(__DIR__ . '/Fixture/param_string_name.txt');

        $simplePhpDocNode = $this->simplePhpDocParser->parseDocBlock($smartFileInfo->getContents());
        $this->assertInstanceOf(SimplePhpDocNode::class, $simplePhpDocNode);

        // DX friendly
        $paramType = $simplePhpDocNode->getParamType('name');
        $withDollarParamType = $simplePhpDocNode->getParamType('$name');

        $this->assertSame($paramType, $withDollarParamType);
    }
}
