<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\DumperFomatter;

use Nette\Utils\Strings;

final class YamlDumpFormatter
{
    public function format(string $content): string
    {
        // not sure why, but sometimes there is extra value in the start
        $content = ltrim($content);

        $content = $this->addExtraSpaceBetweenServiceDefinitions($content);
        $content = $this->clearExtraTagZeroName($content);

        return $this->replaceAnonymousIdsWithDash($content);
    }

    private function addExtraSpaceBetweenServiceDefinitions(string $content): string
    {
        // put extra empty line between service definitions, to make them better readable
        $content = Strings::replace($content, '#\n    (\w)#m', "\n\n    $1");

        // except the first line under "services:"
        return Strings::replace($content, '#services:\n\n    (\w)#m', "services:\n    $1");
    }

    private function replaceAnonymousIdsWithDash(string $content): string
    {
        return Strings::replace($content, '#(\n    )(\d+)\:#m', '$1-');
    }

    private function clearExtraTagZeroName(string $content): string
    {
        // remove pre-space in tags, kinda hacky
        return Strings::replace($content, '#- [\d]+: { (\w+):#', '- { $1:');
    }
}
