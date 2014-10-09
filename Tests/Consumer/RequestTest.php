<?php

namespace Kairos\GoogleAnalyticsClientBundle\Tests;

use Kairos\GoogleAnalyticsClientBundle\Consumer\Request;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Kairos\GoogleAnalyticsClientBundle\Consumer\QueryInterface */
    protected $query;

    /** @var \Kairos\GoogleAnalyticsClientBundle\AuthClient\AuthClientInterface */
    protected $authClient;

    /** @var \Guzzle\Http\Client */
    protected $httpClient;

    /** @var \Kairos\GoogleAnalyticsClientBundle\Consumer\Request */
    protected $object;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->query = $this->getMock('Kairos\GoogleAnalyticsClientBundle\Consumer\QueryInterface');
        $this->httpClient = $this->getMock('Guzzle\Http\Client');

        $this->authClient = $this->getMock('Kairos\GoogleAnalyticsClientBundle\AuthClient\AuthClientInterface');
        $this->authClient->expects($this->once())
            ->method('getAccessToken')
            ->will($this->returnValue('access_token'));

        $this->object = new Request($this->query, $this->authClient);
        $this->object->setHttpClient($this->httpClient);
    }

    public function test()
    {}

    /*public function testGetResult()
    {
        $urls = array('https://url1');
        $this->query->expects($this->once())
            ->method('build')
            ->will($this->returnValue($urls));



        $httpResponse = $this->getMockBuilder('Guzzle\Http\Message\Response')->disableOriginalConstructor()->getMock();
        $httpResponse->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue(json_encode(array('access_token' => 'response_access_token'))));

        $httpRequest = $this->getMock('Guzzle\Http\Message\RequestInterface');
        $httpRequest->expects($this->once())
            ->method('send')
            ->will($this->returnValue($httpResponse));

        $this->httpClient->expects($this->once())
            ->method('get')
            ->with('https://url1')
            ->will($this->returnValue($httpRequest));

    }*/
}