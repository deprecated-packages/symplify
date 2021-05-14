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

class JsonFileJournal implements Journal
{
    /** @var LockingJsonFileAccessor */
    private $fileAccessor;

    /** @var DataContainer */
    private $journal;

    public function __construct(string $journalFilePath = 'journal.json')
    {
        $this->fileAccessor = new LockingJsonFileAccessor($journalFilePath);

        if ($this->fileAccessor->exists() && !$this->fileAccessor->isWritable()) {
            throw new IOException("Cache journal file '{$journalFilePath}' is not writable");
        }

        if (!$this->fileAccessor->exists()) {
            $filesystem = new SmartFileSystem();
            $emptyContainer = new DataContainer();
            $filesystem->dumpFile($journalFilePath, $emptyContainer->toJson());
        }
    }

    public function write(string $key, array $dependencies): void
    {
        $this->journal = $this->fileAccessor->openAndRead();

        if (isset($dependencies[Cache::TAGS]) && count($dependencies[Cache::TAGS]) > 0) {
            $tagManager = new TagManager($this->journal);
            $tagManager->deleteTagsForKey($key);
            $tagManager->addTagsForKey($key, $dependencies[Cache::TAGS]);
        }

        if (isset($dependencies[Cache::PRIORITY])) {
            $priorityManager = new PriorityManager($this->journal);
            $priorityManager->unsetPriority($key);
            $priorityManager->setPriority($key, (int) $dependencies[Cache::PRIORITY]);
        }

        $this->fileAccessor->writeAndClose($this->journal);
    }

    /**
     * @param array $conditions
     *
     * @return mixed[]|null
     */
    public function clean(array $conditions): ?array
    {
        $this->journal = $this->fileAccessor->openAndRead();

        if (isset($conditions[Cache::ALL])) {
            $this->fileAccessor->writeAndClose(new DataContainer());

            return null;
        }

        $tagManager = new TagManager($this->journal);
        $priorityManager = new PriorityManager($this->journal);

        $keys = [];
        if (isset($conditions[Cache::TAGS]) && count($conditions[Cache::TAGS]) > 0) {
            $keys += $tagManager->getKeysByTags($conditions[Cache::TAGS]);
        }

        if (isset($conditions[Cache::PRIORITY]) && count($conditions[Cache::PRIORITY]) > 0) {
            $keys += $priorityManager->getKeysByPriority($conditions[Cache::PRIORITY]);
        }

        foreach ($keys as $key) {
            $tagManager->deleteTagsForKey($key);
            $priorityManager->unsetPriority($key);
        }

        $this->fileAccessor->writeAndClose($this->journal);

        return $keys;
    }

}
