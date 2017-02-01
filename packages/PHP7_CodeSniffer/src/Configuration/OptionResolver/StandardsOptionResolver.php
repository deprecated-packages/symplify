<?php declare(strict_types=1);

namespace Symplify\PHP7_CodeSniffer\Configuration\OptionResolver;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symplify\PHP7_CodeSniffer\Contract\Configuration\OptionResolver\OptionResolverInterface;
use Symplify\PHP7_CodeSniffer\Exception\Configuration\OptionResolver\StandardNotFoundException;

final class StandardsOptionResolver implements OptionResolverInterface
{
    /**
     * @var string
     */
    const NAME = 'standards';

    public function getName() : string
    {
        return self::NAME;
    }

    public function resolve(array $value) : array
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined(self::NAME);
        $this->setAllowedValues($optionsResolver);

        $values = $optionsResolver->resolve([
            self::NAME => $value
        ]);

        return $values[self::NAME];
    }

    private function setAllowedValues(OptionsResolver $optionsResolver) : void
    {
        $optionsResolver->setAllowedValues(self::NAME, function (array $standards) {
            dump($standards);
            die;

            // todo: use sniff group provider
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
