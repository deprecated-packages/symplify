<?php declare(strict_types=1);

namespace Symplify\Statie\FlatWhite\Tests\Latte;

use Latte\Engine;
use Latte\Loaders\StringLoader;
use Nette\Utils\Strings;
use PHPUnit\Framework\TestCase;
use Symplify\Statie\FlatWhite\Latte\LatteFactory;

final class BracketTest extends TestCase
{
    /**
     * @var Engine
     */
    private $latte;

    protected function setUp(): void
    {
        $this->latte = (new LatteFactory(new StringLoader))->create();
    }

    public function test(): void
    {
        $templateFileContent = file_get_contents(__DIR__ . '/BracketSource/latteWithCodeToHighlight.latte');
        $rendered = $this->renderExcludingHighlightBlocks($templateFileContent);

        $expectedFileContent = file_get_contents(__DIR__ . '/BracketSource/expectedCode.latte');
        $this->assertSame($expectedFileContent, $rendered);
    }

    private function renderExcludingHighlightBlocks(string $templateFileContent): string
    {
        // some magic here
        $pattern = '##m'; # m = multiline
        $replacedBlocks = [
            '___1' => '...',
        ];

        $i = 1;
        Strings::replace($templateFileContent, $pattern, function ($highlightedContent) use (&$replacedBlocks, &$i) {
            dump(func_get_args());
            $id = '___replace_block_' . ++$i;
            $replacedBlocks[$id] = $highlightedContent;
            return $id;
        });

        $parsedContent = $this->latte->renderToString($templateFileContent, [
            'hi' => 'Welcome'
        ]);

        $pattern2 = '...';
        Strings::replace($parsedContent, $pattern2, function ($idReference) use (&$replacedBlocks) {
            dump(func_get_args());
            return $replacedBlocks[$idReference];
        });

        return $parsedContent;

        // 1st Idea
        // 1. replace ```php ... ``` with ____1____ and store their content to array
        // 2. parse latte
        // 3. replace ____1____ with ```php ... ``` back

        // 2nd Idea
        // 1. wrap ```php ... ``` with {syntax off} ... {/syntax}
        // 2. parse latte
    }
}