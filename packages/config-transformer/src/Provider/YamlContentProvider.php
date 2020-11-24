<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Provider;

use Migrify\MigrifyKernel\Exception\ShouldNotHappenException;
use Symplify\PhpConfigPrinter\Contract\YamlFileContentProviderInterface;

final class YamlContentProvider implements YamlFileContentProviderInterface
{
    /**
     * @var string|null
     */
    private $yamlContent;

    public function setContent(string $yamlContent): void
    {
        $this->yamlContent = $yamlContent;
    }

    public function getYamlContent(): string
    {
        if ($this->yamlContent === null) {
            throw new ShouldNotHappenException();
        }

        return $this->yamlContent;
    }
}
