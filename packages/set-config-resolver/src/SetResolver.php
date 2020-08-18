<?php

declare(strict_types=1);

namespace Symplify\SetConfigResolver;

use Symfony\Component\Console\Input\InputInterface;
use Symplify\SetConfigResolver\Console\Option\OptionName;
use Symplify\SetConfigResolver\Console\OptionValueResolver;
use Symplify\SetConfigResolver\Contract\SetProviderInterface;
use Symplify\SetConfigResolver\Exception\SetNotFoundException;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SetResolver
{
    /**
     * @var OptionValueResolver
     */
    private $optionValueResolver;

    /**
     * @var SetProviderInterface
     */
    private $setProvider;

    public function __construct(SetProviderInterface $setProvider)
    {
        $this->optionValueResolver = new OptionValueResolver();
        $this->setProvider = $setProvider;
    }

    public function detectFromInput(InputInterface $input): ?SmartFileInfo
    {
        $setName = $this->optionValueResolver->getOptionValue($input, OptionName::SET);
        if ($setName === null) {
            return null;
        }

        return $this->detectFromName($setName);
    }

    public function detectFromName(string $setName): SmartFileInfo
    {
        $set = $this->setProvider->provideByName($setName);
        if ($set === null) {
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
