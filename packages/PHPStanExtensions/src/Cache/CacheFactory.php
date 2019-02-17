<?php declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Cache;

use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;
use Nette\Utils\FileSystem;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

final class CacheFactory
{
    /**
     * @var SymfonyFilesystem
     */
    private $symfonyFilesystem;

    public function __construct(SymfonyFilesystem $symfonyFilesystem)
    {
        $this->symfonyFilesystem = $symfonyFilesystem;
    }

    public function create(): Cache
    {
        $cacheDirectory = sys_get_temp_dir() . '/symplify_phpstan_cache';

        if ($this->symfonyFilesystem->exists($cacheDirectory) === false) {
            FileSystem::createDir($cacheDirectory);
        }

        return new Cache(new FileStorage($cacheDirectory));
    }
}
