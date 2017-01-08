<?php

declare(strict_types = 1);

namespace Symplify\PHP7_CodeSniffer\Configuration;

use Symplify\PHP7_CodeSniffer\Contract\Configuration\OptionResolver\OptionResolverInterface;
use Symplify\PHP7_CodeSniffer\Exception\Configuration\MissingOptionResolverException;

final class ConfigurationResolver
{
    /**
     * @var OptionResolverInterface[]
     */
    private $optionResolvers = [];

    public function addOptionResolver(OptionResolverInterface $optionResolver)
    {
        $this->optionResolvers[$optionResolver->getName()] = $optionResolver;
    }

    public function resolve(string $name, array $source) : array
    {
        $this->ensureResolverExists($name);
        return $this->optionResolvers[$name]->resolve($source);
    }

    private function ensureResolverExists(string $name)
    {
        if (!isset($this->optionResolvers[$name])) {
            throw new MissingOptionResolverException(sprintf(
                'Resolver for "%s" value is not registered. '.
                'Add it via $configurationResolver->addOptionResolver(...).',
                $name
            ));
        }
    }
}
