<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Neon\Application;

use Nette\Neon\Decoder;
use Nette\Neon\Node;
use Nette\Neon\Node\ArrayItemNode;
use Nette\Neon\Node\ArrayNode;
use Nette\Neon\Node\EntityNode;
use Nette\Neon\Traverser;
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

    public function __construct(
        private Decoder $decoder
    ) {
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return FileErrorInterface[]
     */
    public function processFileInfos(array $fileInfos): array
    {
        $fileErrors = [];

        foreach ($fileInfos as $fileInfo) {
            $currentFileErrors = $this->process($fileInfo);
            $fileErrors = array_merge($fileErrors, $currentFileErrors);
        }

        return $fileErrors;
    }

    /**
     * @return FileErrorInterface[]
     */
    private function process(SmartFileInfo $fileInfo): array
    {
        $fileErrors = [];

        $node = $this->decoder->parseToNode($fileInfo->getContents());

        $traverser = new Traverser();
        $traverser->traverse($node, function ($node) use ($fileInfo, &$fileErrors) {
            if (! $node instanceof ArrayItemNode) {
                return null;
            }

            if ($node->key === null) {
                return null;
            }

            $keyName = $node->key->toString();

            // we only take care about services
            if ($keyName !== self::SERVICES_KEY) {
                return null;
            }

            $currentFileErrors = $this->processServicesSection($node->value, $fileInfo);
            $fileErrors = array_merge($fileErrors, $currentFileErrors);
            return null;
        });

        return $fileErrors;
    }

    /**
     * @return FileErrorInterface[]
     */
    private function processServicesSection(Node $servicesNode, SmartFileInfo $fileInfo): array
    {
        $fileErrors = [];
        if (! $servicesNode instanceof ArrayNode) {
            return [];
        }

        foreach ($servicesNode->items as $serviceItem) {
            if ($serviceItem->value instanceof EntityNode) {
                $errorMessage = $this->createErrorMessageFromNeonEntity($serviceItem->value);
                $fileErrors[] = new FileError($errorMessage, $fileInfo);
            }
        }

        return $fileErrors;
    }

    private function createErrorMessageFromNeonEntity(EntityNode $entityNode): string
    {
        $neonEntityContent = $entityNode->toString();

        return sprintf(
            'Complex entity found "%s".%sChange it to explicit syntax with named keys, that is easier to read.',
            $neonEntityContent,
            PHP_EOL,
        );
    }
}
