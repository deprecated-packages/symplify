<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Caching;

use Nette\Caching\Cache;
use Nette\Caching\Storages\Journal;
use Nette\Utils\Json;
use Symfony\Component\Filesystem\Exception\IOException;
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

    /** @var string */
    private $filePath;

    /** @var resource|null */
    private $fileResource = null;

    /** @var array  */
    private $journal;

    public function __construct(string $journalFilePath = 'journal.json')
    {
        $this->filePath = $journalFilePath;

        if (is_file($this->filePath)) {
            if (!is_writable($this->filePath)) {
                throw new IOException("Cache journal file '{$this->filePath}' is not writable");
            }
        } elseif (!is_writable(dirname($this->filePath))) {
            throw new IOException("Cache journal file '{$this->filePath}' does not exist and its parent directory is not writable");
        } else {
            (new SmartFileSystem())->dumpFile($this->filePath, Json::encode(self::EMPTY_STRUCTURE));
        }
    }

    public function write(string $key, array $dependencies): void
    {
        $this->journal = $this->readFile();

        if (!empty($dependencies[Cache::TAGS])) {
            $this->deleteTagsForKey($key);
            $this->addTagsForKey($key, $dependencies[Cache::TAGS]);
        }

        if (!empty($dependencies[Cache::PRIORITY])) {
            $this->unsetPriority($key);
            $this->setPriority($key, (int) $dependencies[Cache::PRIORITY]);
        }

        $this->writeFile($this->journal);
    }

    /**
     * @param array $conditions
     *
     * @return mixed[]|null
     */
    public function clean(array $conditions): ?array
    {
        $this->journal = $this->readFile();

        if (!empty($conditions[Cache::ALL])) {
            $this->journal = self::EMPTY_STRUCTURE;

            return null;
        }

        $keys = [];
        if (!empty($conditions[Cache::TAGS])) {
            foreach ($conditions[Cache::TAGS] as $tag) {
                $keys = array_merge($keys, $this->journal['tags']['by-tag'][$tag] ?? []);
            }
        }

        if (!empty($conditions[Cache::PRIORITY])) {
            foreach (array_keys($this->journal['priorities']['by-priority']) as $priority) {
                if ($priority <= $conditions[Cache::PRIORITY]) {
                    $keys = array_merge($keys, $this->journal['priorities']['by-priority'][$priority] ?? []);
                }
            }
        }

        foreach ($keys as $key) {
            $this->deleteTagsForKey($key);
            $this->unsetPriority($key);
        }

        $this->writeFile($this->journal);

        return $keys;
    }

    /**
     * @return array[]
     * @throws \JsonException
     */
    private function readFile(): array
    {
        if ($this->fileResource === null) {
            $this->fileResource = fopen($this->filePath, 'r+');
            if ($this->fileResource === false) {
                throw new IOException("Failed to open journal file '$this->filePath' for reading & writing");
            }

            $result = flock($this->fileResource, LOCK_EX | LOCK_NB);
            if ($result === false) {
                throw new IOException("Failed to acquire exclusive lock on the journal file '{$this->filePath}'");
            }
        }

        $rawData = fread($this->fileResource, (int) filesize($this->filePath));
        if ($rawData === false) {
            throw new IOException("Could not read contents from the journal file '{$this->filePath}'");
        }

        return Json::decode($rawData, Json::FORCE_ARRAY);
    }

    private function writeFile(array $data): void
    {
        if ($this->fileResource === null) {
            throw new IOException("Trying to write journal file without first reading it");
        }

        $rawData = Json::encode($data);

        $result = ftruncate($this->fileResource, mb_strlen($rawData, '8bit'));
        if ($result === false) {
            throw new IOException("Could not truncate the contents of journal file '$this->filePath' before writing new data to it");
        }

        $result = fseek($this->fileResource, 0);
        if ($result === -1) {
            throw new IOException("Could set the pointer to the beginning of the journal file '$this->filePath'");
        }

        $result = fwrite($this->fileResource, $rawData);
        if ($result === false) {
            throw new IOException("Could not write journal contents into file '$this->filePath'");
        }

        $result = flock($this->fileResource, LOCK_UN);
        if ($result === false) {
            throw new IOException("Failed to release lock on journal file '{$this->filePath}'");
        }

        $result = fflush($this->fileResource);
        if ($result === false) {
            throw new IOException("Failed to flush written data to journal file '{$this->filePath}'");
        }

        $result = fclose($this->fileResource);
        if ($result === false) {
            throw new IOException("Failed to close journal file '{$this->filePath}'");
        }

        $this->fileResource = null;
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
                    static function ($k) use ($key) { return $k !== $key; }
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
        if ($priority !== null) {
            $this->journal['priorities']['by-key'][$key] = $priority;
            if (!isset($this->journal['priorities']['by-priority'][$priority])) {
                $this->journal['priorities']['by-priority'][$priority] = [$key];
            } else {
                $this->journal['priorities']['by-priority'][$priority][] = $key;
            }
        }
    }

    private function unsetPriority(string $key): void
    {
        if (isset($this->journal['priorities']['by-key'][$key])) {
            $currentPriority = $this->journal['priorities']['by-key'][$key];

            $this->journal['priorities']['by-priority'][$currentPriority] = array_filter(
                $this->journal['priorities']['by-priority'][$currentPriority],
                static function ($k) use ($key) { return $k !== $key; }
            );
        }

        if (isset($this->journal['priorities']['by-key'][$key])) {
            unset($this->journal['priorities']['by-key'][$key]);
        }
    }
}
