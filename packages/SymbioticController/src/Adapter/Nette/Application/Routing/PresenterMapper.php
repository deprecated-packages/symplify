<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Adapter\Nette\Application\Routing;

use Nette\InvalidStateException;

final class PresenterMapper
{
    /**
     * @var string
     */
    private const VALID_MASK_PATTERN = '#^\\\\?([\w\\\\]*\\\\)?(\w*\*\w*?\\\\)?([\w\\\\]*\*\w*)\z#';

    /**
     * @var string[][] of module => splited mask
     */
    private $mapping = [
        '*' => ['', '*Module\\', '*Presenter'],
        'Nette' => ['NetteModule\\', '*\\', '*Presenter'],
    ];

    /**
     * @param string[][] $mapping
     */
    public function setMapping(array $mapping): void
    {
        foreach ($mapping as $module => $mask) {
            if (is_string($mask)) {
                $this->setSingleMask($module, $mask);
            } elseif (is_array($mask) && count($mask) === 3) {
                $this->setArrayMask($module, $mask);
            } else {
                throw new InvalidStateException(sprintf(
                    'Invalid mapping mask "%s".',
                    $mask
                ));
            }
        }
    }

    public function detectPresenterClassFromPresenterName(string $presenterName): string
    {
        $parts = explode(':', $presenterName);
        $mapping = isset($parts[1], $this->mapping[$parts[0]])
            ? $this->mapping[array_shift($parts)]
            : $this->mapping['*'];

        while ($part = array_shift($parts)) {
            $mapping[0] .= str_replace('*', $part, $mapping[$parts ? 1 : 2]);
        }

        return $mapping[0];
    }

    private function setSingleMask(string $module, string $mask): void
    {
        if (! preg_match(self::VALID_MASK_PATTERN, $mask, $matches)) {
            throw new InvalidStateException(sprintf(
                'Invalid mapping mask "%s".',
                $mask
            ));
        }

        $this->mapping[$module] = [$matches[1], $matches[2] ?: '*Module\\', $matches[3]];
    }

    /**
     * @param string $module
     * @param string[] $mask
     */
    private function setArrayMask(string $module, array $mask): void
    {
        $this->mapping[$module] = [$mask[0] ? $mask[0] . '\\' : '', $mask[1] . '\\', $mask[2]];
    }
}
