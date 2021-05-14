<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Caching;

use Nette\Caching\Cache;
use Nette\Caching\Storages\Journal;
use Nette\Utils\Json;
use Symfony\Component\Filesystem\Exception\IOException;
use Symplify\EasyCodingStandard\Caching\JsonFile\LockingJsonFileAccessor;
use Symplify\SmartFileSystem\SmartFileSystem;

class JsonFileJournal implements Journal
{
    private const EMPTY_STRUCTURE = [
        'tags' => [
            'by-tag' => [],
            'by-key' => [],
        ],
        'priorities' => [
            'by-priority' => [],
            'by-key' => [],
        ]
    ];

    /** @var LockingJsonFileAccessor */
    private $fileAccessor;

    /** @var array[]  */
    private $journal;

    public function __construct(string $journalFilePath = 'journal.json')
    {
        $this->fileAccessor = new LockingJsonFileAccessor($journalFilePath);

        if ($this->fileAccessor->exists() && !$this->fileAccessor->isWritable()) {
            throw new IOException("Cache journal file '{$journalFilePath}' is not writable");
        }

        if (!$this->fileAccessor->exists()) {
            $filesystem = new SmartFileSystem();
            $filesystem->dumpFile($journalFilePath, Json::encode(self::EMPTY_STRUCTURE));
        }
    }

    public function write(string $key, array $dependencies): void
    {
        $this->journal = $this->fileAccessor->openAndRead();

        if (count($dependencies[Cache::TAGS]) > 0) {
            $this->deleteTagsForKey($key);
            $this->addTagsForKey($key, $dependencies[Cache::TAGS]);
        }

        if (count($dependencies[Cache::PRIORITY]) > 0) {
            $this->unsetPriority($key);
            $this->setPriority($key, (int) $dependencies[Cache::PRIORITY]);
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

        if (count($conditions[Cache::ALL]) > 0) {
            $this->journal = self::EMPTY_STRUCTURE;

            return null;
        }

        $keys = [];
        if (count($conditions[Cache::TAGS]) > 0) {
            $keys += $this->getKeysByTags($conditions[Cache::TAGS]);
        }

        if (count($conditions[Cache::PRIORITY]) > 0) {
            $keys += $this->getKeysByPriority($conditions[Cache::PRIORITY]);
        }

        foreach ($keys as $key) {
            $this->deleteTagsForKey($key);
            $this->unsetPriority($key);
        }

        $this->fileAccessor->writeAndClose($this->journal);

        return $keys;
    }


    private function deleteTagsForKey(string $key): void
    {
        if (isset($this->journal['tags']['by-key'][$key])) {
            $currentTags = $this->journal['tags']['by-key'][$key];
            unset($this->journal['tags']['by-key'][$key]);
        } else {
            $currentTags = [];
        }

        foreach ($currentTags as $tag) {
            if (isset($this->journal['tags']['by-tag'][$tag])) {
                $this->journal['tags']['by-tag'][$tag] = array_filter(
                    $this->journal['tags']['by-tag'][$tag],
                    static function ($itemKey) use ($key) { return $itemKey !== $key; }
                );
            }
        }
    }

    private function addTagsForKey(string $key, array $tags): void
    {
        $this->journal['tags']['by-key'][$key] = $tags;

        foreach ($tags as $tag) {
            if (!isset($this->journal['tags']['by-tag'][$tag])) {
                $this->journal['tags']['by-tag'][$tag] = [];
            }

            $this->journal['tags']['by-tag'][$tag][] = $key;
        }
    }

    private function setPriority(string $key, int $priority): void
    {
        $this->journal['priorities']['by-key'][$key] = $priority;
        if (!isset($this->journal['priorities']['by-priority'][$priority])) {
            $this->journal['priorities']['by-priority'][$priority] = [$key];
        } else {
            $this->journal['priorities']['by-priority'][$priority][] = $key;
        }
    }

    private function unsetPriority(string $key): void
    {
        if (isset($this->journal['priorities']['by-key'][$key])) {
            $currentPriority = $this->journal['priorities']['by-key'][$key];

            $this->journal['priorities']['by-priority'][$currentPriority] = array_filter(
                $this->journal['priorities']['by-priority'][$currentPriority],
                static function ($itemKey) use ($key) { return $itemKey !== $key; }
            );
        }

        if (isset($this->journal['priorities']['by-key'][$key])) {
            unset($this->journal['priorities']['by-key'][$key]);
        }
    }

    /**
     * @param array $tags
     *
     * @return mixed[]
     */
    private function getKeysByTags($tags): array
    {
        $keys = [];
        foreach ($tags as $tag) {
            $keys = array_merge($keys, $this->journal['tags']['by-tag'][$tag] ?? []);
        }

        return $keys;
}

    /**
     * @param int $priorityThreshold
     *
     * @return mixed[]
     */
    private function getKeysByPriority(int $priorityThreshold): array
    {
        $keys = [];
        foreach (array_keys($this->journal['priorities']['by-priority']) as $priority) {
            if ($priority <= $priorityThreshold) {
                $keys = array_merge($keys, $this->journal['priorities']['by-priority'][$priority] ?? []);
            }
        }
        return $keys;
    }
}
