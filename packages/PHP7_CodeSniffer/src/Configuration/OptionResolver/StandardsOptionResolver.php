<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Configuration\OptionResolver;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symplify\PHP7_CodeSniffer\Configuration\ValueNormalizer;
use Symplify\PHP7_CodeSniffer\Contract\Configuration\OptionResolver\OptionResolverInterface;
use Symplify\PHP7_CodeSniffer\Exception\Configuration\OptionResolver\StandardNotFoundException;
use Symplify\PHP7_CodeSniffer\Standard\Finder\StandardFinder;

final class StandardsOptionResolver implements OptionResolverInterface
{
    /**
     * @var string
     */
    const NAME = 'standards';

    /**
     * @var StandardFinder
     */
    private $standardFinder;

    public function __construct(StandardFinder $standardFinder)
    {
        $this->standardFinder = $standardFinder;
    }

    public function getName() : string
    {
        return self::NAME;
    }

    public function resolve(array $value) : array
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined(self::NAME);
        $this->setAllowedValues($optionsResolver);
        $this->setNormalizer($optionsResolver);

        $values = $optionsResolver->resolve([
            self::NAME => $value
        ]);

        return $values[self::NAME];
    }

    private function setNormalizer(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setNormalizer(
            self::NAME,
            function (OptionsResolver $optionsResolver, array $standardNames) {
                return ValueNormalizer::normalizeCommaSeparatedValues($standardNames);
            }
        );
    }

    private function setAllowedValues(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setAllowedValues(self::NAME, function (array $standards) {
            $standards = ValueNormalizer::normalizeCommaSeparatedValues($standards);

            $availableStandards = $this->standardFinder->getStandards();
            foreach ($standards as $standardName) {
                if (!array_key_exists($standardName, $availableStandards)) {
                    throw new StandardNotFoundException(sprintf(
                        'Standard "%s" is not supported. Pick one of: %s.',
                        $standardName,
                        implode(array_keys($availableStandards), ', ')
                    ));
                }
            }

            return true;
        });
    }
}
