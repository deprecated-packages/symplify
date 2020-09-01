<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\CognitiveComplexity\Tests\AstCognitiveComplexityAnalyzer;

use Iterator;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use PHPStan\DependencyInjection\ContainerFactory;
use PHPUnit\Framework\TestCase;
use Symplify\CodingStandard\CognitiveComplexity\AstCognitiveComplexityAnalyzer;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\SmartFileSystem\SmartFileInfo;

final class AstCognitiveComplexityAnalyzerTest extends TestCase
{
    /**
     * @var AstCognitiveComplexityAnalyzer
     */
    private $astCognitiveComplexityAnalyzer;

    protected function setUp(): void
    {
        $phpstanContainerFactory = new ContainerFactory(getcwd());

        $tempFile = sys_get_temp_dir() . '/_symplify_cogntive_complexity_test';
        $container = $phpstanContainerFactory->create(
            $tempFile,
            [__DIR__ . '/../../config/cognitive-complexity-rules.neon'],
            []
        );

        $this->astCognitiveComplexityAnalyzer = $container->getByType(AstCognitiveComplexityAnalyzer::class);
    }

    /**
     * @dataProvider provideTokensAndExpectedCognitiveComplexity()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        $inputAndExpected = StaticFixtureSplitter::splitFileInfoToInputAndExpected($fixtureFileInfo);

        $functionLike = $this->parseFileToFistFunctionLike($inputAndExpected->getInput());
        $cognitiveComplexity = $this->astCognitiveComplexityAnalyzer->analyzeFunctionLike($functionLike);

        $this->assertSame((int) $inputAndExpected->getExpected(), $cognitiveComplexity);
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
    private function parseFileToFistFunctionLike(string $fileContent): ?Node
    {
        $parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
        $nodes = $parser->parse($fileContent);

        return (new NodeFinder())->findFirst((array) $nodes, function (Node $node): bool {
            return $node instanceof ClassMethod || $node instanceof Function_;
        });
    }
}
