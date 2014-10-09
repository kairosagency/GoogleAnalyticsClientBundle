<?php

namespace Kairos\GoogleAnalyticsClientBundle\Tests\DependencyInjection;

use Kairos\GoogleAnalyticsClientBundle\DependencyInjection\Compiler\CacheProviderPass;
use Kairos\GoogleAnalyticsClientBundle\DependencyInjection\KairosGoogleAnalyticsClientExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Yaml\Parser;

class KairosGoogleAnalyticsClientExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected $configuration;

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testAnalyticsLoadThrowsExceptionUnlessGapiIdSet()
    {
        $loader = new KairosGoogleAnalyticsClientExtension();
        $config = $this->getEmptyConfig();
        unset($config['gapi_id']);
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testAnalyticsLoadThrowsExceptionUnlessGapiAccountSet()
    {
        $loader = new KairosGoogleAnalyticsClientExtension();
        $config = $this->getEmptyConfig();
        unset($config['gapi_account']);
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testAnalyticsLoadThrowsExceptionUnlessClientEmailSet()
    {
        $loader = new KairosGoogleAnalyticsClientExtension();
        $config = $this->getEmptyConfig();
        unset($config['oauth']['client_email']);
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testAnalyticsLoadThrowsExceptionUnlessPrivateKeySet()
    {
        $loader = new KairosGoogleAnalyticsClientExtension();
        $config = $this->getEmptyConfig();
        unset($config['oauth']['private_key']);
        $loader->load(array($config), new ContainerBuilder());
    }

    public function testAnalyticsLoadOAuthValid()
    {
        $this->configuration = $this->getContainer($this->getEmptyConfig());
        $this->assertParameter('https://accounts.google.com', 'kairos_google_analytics_client.oauth.base_url');
        $this->assertParameter('/o/oauth2/token', 'kairos_google_analytics_client.oauth.token_end_point');
        $this->assertParameter('no_email', 'kairos_google_analytics_client.oauth.client_email');
        $this->assertParameter('no_private_key', 'kairos_google_analytics_client.oauth.private_key');
        $this->assertParameter('https://www.googleapis.com/analytics/v3/data/ga', 'kairos_google_analytics_client.oauth.base_url_api');
    }

    public function testAnalyticsLoadOAuthFullValid()
    {
        $this->configuration = $this->getContainer($this->getFullConfig());
        $this->assertParameter('foo@bar.com', 'kairos_google_analytics_client.oauth.client_email');
        $this->assertParameter('private_key', 'kairos_google_analytics_client.oauth.private_key');
    }

    public function testAnalyticsLoadGapi()
    {
        $this->configuration = $this->getContainer($this->getEmptyConfig());
        $this->assertParameter('no_id', 'kairos_google_analytics_client.gapi_id');
        $this->assertParameter('no_id', 'kairos_google_analytics_client.gapi_account');
    }

    public function testAnalyticsLoadGapiFull()
    {
        $this->configuration = $this->getContainer($this->getFullConfig());
        $this->assertParameter('111', 'kairos_google_analytics_client.gapi_id');
        $this->assertParameter('111', 'kairos_google_analytics_client.gapi_account');
    }

    public function testAnalyticsLoadCacheProviderValid()
    {
        $this->configuration = $this->getContainer($this->getFullConfig());
        $this->assertParameter('custom_service', 'kairos_google_analytics_client.cache_provider_id');
    }

    public function testCacheProviderDefault()
    {
        $container = $this->getContainer($this->getEmptyConfig());
        $cacheProvider = $container->get('kairos_google_analytics_client.cache_provider');
        $this->assertInstanceOf('Doctrine\Common\Cache\PhpFileCache', $cacheProvider);
    }

    public function testCacheProviderCustom()
    {
        $container = $this->getContainer($this->getFullConfig());
        $cacheProvider = $container->get('kairos_google_analytics_client.cache_provider');
        $this->assertInstanceOf('Symfony\Component\Yaml\Parser', $cacheProvider);
    }

    /**
     * Gets an empty config
     *
     * @return array
     */
    protected function getEmptyConfig()
    {
        $yaml = <<<EOF
kairos_google_analytics_client:
    gapi_id: no_id
    gapi_account: no_id
    oauth:
        client_email: no_email
        private_key: no_private_key
EOF;
        $parser = new Parser();
        return $parser->parse($yaml);
    }

    /**
     * Gets a full config
     *
     * @return mixed
     */
    protected function getFullConfig()
    {
        $yaml = <<<EOF
kairos_google_analytics_client:
    gapi_id: 111
    gapi_account: 111
    oauth:
        base_url: https://accounts.google.com
        token_end_point: /o/oauth2/token
        client_email: foo@bar.com
        private_key: private_key
        base_url_api: https://www.googleapis.com/analytics/v3/data/ga
    cache_provider_id: custom_service
EOF;
        $parser = new Parser();
        return  $parser->parse($yaml);
    }

    private function getContainer(array $config = null)
    {
        $container = new ContainerBuilder(new ParameterBag(array(
            'kernel.debug'       => false,
            'kernel.bundles'     => array('KairosCacheBundle' => 'Kairos\GoogleAnalyticsClientBundle\KairosGoogleAnalyticsClientBundle'),
            'kernel.environment' => 'test',
            'kernel.root_dir'    => __DIR__.'/../../' // src dir
        )));

        $container->setDefinition('custom_service', new Definition('Symfony\Component\Yaml\Parser'));

        $extension = new KairosGoogleAnalyticsClientExtension();
        $container->registerExtension($extension);

        if(is_null($config)) {
            $config = $this->getEmptyConfig();
        }

        $extension->load($config, $container);

        $container->addCompilerPass(new CacheProviderPass());
        $container->compile();

        return $container;
    }

    /**
     * @param mixed  $value
     *
     * @param string $key
     */
    private function assertParameter($value, $key)
    {
        $this->assertEquals($value, $this->configuration->getParameter($key), sprintf('%s parameter is correct', $key));
    }

    protected function tearDown()
    {
        unset($this->configuration);
    }
}
