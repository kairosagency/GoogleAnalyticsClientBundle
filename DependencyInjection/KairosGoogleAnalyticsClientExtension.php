<?php

namespace Kairos\GoogleAnalyticsClientBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KairosGoogleAnalyticsClientExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->remapParametersNamespaces($config, $container, array(
                '' => array(
                    'gapi_id'           => 'kairos_google_analytics_client.gapi_id',
                    'gapi_account'      => 'kairos_google_analytics_client.gapi_account',
                    'cache_provider_id' => 'kairos_google_analytics_client.cache_provider_id'
                ),

                'oauth' => array(
                    'base_url'          => 'kairos_google_analytics_client.oauth.base_url',
                    'token_end_point'   => 'kairos_google_analytics_client.oauth.token_end_point',
                    'client_email'      => 'kairos_google_analytics_client.oauth.client_email',
                    'private_key'       => 'kairos_google_analytics_client.oauth.private_key',
                    'base_url_api'      => 'kairos_google_analytics_client.oauth.base_url_api'
                )
            ));

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     * @param array $map
     */
    protected function remapParameters(array $config, ContainerBuilder $container, array $map)
    {
        foreach ($map as $name => $paramName) {
            if (array_key_exists($name, $config)) {
                $container->setParameter($paramName, $config[$name]);
            }
        }
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     * @param array $namespaces
     */
    protected function remapParametersNamespaces(array $config, ContainerBuilder $container, array $namespaces)
    {
        foreach ($namespaces as $ns => $map) {
            if ($ns) {
                if (!array_key_exists($ns, $config)) {
                    continue;
                }
                $namespaceConfig = $config[$ns];
            } else {
                $namespaceConfig = $config;
            }
            if (is_array($map)) {
                $this->remapParameters($namespaceConfig, $container, $map);
            } else {
                foreach ($namespaceConfig as $name => $value) {
                    $container->setParameter(sprintf($map, $name), $value);
                }
            }
        }
    }
}
