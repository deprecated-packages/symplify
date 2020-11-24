<?php

declare(strict_types=1);

namespace Symplify\PHPMDDecomposer\PHPMDDecomposer;

use DOMDocument;
use DOMElement;
use Nette\Utils\Strings;
use Symplify\PHPMDDecomposer\Blueprint\PHPMDToPHPStanBluerprint;
use Symplify\PHPMDDecomposer\ValueObject\Config\PHPStanConfig;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\PHPMDDecomposer\Tests\PHPMDDecomposer\PHPStanPHPMDDecomposer\PHPStanPHPMDDecomposerTest
 */
final class PHPStanConfigFactory extends AbstractConfigFactory
{
    /**
     * @var string
     */
    private const PHPMD_KEY_EXCLUDE_REGEX = 'exclude-pattern';

    /**
     * @var string
     */
    private const PHPSTAN_KEY_EXCLUDES_ANALYSE = 'excludes_analyse';

    /**
     * @var string
     * @see https://regex101.com/r/ZjOPUX/1/
     */
    private const SPLIT_BY_COMMA_REGEX = '#\,[\s+]?#';

    /**
     * @var string
     */
    private const VALUE_PLACEHOLDER = '%value%';

    /**
     * @var PHPMDToPHPStanBluerprint
     */
    private $phpMDToPHPStanBluerprint;

    public function __construct(PHPMDToPHPStanBluerprint $phpmdToPHPStanBluerprint)
    {
        $this->phpMDToPHPStanBluerprint = $phpmdToPHPStanBluerprint;
    }

    public function decompose(SmartFileInfo $phpmdFileInfo): PHPStanConfig
    {
        $domDocument = $this->createDOMDocumentFromXmlFileInfo($phpmdFileInfo);

        $phpStanConfig = new PHPStanConfig();

        $this->decorateWithExcludedPaths($domDocument, $phpStanConfig);
        $this->decorateWithRules($domDocument, $phpStanConfig);

        return $phpStanConfig;
    }

    private function decorateWithExcludedPaths(DOMDocument $domDocument, PHPStanConfig $phpStanConfig): void
    {
        $excludeNodes = $domDocument->getElementsByTagName(self::PHPMD_KEY_EXCLUDE_REGEX);
        foreach ($excludeNodes as $domNodeList) {
            $currentPHPStanConfig = new PHPStanConfig([], [
                self::PHPSTAN_KEY_EXCLUDES_ANALYSE => [$domNodeList->nodeValue],
            ]);

            $phpStanConfig->merge($currentPHPStanConfig);
        }
    }

    private function decorateWithRules(DOMDocument $domDocument, PHPStanConfig $phpStanConfig): void
    {
        $ruleNodes = $domDocument->getElementsByTagName('rule');
        foreach ($ruleNodes as $domNodeList) {
            foreach ($this->phpMDToPHPStanBluerprint->provide() as $matchToPHPStanConfig) {
                /** @var DOMElement $domNodeList */
                if (! $matchToPHPStanConfig->isMatch($domNodeList)) {
                    continue;
                }

                $this->mergeWithMatchingParameters($matchToPHPStanConfig->getConfig(), $domNodeList, $phpStanConfig);

                // covered by PHPStan native
                $phpStanConfig->merge($matchToPHPStanConfig->getConfig());

                // this rule is matched, jump to another one
                continue 2;
            }
        }
    }

    /**
     * @param mixed[] $parameterArray
     * @param mixed $resolvedValue
     * @return mixed[]
     */
    private function replaceValuePlaceholderWithValue(array $parameterArray, $resolvedValue): array
    {
        foreach ($parameterArray as $key => $value) {
            if ($value === self::VALUE_PLACEHOLDER) {
                $parameterArray[$key] = $resolvedValue;
            }

            if (is_array($value)) {
                $parameterArray[$key] = $this->replaceValuePlaceholderWithValue($value, $resolvedValue);
            }
        }

        return $parameterArray;
    }

    private function mergeWithMatchingParameters(
        PHPStanConfig $phpmdToPHPStanConfig,
        DOMElement $domElement,
        PHPStanConfig $phpStanConfig
    ): void {
        foreach ($phpmdToPHPStanConfig->getMatchingParameters() as $phpMDParameterName => $phpStanParameterName) {
            $propertyNodes = $domElement->getElementsByTagName('property');

            foreach ($propertyNodes as $domNodeList) {
                /** @var DOMElement $domNodeList */
                $nameAttribute = $domNodeList->getAttribute('name');
                if ($nameAttribute !== $phpMDParameterName) {
                    continue;
                }

                $resolvedValue = $domNodeList->getAttribute('value');

                if (Strings::match($resolvedValue, self::SPLIT_BY_COMMA_REGEX)) {
                    $resolvedValue = Strings::split($resolvedValue, self::SPLIT_BY_COMMA_REGEX);
                    $resolvedValue = array_values($resolvedValue);
                } elseif (Strings::length((string) (int) $resolvedValue) === Strings::length($resolvedValue)) {
                    // is numeric value
                    $resolvedValue = (int) $resolvedValue;
                }

                // replace %value% with current value
                $phpstanParameters = $this->replaceValuePlaceholderWithValue($phpStanParameterName, $resolvedValue);

                $currentParametersPHPStanConfig = new PHPStanConfig([], $phpstanParameters);

                // covered by PHPStan native
                $phpStanConfig->merge($currentParametersPHPStanConfig);
                break;
            }
        }
    }
}
