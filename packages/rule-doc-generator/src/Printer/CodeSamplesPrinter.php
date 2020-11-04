<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Printer;

use Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use Symplify\RuleDocGenerator\ValueObject\ConfiguredCodeSample;

final class CodeSamplesPrinter
{
    /**
     * @param CodeSampleInterface[] $codeSamples
     * @return string[]
     */
    public function print(array $codeSamples): array
    {
        $lines = [];
        foreach ($codeSamples as $codeSample) {
            $lines[] = $this->printPhpCode($codeSample->getGoodCode());
            $lines[] = ':x:';

            $lines[] = $this->printPhpCode($codeSample->getBadCode());
            $lines[] = ':+1:';

            $lines[] = '<br>';

            if ($codeSample instanceof ConfiguredCodeSample) {
                // @todo
            }
        }

        return $lines;
    }

    private function printPhpCode(string $content): string
    {
        return $this->printCodeWrapped($content, 'php');
    }

    private function printCodeWrapped(string $content, string $format): string
    {
        return sprintf('```%s%s%s%s```', $format, PHP_EOL, rtrim($content), PHP_EOL);
    }
}
