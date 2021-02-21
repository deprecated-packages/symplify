<?php

declare(strict_types=1);

namespace Symplify\SetConfigResolver;

use Symplify\SetConfigResolver\Contract\SetProviderInterface;
use Symplify\SetConfigResolver\Exception\SetNotFoundException;
use Symplify\SetConfigResolver\ValueObject\Set;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SetResolver
{
    /**
     * @var SetProviderInterface
     */
    private $setProvider;

    public function __construct(SetProviderInterface $setProvider)
    {
        $this->setProvider = $setProvider;
    }

    public function detectFromName(string $setName): SmartFileInfo
    {
        $set = $this->setProvider->provideByName($setName);
        if (! $set instanceof Set) {
            $this->reportSetNotFound($setName);
        }

        return $set->getSetFileInfo();
    }

    private function reportSetNotFound(string $setName): void
    {
        $message = sprintf('Set "%s" was not found', $setName);
        throw new SetNotFoundException($message, $setName, $this->setProvider->provideSetNames());
    }
}
