<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Neon\Application;

use Nette\Neon\Entity;
use Nette\Neon\Neon;
use Nette\Utils\Arrays;
use Symplify\EasyCI\Contract\Application\FileProcessorInterface;
use Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;
use Symplify\EasyCI\ValueObject\FileError;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\EasyCI\Tests\Neon\Application\NeonFilesProcessor\NeonFilesProcessorTest
 */
final class NeonFilesProcessor implements FileProcessorInterface
{
    /**
     * @var string
     */
    private const SERVICES_KEY = 'services';

    /**
     * @var string
     */
    private const SETUP_KEY = 'setup';

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return FileErrorInterface[]
     */
    public function processFileInfos(array $fileInfos): array
    {
        $fileErrors = [];

        foreach ($fileInfos as $fileInfo) {
            $neon = Neon::decodeFile($fileInfo->getRealPath());

            // 1. we only take care about services
            $servicesNeon = $neon[self::SERVICES_KEY] ?? null;
            if ($servicesNeon === null) {
                continue;
            }

            $currentFileErrors = $this->processServicesSection($servicesNeon, $fileInfo);
            $fileErrors = array_merge($fileErrors, $currentFileErrors);
        }

        return $fileErrors;
    }

    private function createErrorMessageFromNeonEntity(Entity $neonEntity): string
    {
        $neonEntityContent = Neon::encode($neonEntity);

        return sprintf(
            'Complex entity found "%s"%s   Change it to explicit syntax with named keys, that is easier to read.',
            $neonEntityContent,
            PHP_EOL,
        );
    }

    /**
     * @param mixed[] $servicesNeon
     * @return FileErrorInterface[]
     */
    private function processServicesSection(array $servicesNeon, SmartFileInfo $fileInfo): array
    {
        $fileErrors = [];

        foreach ($servicesNeon as $serviceNeon) {
            if ($serviceNeon instanceof Entity) {
                $errorMessage = $this->createErrorMessageFromNeonEntity($serviceNeon);
                $fileErrors[] = new FileError($errorMessage, $fileInfo);
                continue;
            }

            // 0. skip empty or aliaseses
            if (! is_array($serviceNeon)) {
                continue;
            }

            // 1. the "setup" has allowed entities
            $serviceNeon = $this->removeSetupKey($serviceNeon);

            // 2. detect complex neon entities
            $neonLines = Arrays::flatten($serviceNeon, true);
            foreach ($neonLines as $neonLine) {
                if ($this->shouldSkip($neonLine)) {
                    continue;
                }

                /** @var Entity $neonLine */
                $errorMessage = $this->createErrorMessageFromNeonEntity($neonLine);
                $fileErrors[] = new FileError($errorMessage, $fileInfo);
            }
        }

        return $fileErrors;
    }

    /**
     * @param array<int|string, mixed> $singleService
     * @return array<int|string, mixed>
     */
    private function removeSetupKey(array $singleService): array
    {
        if (isset($singleService[self::SETUP_KEY])) {
            unset($singleService[self::SETUP_KEY]);
        }

        return $singleService;
    }

    private function shouldSkip(mixed $neonLine): bool
    {
        if (! $neonLine instanceof Entity) {
            return true;
        }

        // @see https://github.com/nette/di/blob/0ab4d4f67979a38fa06bf4c4f9cd81a98cc6ccba/tests/DI/Compiler.functions.phpt#L36-L40
        // skip functions
        return in_array($neonLine->value, ['not', 'string', 'int', 'float', 'bool'], true);
    }
}
