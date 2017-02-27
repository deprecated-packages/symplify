<?php declare(strict_types=1);

namespace Zenify\DoctrineBehaviors\Entities\Attributes;

use Knp\DoctrineBehaviors\Model\Translatable\Translatable;

/**
 * @method Translatable proxyCurrentLocaleTranslation($method, $args = [])
 */
trait TranslatableTrait
{
    /**
     * @param string $method
     * @param mixed[] $arguments
     * @return mixed
     */
    public function __call(string $method, array $arguments)
    {
        return $this->proxyCurrentLocaleTranslation($method, $arguments);
    }

    /**
     * @param string
     * @return mixed
     */
    public function &__get(string $name) // "&" intentionally due to compatibility with Nette\Object
    {
        $prefix = 'get';
        if (preg_match('/^(is|has|should)/i', $name)) {
            $prefix = '';
        }

        $methodName = $prefix . ucfirst($name);

        if (property_exists($this, $name) === false && method_exists($this, $methodName) === false) {
            $value = $this->proxyCurrentLocaleTranslation($methodName);
            // variable $value intentionally, due to & compatibility
            return $value;
        }

        if (method_exists($this, $methodName)) {
            $value = $this->$methodName();
            return $value;
        }

        return $this->$name;
    }
}
