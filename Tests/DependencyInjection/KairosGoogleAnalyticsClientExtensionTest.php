<?php

namespace Kairos\GoogleAnalyticsClientBundle\Tests\DependencyInjection;

use Kairos\GoogleAnalyticsClientBundle\DependencyInjection\KairosGoogleAnalyticsClientExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
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
        $this->createEmptyConfiguration();

        $this->assertParameter('https://accounts.google.com', 'kairos_google_analytics_client.oauth.base_url');
        $this->assertParameter('/o/oauth2/token', 'kairos_google_analytics_client.oauth.token_end_point');
        $this->assertParameter('no_email', 'kairos_google_analytics_client.oauth.client_email');
        $this->assertParameter('no_private_key', 'kairos_google_analytics_client.oauth.private_key');
        $this->assertParameter('https://www.googleapis.com/analytics/v3/data/ga', 'kairos_google_analytics_client.oauth.base_url_api');
    }

    public function testAnalyticsLoadCacheProviderValid()
    {
        $this->createFullConfiguration();
        $this->assertParameter('cache_provider_service', 'kairos_google_analytics_client.cache_provider_id');
    }

    public function testAnalyticsLoadOAuthFullValid()
    {
        $this->createFullConfiguration();

        $this->assertParameter('foo@bar.com', 'kairos_google_analytics_client.oauth.client_email');
        $this->assertParameter('private_key', 'kairos_google_analytics_client.oauth.private_key');
    }

    public function testAnalyticsLoadGapi()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('no_id', 'kairos_google_analytics_client.gapi_id');
        $this->assertParameter('no_id', 'kairos_google_analytics_client.gapi_account');
    }

    public function testAnalyticsLoadGapiFull()
    {
        $this->createFullConfiguration();

        $this->assertParameter('111', 'kairos_google_analytics_client.gapi_id');
        $this->assertParameter('111', 'kairos_google_analytics_client.gapi_account');
    }

    protected function createEmptyConfiguration()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new KairosGoogleAnalyticsClientExtension();
        $config = $this->getEmptyConfig();
        $loader->load(array($config), $this->configuration);
        $this->assertTrue($this->configuration instanceof ContainerBuilder);
    }

    protected function createFullConfiguration()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new KairosGoogleAnalyticsClientExtension();
        $config = $this->getFullConfig();
        $loader->load(array($config), $this->configuration);
        $this->assertTrue($this->configuration instanceof ContainerBuilder);
    }

    /**
     * getEmptyConfig
     *
     * @return array
     */
    protected function getEmptyConfig()
    {
        $yaml = <<<EOF
gapi_id: no_id
gapi_account: no_id
oauth:
    client_email: no_email
    private_key: no_private_key
EOF;
        $parser = new Parser();
        return $parser->parse($yaml);
    }

    protected function getFullConfig()
    {
        $yaml = <<<EOF
gapi_id: 111
gapi_account: 111
oauth:
    base_url: https://accounts.google.com
    token_end_point: /o/oauth2/token
    client_email: foo@bar.com
    private_key: private_key
    base_url_api: https://www.googleapis.com/analytics/v3/data/ga
cache_provider_id: cache_provider_service
EOF;
        $parser = new Parser();
        return  $parser->parse($yaml);
    }

    /**
     * @param mixed  $value
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
