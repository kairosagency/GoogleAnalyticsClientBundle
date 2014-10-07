<?php

namespace Kairos\GoogleAnalyticsClientBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kairos_google_analytics_client');

        $rootNode
            ->children()
                ->scalarNode('gapi_id')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end()

            ->children()
                ->scalarNode('gapi_account')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end()

            ->children()
                ->arrayNode('oauth')
                    ->addDefaultsIfNotSet()->canBeUnset()
                    ->children()
                        ->scalarNode('base_url')->defaultValue('https://accounts.google.com')->end()
                        ->scalarNode('token_end_point')->defaultValue('/o/oauth2/token')->end()
                        ->scalarNode('client_email')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('private_key')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('base_url_api')->defaultValue('https://www.googleapis.com/analytics/v3/data/ga')->end()
                    ->end()
                ->end()
            ->end()

            ->children()
                ->scalarNode('cache_provider')->end()
            ->end()

        ->end();

        return $treeBuilder;
    }
}
