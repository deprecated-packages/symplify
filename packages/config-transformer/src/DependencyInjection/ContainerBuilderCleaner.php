<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\DependencyInjection;

use Nette\Utils\Strings;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symplify\ConfigTransformer\Configuration\Configuration;
use Symplify\ConfigTransformer\ValueObject\SymfonyVersionFeature;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;

final class ContainerBuilderCleaner
{
    /**
     * @see https://regex101.com/r/0qo8RA/1
     * @var string
     */
    private const ANONYMOUS_CLASS_REGEX = '#^[\d]+\_[\w]{64}$#';

    /**
     * @var PrivatesAccessor
     */
    private $privatesAccessor;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(PrivatesAccessor $privatesAccessor, Configuration $configuration)
    {
        $this->privatesAccessor = $privatesAccessor;
        $this->configuration = $configuration;
    }

    public function cleanContainerBuilder(ContainerBuilder $containerBuilder): void
    {
        $this->removeExplicitPrivate($containerBuilder);
        $this->removeSymfonyInternalServices($containerBuilder);
        $this->removeTemporaryAnonymousIds($containerBuilder);

        foreach ($containerBuilder->getDefinitions() as $definition) {
            $this->resolvePolyfillForNameTag($definition);
        }
    }

    private function removeSymfonyInternalServices(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->removeDefinition('service_container');
        $containerBuilder->removeAlias(PsrContainerInterface::class);
        $containerBuilder->removeAlias(ContainerInterface::class);
    }

    private function removeExplicitPrivate(ContainerBuilder $containerBuilder): void
    {
        foreach ($containerBuilder->getDefinitions() as $definition) {
            // remove public: false, by default
            if ($definition->isPublic()) {
                continue;
            }

            $definition->setPrivate(true);
        }
    }

    private function removeTemporaryAnonymousIds(ContainerBuilder $containerBuilder): void
    {
        $definitions = $this->privatesAccessor->getPrivateProperty($containerBuilder, 'definitions');

        foreach ($definitions as $name => $definition) {
            if (! is_string($name)) {
                continue;
            }

            if (! $this->isGeneratedKeyForAnonymousClass($name)) {
                continue;
            }

            unset($definitions[$name]);
            $definitions[] = $definition;
        }

        $this->privatesAccessor->setPrivateProperty($containerBuilder, 'definitions', $definitions);
    }

    private function isGeneratedKeyForAnonymousClass(string $name): bool
    {
        return (bool) Strings::match($name, self::ANONYMOUS_CLASS_REGEX);
    }

    private function resolvePolyfillForNameTag(Definition $definition): void
    {
        if ($definition->getTags() === []) {
            return;
        }

        $tags = $definition->getTags();
        foreach ($definition->getTags() as $name => $value) {
            /** @var mixed[] $tagValues */
            $tagValues = $value[0];
            if ($this->shouldSkipNameTagInlining($tagValues)) {
                continue;
            }

            unset($tags[$name]);

            $tagValues = [];
            foreach ($value as $singleValue) {
                $singleTag = array_merge([
                    'name' => $name,
                ], $singleValue);
                $tagValues[] = $singleTag;
            }

            $tags[] = $tagValues;
        }

        $definition->setTags($tags);
    }

    private function shouldSkipNameTagInlining(array $tagValues): bool
    {
        if (count($tagValues) !== 0) {
            return false;
        }

        return $this->configuration->isAtLeastSymfonyVersion(SymfonyVersionFeature::TAGS_WITHOUT_NAME);
    }
}
