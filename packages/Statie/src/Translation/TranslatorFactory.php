<?php declare(strict_types=1);

namespace Symplify\Statie\Translation;

use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Translation\Filesystem\ResourceFinder;

final class TranslatorFactory
{
    /**
     * @var ResourceFinder
     */
    private $resourceFinder;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration, ResourceFinder $resourceFinder)
    {
        $this->configuration = $configuration;
        $this->resourceFinder = $resourceFinder;
    }

    public function create() : TranslatorInterface
    {
        $translator = new Translator(null);
        $translator->addLoader('neon', new YamlFileLoader());
        $translator->setFallbackLocales(['cs']);

        $this->addResourcesToTranslator($translator);

        return $translator;
    }

    private function addResourcesToTranslator(Translator $translator)
    {
        foreach ($this->resourceFinder->findInDirectory($this->getTranslationDirecrory()) as $resource) {
            $translator->addResource(
                $resource['format'],
                $resource['pathname'],
                $resource['locale'],
                $resource['domain']
            );
        }
    }

    private function getTranslationDirecrory() : string
    {
        return $this->configuration->getSourceDirectory() . DIRECTORY_SEPARATOR . '_translations';
    }
}
