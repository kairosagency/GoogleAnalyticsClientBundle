<?php

namespace Kairos\GoogleAnalyticsClientBundle\Tests\AuthClient;

use Kairos\GoogleAnalyticsClientBundle\AuthClient\P12AuthClient;

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

    /** @var \Guzzle\Http\Client */
    protected $httpClient;

    /** @var int */
    protected $cacheTTL;

    /** @var \Kairos\GoogleAnalyticsClientBundle\AuthClient\P12AuthClient */
    protected $object;

    /** Init the object App */
    protected function setUp()
    {
        $this->cacheProvider = $this->getMock('Doctrine\Common\Cache\Cache');
        $this->httpClient = $this->getMock('Guzzle\Http\Client');
        $this->baseUrl = 'https://base_url';
        $this->tokenEndPoint = '/token_end_point';
        $this->clientEmail = 'client_email';
        $this->privateKey = __DIR__ . '/Fixtures/certificate.p12';
        $this->cacheTTL = 3600;

        $this->object = new P12AuthClient($this->cacheProvider, $this->baseUrl, $this->tokenEndPoint, $this->clientEmail, $this->privateKey);
        $this->object->setHttpClient($this->httpClient);
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Doctrine\Common\Cache\Cache', $this->object->getCacheProvider());
        $this->assertSame($this->clientEmail, $this->object->getClientEmail());
        $this->assertSame($this->baseUrl . $this->tokenEndPoint, $this->object->getUrl());
        $this->assertSame($this->privateKey, $this->object->getPrivateKey());
        $this->assertInstanceOf('Guzzle\Http\Client', $this->object->getHttpClient());
        $this->assertSame($this->cacheTTL, $this->object->getCacheTTL());
    }

    public function testAccessTokenNotCached()
    {
        $this->cacheProvider->expects($this->once())
            ->method('contains')
            ->with($this->equalTo(md5($this->clientEmail)))
            ->will($this->returnValue(false));

        $this->cacheProvider->expects($this->once())
            ->method('save')
            ->with(
                $this->equalTo(md5($this->clientEmail)),
                $this->equalTo('response_access_token'),
                3600
            )
            ->will($this->returnValue('response_access_token'));

        if (!function_exists('openssl_x509_read')) {
            $this->markTestSkipped('The "openssl_x509_read" function is not available.');
        }

        $httpResponse = $this->getMockBuilder('Guzzle\Http\Message\Response')->disableOriginalConstructor()->getMock();
        $httpResponse->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue(json_encode(array('access_token' => 'response_access_token'))));

        $httpRequest = $this->getMock('Guzzle\Http\Message\RequestInterface');
        $httpRequest->expects($this->once())
            ->method('send')
            ->will($this->returnValue($httpResponse));

        $this->httpClient->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo($this->baseUrl . $this->tokenEndPoint),
                $this->equalTo(array('Content-Type' => 'application/x-www-form-urlencoded'))
            )
            ->will($this->returnValue($httpRequest));

        $this->assertSame('response_access_token', $this->object->getAccessToken());
    }

    public function testAccessTokenCached()
    {
        $this->cacheProvider->expects($this->once())
            ->method('contains')
            ->with($this->equalTo(md5($this->clientEmail)))
            ->will($this->returnValue(true));

        $this->cacheProvider->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo(md5($this->clientEmail)))
            ->will($this->returnValue('response_access_token'));

        $this->assertSame('response_access_token', $this->object->getAccessToken());
    }

    public function testAccessTokenError()
    {
        $this->cacheProvider->expects($this->once())
            ->method('contains')
            ->with($this->equalTo(md5($this->clientEmail)))
            ->will($this->returnValue(false));

        if (!function_exists('openssl_x509_read')) {
            $this->markTestSkipped('The "openssl_x509_read" function is not available.');
        } else {
            $this->setExpectedException('Kairos\GoogleAnalyticsClientBundle\Exception\GoogleAnalyticsException');
        }

        $httpResponse = $this->getMockBuilder('Guzzle\Http\Message\Response')->disableOriginalConstructor()->getMock();
        $httpResponse->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue(json_encode(array('error' => 'error'))));

        $httpRequest = $this->getMock('Guzzle\Http\Message\RequestInterface');
        $httpRequest->expects($this->once())
            ->method('send')
            ->will($this->returnValue($httpResponse));

        $this->httpClient->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo($this->baseUrl . $this->tokenEndPoint),
                $this->equalTo(array('Content-Type' => 'application/x-www-form-urlencoded'))
            )
            ->will($this->returnValue($httpRequest));

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