<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Markdown;

use Nette\Utils\Strings;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class MarkdownPHPCodeFormatter
{
    /**
     * @see https://regex101.com/r/4YUIu1/1
     * @var string
     */
    private const PHP_CODE_SNIPPET_IN_MARKDOWN = '#\`\`\`php\s+(?<content>[^\`\`\`]+)\s+\`\`\`#ms';

    /**
     * @var string
     */
    private const PHP_CODE_SNIPPET_IN_MARKDOWN_BACK = '#\\`\\`\\`php\\s+([^\\`\\`\\`]+)\\s+\\`\\`\\`#';

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    /**
     * @var FixerFileProcessor
     */
    private $fixerFileProcessor;

    /**
     * @var SniffFileProcessor
     */
    private $sniffFileProcessor;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(
        SmartFileSystem $smartFileSystem,
        FixerFileProcessor $fixerFileProcessor,
        SniffFileProcessor $sniffFileProcessor,
        Configuration $configuration
    ) {
        $this->smartFileSystem = $smartFileSystem;
        $this->fixerFileProcessor = $fixerFileProcessor;
        $this->sniffFileProcessor = $sniffFileProcessor;
        $this->configuration = $configuration;
    }

    public function format(SmartFileInfo $fileInfo): ?string
    {
        // enable fixing
        $this->configuration->resolveFromArray(['isFixer' => true]);

        $content = $fileInfo->getContents();

        $matches = Strings::matchAll($content, self::PHP_CODE_SNIPPET_IN_MARKDOWN);
        if ($matches === []) {
            // nothing changed
            return null;
        }

        $fixedContents = $this->collectFixedContents($matches);

        foreach ($fixedContents as $key => $fixedContent) {
            $content = (string) Strings::replace(
                $content,
                self::PHP_CODE_SNIPPET_IN_MARKDOWN_BACK,
                function () use ($fixedContent): string {
                    static $key = 0;

                    $result = '```php' . PHP_EOL . '<?php' . PHP_EOL . ltrim($fixedContent, ' ') . PHP_EOL . '```';
                    $key++;

                    return $result;
                }
            );

            /** @var string $file */
            $file = sprintf('php-code-%s.php', $key);
            $this->smartFileSystem->remove($file);
        }

        return $content;
    }

    /**
     * @param string[][] $matches
     * @return string[]
     */
    private function collectFixedContents(array $matches): array
    {
        $fixedContents = [];

        foreach ($matches as $key => $match) {
            /** @var string $file */
            $file = sprintf('php-code-%s.php', $key);

            $fileContent = '<?php' . PHP_EOL . ltrim($match['content'], '<?php');
            $this->smartFileSystem->dumpFile($file, $fileContent);

            $fileInfo = new SmartFileInfo($file);
            $this->fixerFileProcessor->processFile($fileInfo);

            $fileInfo = new SmartFileInfo($file);
            $this->sniffFileProcessor->processFile($fileInfo);

            /** @var string $fileContent */
            $fileContent = file_get_contents($file);
            $fixedContents[] = ltrim($fileContent, '<?php' . PHP_EOL);
        }

        return $fixedContents;
    }
}
