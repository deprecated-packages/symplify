<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Caching\JsonFile;

use Symfony\Component\Filesystem\Exception\IOException;
use Symplify\EasyCodingStandard\Caching\Journal\DataContainer;

class LockingJsonFileAccessor
{
    /** @var string */
    private $filePath;

    /** @var resource|null */
    private $fileResource = null;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function exists(): bool
    {
        return file_exists($this->filePath);
    }

    public function isWritable(): bool
    {
        return is_writable($this->filePath);
    }

    /**
     * @return DataContainer
     * @throws \Nette\Utils\JsonException
     */
    public function openAndRead(): DataContainer
    {
        if ($this->fileResource === null) {
            $this->fileResource = fopen($this->filePath, 'r+') ?: null;
            if ($this->fileResource === null) {
                throw new IOException("Failed to open file '$this->filePath' for reading & writing");
            }

            $result = flock($this->fileResource, LOCK_EX | LOCK_NB);
            if ($result === false) {
                throw new IOException("Failed to acquire exclusive lock on the file '{$this->filePath}'");
            }
        }

        $filesize = (int) filesize($this->filePath);
        $rawData = fread($this->fileResource, $filesize);
        if ($rawData === false) {
            throw new IOException("Could not read contents from the file '{$this->filePath}'");
        }

        return DataContainer::fromJson($rawData);
    }

    public function writeAndClose(DataContainer $dataContainer): void
    {
        if ($this->fileResource === null) {
            throw new IOException("Trying to write file without first reading it");
        }

        $rawData = $dataContainer->toJson();

        $byteLength = mb_strlen($rawData, '8bit');
        $result = ftruncate($this->fileResource, $byteLength);
        if ($result === false) {
            throw new IOException("Could not truncate the contents of file '$this->filePath' before writing new data to it");
        }

        $result = fseek($this->fileResource, 0);
        if ($result === -1) {
            throw new IOException("Could set the pointer to the beginning of the file '$this->filePath'");
        }

        $result = fwrite($this->fileResource, $rawData);
        if ($result === false) {
            throw new IOException("Could not write contents into file '$this->filePath'");
        }

        $result = flock($this->fileResource, LOCK_UN);
        if ($result === false) {
            throw new IOException("Failed to release lock on file '{$this->filePath}'");
        }

        $result = fflush($this->fileResource);
        if ($result === false) {
            throw new IOException("Failed to flush written data to file '{$this->filePath}'");
        }

        $result = fclose($this->fileResource);
        if ($result === false) {
            throw new IOException("Failed to close file '{$this->filePath}'");
        }

        $this->fileResource = null;
    }
}
