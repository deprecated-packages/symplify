<?php declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Parser;

use Nette\Caching\Cache;
use PhpParser\Node;
use PHPStan\Parser\Parser;

final class FileSystemCachedParser implements Parser
{
    /**
     * @var Parser
     */
    private $originalParser;

    /**
     * @var Cache
     */
    private $cache;

    public function __construct(Parser $originalParser, Cache $cache)
    {
        $this->cache = $cache;
        $this->originalParser = $originalParser;
    }

    /**
     * @return Node[]
     */
    public function parseFile(string $file): array
    {
        $md5File = md5_file($file);
        $cachedFile = $this->cache->load($file);

        if (isset($cachedFile['md5_file']) && $cachedFile['md5_file'] === $md5File) {
            // no change of file - no need to process it
            return [];
        }

        $this->cache->save($file, [
            'md5_file' => $md5File,
        ]);

        return $this->originalParser->parseFile($file);
    }

    /**
     * @return Node[]
     */
    public function parseString(string $sourceCode): array
    {
        return $this->originalParser->parseString($sourceCode);
    }
}
