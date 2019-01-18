<?php declare(strict_types=1);

namespace Symplify\Statie\FileSystem;

use Nette\Utils\DateTime;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use Symplify\Statie\Configuration\StatieConfiguration;

final class GeneratedFilesDumper
{
    /**
     * @var StatieConfiguration
     */
    private $statieConfiguration;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(StatieConfiguration $statieConfiguration, Filesystem $filesystem)
    {
        $this->statieConfiguration = $statieConfiguration;
        $this->filesystem = $filesystem;
    }

    /**
     * @param mixed[] $items
     */
    public function dump(string $key, array $items): void
    {
        $data['parameters'][$key] = $items;

        $yamlDump = Yaml::dump($data, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);

        $dumpFilePath = $this->statieConfiguration->getSourceDirectory() . '/_data/generated/' . $key . '.yaml';
        $timestampComment = $this->createTimestampComment();

        $this->filesystem->dumpFile($dumpFilePath, $timestampComment . $yamlDump);
    }

    private function createTimestampComment(): string
    {
        return sprintf(
            '# this file was generated on %s, do not edit it manually' . PHP_EOL,
            (new DateTime())->format('Y-m-d H:i:s')
        );
    }
}
