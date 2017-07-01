<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration;

use Symfony\Component\Console\Input\InputInterface;

final class Configuration
{
    /**
     * @var bool
     */
    private $isFixer = false;

    public function isFixer(): bool
    {
        return $this->isFixer;
    }

    public function resolveFromInput(InputInterface $input): void
    {
        $this->isFixer = (bool) $input->getOption('fix');
    }

    /**
     * @param mixed[] $options
     */
    public function resolveFromArray(array $options): void
    {
        $this->isFixer = isset($options['isFixer']) && $options['isFixer'];
    }
}
