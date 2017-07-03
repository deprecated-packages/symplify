<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Console\Input;

use Nette\Utils\Strings;
use Symfony\Component\Console\Input\ArgvInput;

final class LiberalFormatArgvInput extends ArgvInput
{
    /**
     * @return mixed[]
     */
    public function getOptions(): array
    {
        $options = parent::getOptions();
        foreach ($options as $key => $value) {
            $options[$key] = $this->removeEqualsSign($value);
            $options[$key] = $this->splitByComma($value);
        }

        return $options;
    }

    public function getParameterOption($values, $default = false, $onlyParams = false)
    {
        $this->options = $this->getOptions();

        return parent::getParameterOption($name);
    }

    /**
     * @return mixed
     */
    public function getOption($name)
    {
        $this->options = $this->getOptions();

        return parent::getOption($name);
    }

    /**
     * @param array|string $value
     * @return array|string
     */
    private function removeEqualsSign($value)
    {
        if (is_array($value)) {
            array_walk($value, function (&$singleValue) {
                $singleValue = ltrim($singleValue, '=');
            });
        } else {
            $value = ltrim($value, '=');
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function splitByComma($value)
    {
        if (is_array($value)) {
            array_walk($value, function (&$singleValue) {
                $singleValue = $this->splitByCommaIfHasAny($singleValue);
            });
            if (count($value) && is_array($value[0])) {
                return $value[0];
            }
        } else {
            $value = $this->splitByCommaIfHasAny($value);
        }

        return $value;
    }

    /**
     * @return string|array
     */
    private function splitByCommaIfHasAny(string $value)
    {
        if ($this->containsComma($value)) {
            return explode(',', $value);
        }

        return $value;
    }

    private function containsComma(string $value): bool
    {
        return Strings::contains($value, ',');
    }
}
