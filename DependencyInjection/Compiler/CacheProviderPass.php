<?php

namespace Kairos\GoogleAnalyticsClientBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Class CacheProviderPass
 * @package Kairos\GoogleAnalyticsClientBundle\DependencyInjection\Compiler
 */
class CacheProviderPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        /**
         * If there is a cache provider in the config file so we set an alias to this cache provider
         * in order to redirect the custom cache provider to our service name
         */
        if ($container->hasParameter('kairos_google_analytics_client.cache_provider_id')) {
            $customCacheProvider = $container->getParameter('kairos_google_analytics_client.cache_provider_id');
            $container->setAlias('kairos_google_analytics_client.cache_provider', $customCacheProvider);
            return;
        }

        /** If there is no custom cache provider in the config we init a default cache provider */
        $defaultCacheProvider = new Definition('%kairos_google_analytics_client.default_cache_provider.class%', array($container->getParameter('%kernel.root_dir%') . '/cache'));
        $container->setDefinition('kairos_google_analytics_client.cache_provider', $defaultCacheProvider);
    }
}
