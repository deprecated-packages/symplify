<?php declare(strict_types=1);

namespace Symplify\PHP7_CodeSniffer\Configuration\OptionResolver;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symplify\PHP7_CodeSniffer\Contract\Configuration\OptionResolver\OptionResolverInterface;

final class SniffsOptionResolver implements OptionResolverInterface
{
    /**
     * @var string
     */
    private const NAME = 'sniffs';

    public function getName() : string
    {
        return self::NAME;
    }

    public function resolve(array $value) : array
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined(self::NAME);

        $values = $optionsResolver->resolve([
            self::NAME => $value
        ]);

        return $values[self::NAME];
    }
}
