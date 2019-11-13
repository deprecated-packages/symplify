<?php declare(strict_types=1);

namespace Symplify\Statie\Twig;

use Iterator;
use Symplify\Statie\Exception\Configuration\ConfigurationException;
use Symplify\Statie\Generator\Renderable\File\AbstractGeneratorFile;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class GeneratorTwigExtension extends AbstractExtension
{
    /**
     * @return TwigFilter[]
     */
    public function getFilters(): Iterator
    {
        // usage in Twig: {{ post|link }}
        yield new TwigFilter('link', function ($generatorFile): string {
            $this->ensureArgumentIsGeneratorFile($generatorFile);

            /** @var AbstractGeneratorFile $generatorFile */
            return $generatorFile->getRelativeUrl();
        });
    }

    private function ensureArgumentIsGeneratorFile($value): void
    {
        if ($value instanceof AbstractGeneratorFile) {
            return;
        }

        $message = sprintf('Only "%s" can be passed to "%s" filter', AbstractGeneratorFile::class, 'link');

        if (is_object($value)) {
            $message .= ' ' . sprintf('"%s" given', get_class($value));
        } elseif (is_array($value)) {
            $message .= ' Array given';
        } elseif (is_numeric($value) || is_string($value)) {
            $message .= ' ' . sprintf('"%s" given', $value);
        }

        throw new ConfigurationException($message);
    }
}
