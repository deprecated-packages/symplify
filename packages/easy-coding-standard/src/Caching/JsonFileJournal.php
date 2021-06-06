<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Caching;

use Nette\Caching\Cache;
use Nette\Caching\Storages\Journal;
use Symfony\Component\Filesystem\Exception\IOException;
use Symplify\EasyCodingStandard\Caching\Journal\DataContainer;
use Symplify\EasyCodingStandard\Caching\Journal\PriorityManager;
use Symplify\EasyCodingStandard\Caching\Journal\TagManager;
use Symplify\EasyCodingStandard\Caching\JsonFile\LockingJsonFileAccessor;
use Symplify\SmartFileSystem\SmartFileSystem;

final class JsonFileJournal implements Journal
{
    /**
     * @var LockingJsonFileAccessor
     */
    private $lockingJsonFileAccessor;

    /**
     * @var DataContainer
     */
    private $dataContainer;

    public function __construct(string $journalFilePath = 'journal.json')
    {
        $this->lockingJsonFileAccessor = new LockingJsonFileAccessor($journalFilePath);

        if ($this->lockingJsonFileAccessor->exists() && ! $this->lockingJsonFileAccessor->isWritable()) {
            throw new IOException(sprintf("Cache journal file '%s' is not writable", $journalFilePath));
        }

        if (! $this->lockingJsonFileAccessor->exists()) {
            $smartFileSystem = new SmartFileSystem();
            $dataContainer = new DataContainer();
            $smartFileSystem->dumpFile($journalFilePath, $dataContainer->toJson());
        }
    }

    public function write(string $key, array $dependencies): void
    {
        $this->dataContainer = $this->lockingJsonFileAccessor->openAndRead();

        if (isset($dependencies[Cache::TAGS]) && (is_countable($dependencies[Cache::TAGS]) ? count(
            $dependencies[Cache::TAGS]
        ) : 0) > 0) {
            $tagManager = new TagManager($this->dataContainer);
            $tagManager->deleteTagsForKey($key);
            $tagManager->addTagsForKey($key, $dependencies[Cache::TAGS]);
        }

        if (isset($dependencies[Cache::PRIORITY])) {
            $priorityManager = new PriorityManager($this->dataContainer);
            $priorityManager->unsetPriority($key);
            $priorityManager->setPriority($key, (int) $dependencies[Cache::PRIORITY]);
        }

        $this->lockingJsonFileAccessor->writeAndClose($this->dataContainer);
    }

    /**
     * @param array<string, mixed> $conditions
     * @return mixed[]|null
     */
    public function clean(array $conditions): ?array
    {
        $this->dataContainer = $this->lockingJsonFileAccessor->openAndRead();

        if (isset($conditions[Cache::ALL])) {
            $this->lockingJsonFileAccessor->writeAndClose(new DataContainer());
            return null;
        }

        $tagManager = new TagManager($this->dataContainer);
        $priorityManager = new PriorityManager($this->dataContainer);

        $keys = [];
        if (isset($conditions[Cache::TAGS]) && (is_countable($conditions[Cache::TAGS]) ? count(
            $conditions[Cache::TAGS]
        ) : 0) > 0) {
            $keys += $tagManager->getKeysByTags($conditions[Cache::TAGS]);
        }

        foreach ($keys as $key) {
            $tagManager->deleteTagsForKey($key);
            $priorityManager->unsetPriority($key);
        }

        $this->lockingJsonFileAccessor->writeAndClose($this->dataContainer);

        return $keys;
    }
}
