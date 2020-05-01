<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\CognitiveComplexity\Tests\AstCognitiveComplexityAnalyzer;

use Iterator;
use Nette\Utils\FileSystem;
use PhpParser\Node;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use Symplify\CodingStandard\CognitiveComplexity\AstCognitiveComplexityAnalyzer;
use Symplify\CodingStandard\Tests\HttpKernel\SymplifyCodingStandardKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

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
    public function test(string $filePath, int $expectedCognitiveComplexity): void
    {
        $functionLike = $this->parseFileToFistFunctionLike($filePath);

        $cognitiveComplexity = $this->astCognitiveComplexityAnalyzer->analyzeFunctionLike($functionLike);

        $this->assertSame($expectedCognitiveComplexity, $cognitiveComplexity);
    }

    /**
     * Here are tested all examples from https://www.sonarsource.com/docs/CognitiveComplexity.pdf
     */
    public function provideTokensAndExpectedCognitiveComplexity(): Iterator
    {
        // passing
        yield [__DIR__ . '/Source/function.php.inc', 9];
        yield [__DIR__ . '/Source/function2.php.inc', 6];
        yield [__DIR__ . '/Source/function3.php.inc', 1];
        yield [__DIR__ . '/Source/function8.php.inc', 7];

        yield [__DIR__ . '/Source/function6.php.inc', 0];
        yield [__DIR__ . '/Source/function4.php.inc', 2];
        yield [__DIR__ . '/Source/function7.php.inc', 3];
    }

    /**
     * @return ClassMethod|Function_
     */
    private function parseFileToFistFunctionLike(string $filePath): FunctionLike
    {
        $parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
        $fileCotent = FileSystem::read($filePath);
        $nodes = $parser->parse($fileCotent);

        return (new NodeFinder())->findFirst((array) $nodes, function (Node $node) {
            return $node instanceof ClassMethod || $node instanceof Function_;
        });
    }
}
