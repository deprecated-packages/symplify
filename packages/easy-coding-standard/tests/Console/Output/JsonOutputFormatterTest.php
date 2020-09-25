<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Console\Output;

use Symplify\EasyCodingStandard\Console\Output\JsonOutputFormatter;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class JsonOutputFormatterTest extends AbstractKernelTestCase
{
    /**
     * @var JsonOutputFormatter
     */
    private $jsonOutputFormatter;

    protected function setUp(): void
    {
        $config = __DIR__ . '/config/config.php';
        $this->bootKernelWithConfigs(EasyCodingStandardKernel::class, [$config]);

        $this->jsonOutputFormatter = self::$container->get(JsonOutputFormatter::class);
    }

    public function test(): void
    {
        $jsonContent = $this->jsonOutputFormatter->createJsonContent();
        $this->assertStringMatchesFormatFile(__DIR__ . '/Fixture/expected_plain.json', $jsonContent . PHP_EOL);

//
//        $escapedPath = addslashes(__DIR__);
//        $stringInput = [
//            'check',
//            $escapedPath . '/wrong/wrong.php.inc',
//            '--config',
//            $escapedPath . '/config/config.php',
//            '--' . Option::OUTPUT_FORMAT,
//            JsonOutputFormatter::NAME,
//        ];
//
//        $input = new StringInput(implode(' ', $stringInput));
//        $exitCode = $this->easyCodingStandardConsoleApplication->run($input);
//
//        $output = $this->bufferedOutput->fetch();
//        $this->assertStringMatchesFormatFile(__DIR__ . '/Source/expected_json_output.json', $output);
//        $this->assertSame(ShellCode::ERROR, $exitCode);
    }
//
//    /**
//     * To catch printed content
//     */
//    private function createEasyCodingStandardStyleWithBufferOutput(): EasyCodingStandardStyle
//    {
//        $this->bufferedOutput = new BufferedOutput();
//        return new EasyCodingStandardStyle(new StringInput(''), $this->bufferedOutput, new Terminal());
//    }
}
