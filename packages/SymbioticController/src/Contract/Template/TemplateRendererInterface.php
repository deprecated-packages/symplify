<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Contract\Template;

interface TemplateRendererInterface
{
    /**
     * @param string $file
     * @param mixed[] $parameters
     */
    public function renderFileWithParameters(string $file, array $parameters = []): string;
}
