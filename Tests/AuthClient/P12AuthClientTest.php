<?php

namespace Kairos\GoogleAnalyticsClientBundle\Tests\AuthClient;

use Kairos\GoogleAnalyticsClientBundle\AuthProvider\P12AuthClient;
use Prophecy\Argument;

/**
 * Class P12AuthClientTest
 * @package Kairos\GoogleAnalyticsClientBundle\Tests
 */
class P12AuthClientTest extends \PHPUnit_Framework_TestCase
{
    /** @var */
    protected $cacheProvider;

    /** @var string */
    protected $baseUrl;

    /** @var string */
    protected $tokenEndPoint;

    /** @var string */
    protected $clientEmail;

    /** @var p12 file */
    protected $privateKey;

    /** @var \GuzzleHttp\Client */
    protected $httpClient;

    /** @var int */
    protected $cacheTTL;

    /** @var \Kairos\GoogleAnalyticsClientBundle\AuthProvider\P12AuthClient */
    protected $object;

    /** Init the object App */
    protected function setUp()
    {
        $this->cacheProvider = $this->prophesize('Doctrine\Common\Cache\Cache');
        $this->httpClient = $this->prophesize('GuzzleHttp\Client');
        $this->baseUrl = 'https://base_url';
        $this->tokenEndPoint = '/token_end_point';
        $this->clientEmail = 'client_email';
        $this->privateKey = __DIR__ . '/Fixtures/certificate.p12';
        $this->cacheTTL = 3600;

        $this->object = new P12AuthClient($this->cacheProvider->reveal(), $this->baseUrl, $this->tokenEndPoint, $this->clientEmail, $this->privateKey);
        $this->object->setHttpClient($this->httpClient->reveal());
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Doctrine\Common\Cache\Cache', $this->object->getCacheProvider());
        $this->assertSame($this->clientEmail, $this->object->getClientEmail());
        $this->assertSame($this->baseUrl . $this->tokenEndPoint, $this->object->getUrl());
        $this->assertSame($this->privateKey, $this->object->getPrivateKey());
        $this->assertInstanceOf('GuzzleHttp\Client', $this->object->getHttpClient());
        $this->assertSame($this->cacheTTL, $this->object->getCacheTTL());
    }

    public function testAccessTokenNotCached()
    {
        $this->cacheProvider->contains(md5($this->clientEmail))
            ->shouldBeCalled()
            ->willReturn(false);

        $this->cacheProvider->save(md5($this->clientEmail), 'response_access_token', 3600)
            ->shouldBeCalled()
            ->willReturn('response_access_token');

        if (!function_exists('openssl_x509_read')) {
            $this->markTestSkipped('The "openssl_x509_read" function is not available.');
        }

        $httpResponse = $this->prophesize('Psr\Http\Message\ResponseInterface');
        $httpResponse->getBody()->shouldBeCalled()
            ->willReturn(json_encode(array('access_token' => 'response_access_token')));

        $this->httpClient->post($this->baseUrl . $this->tokenEndPoint, Argument::type('array'))
            ->willReturn($httpResponse);

        $this->assertSame('response_access_token', $this->object->getAccessToken());
    }

    public function testAccessTokenCached()
    {
        $this->cacheProvider->contains(md5($this->clientEmail))
            ->shouldBeCalled()
            ->willReturn(true);

        $this->cacheProvider->fetch(md5($this->clientEmail))
            ->shouldBeCalled()
            ->willReturn('response_access_token');

        $this->assertSame('response_access_token', $this->object->getAccessToken());
    }

    public function testAccessTokenError()
    {
        $this->cacheProvider->contains(md5($this->clientEmail))
            ->shouldBeCalled()
            ->willReturn(false);

        if (!function_exists('openssl_x509_read')) {
            $this->markTestSkipped('The "openssl_x509_read" function is not available.');
        } else {
            $this->setExpectedException('Kairos\GoogleAnalyticsClientBundle\Exception\GoogleAnalyticsException');
        }

        $httpResponse = $this->prophesize('Psr\Http\Message\ResponseInterface');
        $httpResponse->getBody()->shouldBeCalled()
            ->willReturn(json_encode(array('error' => 'error')));

        $this->httpClient->post($this->baseUrl . $this->tokenEndPoint, Argument::type('array'))
            ->willReturn($httpResponse);

        $this->object->getAccessToken();
    }

    /**
     * @expectedException \Kairos\GoogleAnalyticsClientBundle\Exception\GoogleAnalyticsException
     */
    public function testInvalidPrivateKeyFile()
    {
        $this->object->setPrivateKey('/foo.p12');
        $this->object->getAccessToken();
    }

    /**
     * @expectedException \Kairos\GoogleAnalyticsClientBundle\Exception\GoogleAnalyticsException
     */
    public function testInvalidPkcs12Format()
    {
        $this->object->setPrivateKey(__DIR__.'/Fixtures/invalid_format.p12');
        $this->object->getAccessToken();
    }

    protected function tearDown()
    {
        $this->cacheProvider = null;
        $this->httpClient = null;
        $this->baseUrl = null;
        $this->tokenEndPoint = null;
        $this->clientEmail = null;
        $this->privateKey = null;
        $this->cacheTTL = null;
    }
}
