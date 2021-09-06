<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Neon\Application;

use Nette\Neon\Entity;
use Nette\Neon\Neon;
use Nette\Utils\Arrays;
use Symplify\EasyCI\Contract\Application\FileProcessorInterface;
use Symplify\EasyCI\ValueObject\FileError;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class NeonFilesProcessor implements FileProcessorInterface
{
    public function __construct(
        private SmartFileSystem $smartFileSystem
    ) {
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return FileError[]
     */
    public function analyzeFileInfos(array $fileInfos): array
    {
        $fileErrors = [];

        foreach ($fileInfos as $fileInfo) {
            $fileContent = $this->smartFileSystem->readFile($fileInfo->getRealPath());
            $neon = Neon::decode($fileContent);

            // 1. detect complex neon entities
            $flatNeon = Arrays::flatten($neon);
            foreach ($flatNeon as $itemNeon) {
                if (! $itemNeon instanceof Entity) {
                    continue;
                }

                $neonEntityContent = Neon::encode($itemNeon);

                $errorMessage = sprintf(
                    'Complex entity found "%s"%s   Change it to explicit syntax with named keys, that is easier to read.',
                    $neonEntityContent,
                    PHP_EOL,
                );

                $fileErrors[] = new FileError($errorMessage, $fileInfo);
            }
        }

        return $fileErrors;
    }
}
