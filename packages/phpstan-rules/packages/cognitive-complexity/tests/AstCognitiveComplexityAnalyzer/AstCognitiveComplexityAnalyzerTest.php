<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\CognitiveComplexity\Tests\AstCognitiveComplexityAnalyzer;

use Iterator;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use PHPStan\DependencyInjection\ContainerFactory;
use PHPUnit\Framework\TestCase;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PHPStanRules\CognitiveComplexity\AstCognitiveComplexityAnalyzer;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

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
        if ($functionLike === null) {
            throw new ShouldNotHappenException();
        }

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
        $parserFactory = new ParserFactory();
        $parser = $parserFactory->create(ParserFactory::ONLY_PHP7);
        $nodes = $parser->parse($fileContent);

        $nodeFinder = new NodeFinder();
        return $nodeFinder->findFirst((array) $nodes, function (Node $node): bool {
            return $node instanceof ClassMethod || $node instanceof Function_;
        });
    }
}
