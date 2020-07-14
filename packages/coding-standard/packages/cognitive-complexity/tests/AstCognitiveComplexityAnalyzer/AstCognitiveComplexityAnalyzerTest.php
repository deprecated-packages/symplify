<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\CognitiveComplexity\Tests\AstCognitiveComplexityAnalyzer;

use Iterator;
use PhpParser\Node;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use Symplify\CodingStandard\CognitiveComplexity\AstCognitiveComplexityAnalyzer;
use Symplify\CodingStandard\Tests\HttpKernel\SymplifyCodingStandardKernel;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class AstCognitiveComplexityAnalyzerTest extends AbstractKernelTestCase
{
    /**
     * @var AstCognitiveComplexityAnalyzer
     */
    private $astCognitiveComplexityAnalyzer;

    protected function setUp(): void
    {
        $this->bootKernel(SymplifyCodingStandardKernel::class);
        $this->astCognitiveComplexityAnalyzer = self::$container->get(AstCognitiveComplexityAnalyzer::class);
    }

    /**
     * @dataProvider provideTokensAndExpectedCognitiveComplexity()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        [$inputContent, $expectedCognitiveComplexity] = StaticFixtureSplitter::splitFileInfoToInputAndExpected(
            $fixtureFileInfo
        );

        $functionLike = $this->parseFileToFistFunctionLike($inputContent);
        $cognitiveComplexity = $this->astCognitiveComplexityAnalyzer->analyzeFunctionLike($functionLike);

        $this->assertSame($expectedCognitiveComplexity, $cognitiveComplexity);
    }

    /**
     * Here are tested all examples from https://www.sonarsource.com/docs/CognitiveComplexity.pdf
     */
    public function provideTokensAndExpectedCognitiveComplexity(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Source');
    }

    /**
     * @return ClassMethod|Function_
     */
    private function parseFileToFistFunctionLike(string $fileContent): FunctionLike
    {
        $parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
        $nodes = $parser->parse($fileContent);

        return (new NodeFinder())->findFirst((array) $nodes, function (Node $node) {
            return $node instanceof ClassMethod || $node instanceof Function_;
        });
    }
}
