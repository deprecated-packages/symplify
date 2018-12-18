<?php declare(strict_types=1);

namespace Symplify\Autodiscovery\Tests\Yaml;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;
use Symfony\Component\Yaml\Yaml;
use Symplify\Autodiscovery\Php\InterfaceAnalyzer;
use Symplify\Autodiscovery\Yaml\CommonNamespaceResolver;
use Symplify\Autodiscovery\Yaml\ExplicitToAutodiscoveryConverter;
use Symplify\Autodiscovery\Yaml\TagAnalyzer;

final class ExplicitToAutodiscoveryConverterTest extends TestCase
{
    /**
     * @var string
     */
    private const SPLIT_PATTERN = "#---\n#";

    /**
     * @var ExplicitToAutodiscoveryConverter
     */
    private $explicitToAutodiscoveryConverter;

    protected function setUp(): void
    {
        $this->explicitToAutodiscoveryConverter = new ExplicitToAutodiscoveryConverter(
            new SymfonyFilesystem(),
            new CommonNamespaceResolver(),
            new InterfaceAnalyzer(),
            new TagAnalyzer()
        );
    }

    public function test(): void
    {
        $this->doTestFile(__DIR__ . '/Fixture/singly_implemented_interfaces.yaml', 2);
        $this->doTestFile(__DIR__ . '/Fixture/singly_implemented_interfaces_excluded.yaml', 2, true);
        $this->doTestFile(__DIR__ . '/Fixture/first.yaml', 2);
        $this->doTestFile(__DIR__ . '/Fixture/tags_with_values.yaml', 2);
        $this->doTestFile(__DIR__ . '/Fixture/shopsys.yaml', 3);
        $this->doTestFile(__DIR__ . '/Fixture/elasticr.yaml', 3);
        $this->doTestFile(__DIR__ . '/Fixture/blog_post_votruba.yaml', 1);
    }

    private function doTestFile(string $file, int $nestingLevel, bool $removeSinglyImplemented = false): void
    {
        $yamlContent = FileSystem::read($file);

        [$originalYamlContent, $expectedYamlContent] = $this->splitFile($yamlContent);

        $originalYaml = Yaml::parse($originalYamlContent);
        $expectedYaml = Yaml::parse($expectedYamlContent);

        $this->assertSame(
            $expectedYaml,
            $this->explicitToAutodiscoveryConverter->convert(
                $originalYaml,
                $file,
                $nestingLevel,
                $removeSinglyImplemented
            )
        );
    }

    /**
     * @return string[]
     */
    private function splitFile(string $yamlContent): array
    {
        if (Strings::match($yamlContent, self::SPLIT_PATTERN)) {
            return Strings::split($yamlContent, self::SPLIT_PATTERN);
        }

        return [$yamlContent, $yamlContent];
    }
}
