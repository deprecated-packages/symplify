<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Configuration\OptionResolver;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symplify\PHP7_CodeSniffer\Contract\Configuration\OptionResolver\OptionResolverInterface;
use Symplify\PHP7_CodeSniffer\Exception\Configuration\OptionResolver\SourceNotFoundException;

final class SourceOptionResolver implements OptionResolverInterface
{
    /**
     * @var string
     */
    const NAME = 'source';

    public function getName() : string
    {
        return self::NAME;
    }

    public function resolve(array $value) : array
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined(self::NAME);
        $this->setSourceAllowedValues($optionsResolver);

        $values = $optionsResolver->resolve([
            self::NAME => $value
        ]);

        return $values[self::NAME];
    }

    private function setSourceAllowedValues(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setAllowedValues(self::NAME, function (array $source) {
            foreach ($source as $singleSource) {
                if (!file_exists($singleSource)) {
                    throw new SourceNotFoundException(sprintf(
                        'Source "%s" does not exist.',
                        $singleSource
                    ));
                }
            }

            return true;
        });
    }
}
