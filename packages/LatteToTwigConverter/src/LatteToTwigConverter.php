<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symplify\LatteToTwigConverter\Contract\CaseConverter\CaseConverterInterface;

final class LatteToTwigConverter
{
    /**
     * @var CaseConverterInterface[]
     */
    private $caseConverters = [];

    /**
     * @param CaseConverterInterface[] $caseConverters
     */
    public function __construct(array $caseConverters = [])
    {
        $this->caseConverters = $caseConverters;
    }

    public function convertFile(string $file): string
    {
        $content = FileSystem::read($file);

        foreach ($this->caseConverters as $caseConverter) {
            $content = $caseConverter->convertContent($content);
        }

        // suffix: "_snippets/menu.latte" => "_snippets/menu.twig"
        $content = Strings::replace($content, '#([\w/"]+).latte#', '$1.twig');

        return Strings::replace($content, '#{% include \'?(\w+)\'? %}#', '{{ block(\'$1\') }}');
    }
}
