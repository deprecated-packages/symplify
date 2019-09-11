<?php declare(strict_types=1);

namespace Symplify\Statie\Templating\FilterProvider;

use Symplify\Statie\Contract\Templating\FilterProviderInterface;
use Symplify\Statie\Exception\Configuration\ConfigurationException;
use Symplify\Statie\Generator\Renderable\File\AbstractGeneratorFile;

final class GeneratorFilterProvider implements FilterProviderInterface
{
    /**
     * @return callable[]
     */
    public function provide(): array
    {
        return [
            // usage in Twig: {{ post|link }}
            // usage in Latte: {$post|link}
            'link' => function ($generatorFile): string {
                $this->ensureArgumentIsGeneratorFile($generatorFile);

                /** @var AbstractGeneratorFile $generatorFile */
                return $generatorFile->getRelativeUrl();
            },
        ];
    }

    /**
     * @param mixed $value
     */
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
