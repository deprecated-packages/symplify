<?php

declare(strict_types=1);

namespace Symplify\Amnesia\ValueObject\Symfony;

/**
 * @api
 * @see https://symfony.com/doc/current/reference/dic_tags.html
 */
final class ServiceTag
{
    /**
     * @var string
     */
    public const AUTO_ALIAS = 'auto_alias';

    /**
     * @var string
     */
    public const CONSOLE_COMMAND = 'console.command';

    /**
     * @var string
     */
    public const CONTAINER_HOT_PATH = 'container.hot_path';

    /**
     * @var string
     */
    public const CONTAINER_NO_PRELOAD = 'container.no_preload';

    /**
     * @var string
     */
    public const CONTAINER_PRELOAD = 'container.preload';

    /**
     * @var string
     */
    public const CONTROLLER_ARGUMENT_VALUE_RESOLVER = 'controller.argument_value_resolver';

    /**
     * @var string
     */
    public const DATA_COLLECTOR = 'data_collector';

    /**
     * @var string
     */
    public const DOCTRINE_EVENT_LISTENER = 'doctrine.event_listener';

    /**
     * @var string
     */
    public const DOCTRINE_EVENT_SUBSCRIBER = 'doctrine.event_subscriber';

    /**
     * @var string
     */
    public const FORM_TYPE = 'form.type';

    /**
     * @var string
     */
    public const FORM_TYPE_EXTENSION = 'form.type_extension';

    /**
     * @var string
     */
    public const FORM_TYPE_GUESSER = 'form.type_guesser';

    /**
     * @var string
     */
    public const KERNEL_CACHE_CLEARER = 'kernel.cache_clearer';

    /**
     * @var string
     */
    public const KERNEL_CACHE_WARMER = 'kernel.cache_warmer';

    /**
     * @var string
     */
    public const KERNEL_EVENT_LISTENER = 'kernel.event_listener';

    /**
     * @var string
     */
    public const KERNEL_EVENT_SUBSCRIBER = 'kernel.event_subscriber';

    /**
     * @var string
     */
    public const KERNEL_FRAGMENT_RENDERER = 'kernel.fragment_renderer';

    /**
     * @var string
     */
    public const KERNEL_RESET = 'kernel.reset';

    /**
     * @var string
     */
    public const MIME_MIME_TYPE_GUESSER = 'mime.mime_type_guesser';

    /**
     * @var string
     */
    public const MONOLOG_LOGGER = 'monolog.logger';

    /**
     * @var string
     */
    public const MONOLOG_PROCESSOR = 'monolog.processor';

    /**
     * @var string
     */
    public const ROUTING_LOADER = 'routing.loader';

    /**
     * @var string
     */
    public const ROUTING_EXPRESSION_LANGUAGE_PROVIDER = 'routing.expression_language_provider';

    /**
     * @var string
     */
    public const SECURITY_EXPRESSION_LANGUAGE_PROVIDER = 'security.expression_language_provider';

    /**
     * @var string
     */
    public const SECURITY_REMEMBER_ME_AWARE = 'security.remember_me_aware';

    /**
     * @var string
     */
    public const SECURITY_VOTER = 'security.voter';

    /**
     * @var string
     */
    public const SERIALIZER_ENCODER = 'serializer.encoder';

    /**
     * @var string
     */
    public const SERIALIZER_NORMALIZER = 'serializer.normalizer';

    /**
     * @var string
     */
    public const SWIFTMAILER_DEFAULT_PLUGIN = 'swiftmailer.default.plugin';

    /**
     * @var string
     */
    public const TRANSLATION_LOADER = 'translation.loader';

    /**
     * @var string
     */
    public const TRANSLATION_EXTRACTOR = 'translation.extractor';

    /**
     * @var string
     */
    public const TRANSLATION_DUMPER = 'translation.dumper';

    /**
     * @var string
     */
    public const TWIG_EXTENSION = 'twig.extension';

    /**
     * @var string
     */
    public const TWIG_LOADER = 'twig.loader';

    /**
     * @var string
     */
    public const TWIG_RUNTIME = 'twig.runtime';

    /**
     * @var string
     */
    public const VALIDATOR_CONSTRAINT_VALIDATOR = 'validator.constraint_validator';

    /**
     * @var string
     */
    public const VALIDATOR_INITIALIZER = 'validator.initializer';

    /**
     * @var string
     */
    public const KNP_MENU_MENU_BUILDER = 'knp_menu.menu_builder';
}
