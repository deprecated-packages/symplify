<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Caching;

use Nette\Caching\Cache;
use Nette\Caching\Storages\Journal;
use Symfony\Component\Filesystem\Exception\IOException;

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

    private $filePath;
    private $fileResource = null;

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
            file_put_contents($this->filePath, json_encode(self::EMPTY_STRUCTURE, JSON_PRETTY_PRINT));
        }
    }

    public function write(string $key, array $dependencies): void
    {
        $journal = $this->readFile();

        if (!empty($dependencies[Cache::TAGS])) {
            $journal = $this->deleteTagsForKey($journal, $key);
            $journal = $this->addTagsForKey($journal, $key, $dependencies[Cache::TAGS]);
        }

        if (!empty($dependencies[Cache::PRIORITY])) {
            $journal = $this->unsetPriority($journal, $key);
            $journal = $this->setPriority($journal, $key, (int) $dependencies[Cache::PRIORITY]);
        }

        $this->writeFile($journal);
    }

    public function clean(array $conditions): ?array
    {
        $journal = $this->readFile();

        if (!empty($conditions[Cache::ALL])) {
            $this->writeFile(self::EMPTY_STRUCTURE);

            return null;
        }

        $keys = [];
        if (!empty($conditions[Cache::TAGS])) {
            foreach ($conditions[Cache::TAGS] as $tag) {
                $keys = array_merge($keys, $journal['tags']['by-tag'][$tag] ?? []);
            }
        }

        if (!empty($conditions[Cache::PRIORITY])) {
            foreach (array_keys($journal['priorities']['by-priority']) as $priority) {
                if ($priority <= $conditions[Cache::PRIORITY]) {
                    $keys = array_merge($keys, $journal['priorities']['by-priority'][$priority] ?? []);
                }
            }
        }

        foreach ($keys as $key) {
            $journal = $this->deleteTagsForKey($journal, $key);
            $journal = $this->unsetPriority($journal, $key);
        }

        $this->writeFile($journal);

        return $keys;
    }

    private function readFile(): array
    {
        if ($this->fileResource === null) {
            if (($this->fileResource = fopen($this->filePath, 'r+')) === false) {
                throw new IOException("Failed to open journal file '$this->filePath' for reading & writing");
            }

            if (flock($this->fileResource, LOCK_EX) === false) {
                throw new IOException("Failed to acquire exclusive lock on the journal file '{$this->filePath}'");
            }
        }

        if (($rawData = fread($this->fileResource, filesize($this->filePath))) === false) {
            throw new IOException("Could not read contents from the journal file '{$this->filePath}'");
        }

        $jsonData = json_decode($rawData, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \JsonException("Could not parse JSON stored in the journal file '{$this->filePath}'. Error: " . json_last_error_msg());
        }

        return $jsonData;
    }

    private function writeFile(array $data): void
    {
        if ($this->fileResource === null) {
            throw new IOException("Trying to write journal file without first reading it");
        }

        $rawData = json_encode($data);

        if (ftruncate($this->fileResource, mb_strlen($rawData, '8bit')) === false) {
            throw new IOException("Could not truncate the contents of journal file '$this->filePath' before writing new data to it");
        }

        if (fseek($this->fileResource, 0) === -1) {
            throw new IOException("Could set the pointer to the beginning of the journal file '$this->filePath'");
        }

        if (fwrite($this->fileResource, $rawData) === false) {
            throw new IOException("Could not write journal contents into file '$this->filePath'");
        }

        if (flock($this->fileResource, LOCK_UN) === false) {
            throw new IOException("Failed to release lock on journal file '{$this->filePath}'");
        }

        if (fflush($this->fileResource) === false) {
            throw new IOException("Failed to flush written data to journal file '{$this->filePath}'");
        }

        if (fclose($this->fileResource) === false) {
            throw new IOException("Failed to close journal file '{$this->filePath}'");
        }

        $this->fileResource = null;
    }

    private function deleteTagsForKey(array $journal, string $key): array
    {
        if (isset($journal['tags']['by-key'][$key])) {
            $currentTags = $journal['tags']['by-key'][$key];
            unset($journal['tags']['by-key'][$key]);
        } else {
            $currentTags = [];
        }

        foreach ($currentTags as $tag) {
            if (isset($journal['tags']['by-tag'][$tag])) {
                $journal['tags']['by-tag'][$tag] = array_filter(
                    $journal['tags']['by-tag'][$tag],
                    static function ($k) use ($key) { return $k !== $key; }
                );
            }
        }

        return $journal;
    }

    private function addTagsForKey(array $journal, string $key, array $tags): array
    {
        $journal['tags']['by-key'][$key] = $tags;

        foreach ($tags as $tag) {
            if (!isset($journal['tags']['by-tag'][$tag])) {
                $journal['tags']['by-tag'][$tag] = [];
            }

            $journal['tags']['by-tag'][$tag][] = $key;
        }

        return $journal;
    }

    private function setPriority(array $journal, string $key, int $priority): array
    {
        if ($priority !== null) {
            $journal['priorities']['by-key'][$key] = $priority;
            if (!isset($journal['priorities']['by-priority'][$priority])) {
                $journal['priorities']['by-priority'][$priority] = [$key];
            } else {
                $journal['priorities']['by-priority'][$priority][] = $key;
            }
        }

        return $journal;
    }

    private function unsetPriority(array $journal, string $key): array
    {
        if (isset($journal['priorities']['by-key'][$key])) {
            $currentPriority = $journal['priorities']['by-key'][$key];

            $journal['priorities']['by-priority'][$currentPriority] = array_filter(
                $journal['priorities']['by-priority'][$currentPriority],
                static function ($k) use ($key) { return $k !== $key; }
            );
        }

        if (isset($journal['priorities']['by-key'][$key])) {
            unset($journal['priorities']['by-key'][$key]);
        }

        return $journal;
    }
}
