<?php declare(strict_types=1);

namespace Symplify\DefaultAutoconfigure;

final class ClassToTagMap
{
    /**
     * @var string[]
     */
    private static $map = [
        'Symfony\Component\Security\Core\Authorization\Voter\VoterInterface' => 'security.voter',
        'Symfony\Component\Console\Command\Command' => 'console.command',
        'Symfony\Component\Config\ResourceCheckerInterface' => 'config_cache.resource_checker',
        'Symfony\Component\DependencyInjection\ServiceSubscriberInterface' => 'container.service_subscriber',
        'Symfony\Bundle\FrameworkBundle\Controller\AbstractController' => 'controller.service_arguments',
        'Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface' => 'data_collector',
        'Symfony\Component\Form\FormTypeInterface' => 'form.type',
        'Symfony\Component\Form\FormTypeGuesserInterface' => 'form.type_guesser',
        'Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface' => 'kernel.cache_clearer',
        'Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface' => 'kernel.cache_warmer',
        'Symfony\Component\EventDispatcher\EventSubscriberInterface' => 'kernel.event_subscriber',
        'Symfony\Component\PropertyInfo\PropertyListExtractorInterface' => 'property_info.list_extractor',
        'Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface' => 'property_info.type_extractor',
        'Symfony\Component\PropertyInfo\PropertyDescriptionExtractorInterface' => 'property_info.description_extractor',
        'Symfony\Component\PropertyInfo\PropertyAccessExtractorInterface' => 'property_info.access_extractor',
        'Symfony\Component\Serializer\Encoder\EncoderInterface' => 'serializer.encoder',
        'Symfony\Component\Serializer\Normalizer\NormalizerInterface' => 'serializer.normalizer',
        'Symfony\Component\Validator\ConstraintValidatorInterface' => 'validator.constraint_validator',
        'Symfony\Component\Validator\ObjectInitializerInterface' => 'validator.initializer',
        'Twig_ExtensionInterface' => 'twig.extension',
        'Twig_LoaderInterface' => 'twig.loader',
    ];

    /**
     * @return string[]
     */
    public static function getMap(): array
    {
        return self::filterOnlyExistingClasses(self::$map);
    }

    /**
     * @param string[] $map
     * @return string[]
     */
    private static function filterOnlyExistingClasses(array $map): array
    {
        return array_filter(self::$map, function ($class) {
            return class_exists($class);
        }, ARRAY_FILTER_USE_KEY);
    }
}
