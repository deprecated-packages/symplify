<?php

declare(strict_types=1);

namespace Symplify\EasyCI\SymfonyNameToTypeService;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use SimpleXMLElement;
use Symplify\EasyCI\SymfonyNameToTypeService\Exception\LoadingXmlException;

/**
 * @see \Symplify\EasyCI\Tests\SymfonyNameToTypeService\XmlServiceMapFactory\XmlServiceMapFactoryTest
 */
final class XmlServiceMapFactory
{
    /**
     * @see https://regex101.com/r/1LCxdk/1
     * @var string
     */
    private const SYMFONY_NAMESPACE_REGEX = '#^(Assetic|Webfactory|Symfony|Twig|FOS|SmokedTwigRenderer|Monolog|JMS|Sensio|Doctrine)\\\\#';

    /**
     * @return array<string, string>
     */
    public function create(string $containerXmlFile): array
    {
        $containerServicesXml = FileSystem::read($containerXmlFile);
        $xml = simplexml_load_string($containerServicesXml);
        if ($xml === false) {
            throw new LoadingXmlException();
        }

        $serviceTypesByName = [];

        foreach ($xml->services->service as $def) {
            /** @var SimpleXMLElement $attrs */
            $attrs = $def->attributes();
            if (! (property_exists($attrs, 'id') && $attrs->id !== null)) {
                continue;
            }

            $name = (string) $attrs->id;
            $type = (string) $attrs->class;

            if ($this->shouldSkipType($type)) {
                continue;
            }

            $serviceTypesByName[$name] = $type;
        }

        return $serviceTypesByName;
    }

    private function shouldSkipType(string $type): bool
    {
        if ($type === '') {
            return true;
        }

        // skip core services, we won't touch those
        return (bool) Strings::match($type, self::SYMFONY_NAMESPACE_REGEX);
    }
}
