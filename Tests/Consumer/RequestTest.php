<?php

namespace Kairos\GoogleAnalyticsClientBundle\Tests\Consumer;

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

    public function testGetResult()
    {

    }
}