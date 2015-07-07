<?php

namespace Kairos\GoogleAnalyticsClientBundle\Tests\Consumer;

use Kairos\GoogleAnalyticsClientBundle\Request\GoogleAnalyticsRequest;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Kairos\GoogleAnalyticsClientBundle\Query\QueryInterface */
    protected $query;

    /** @var \Kairos\GoogleAnalyticsClientBundle\AuthProvider\AuthClientInterface */
    protected $authClient;

    /** @var \GuzzleHttp\Client */
    protected $httpClient;

    /** @var \Kairos\GoogleAnalyticsClientBundle\Request\GoogleAnalyticsRequest */
    protected $object;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->query = $this->prophesize('Kairos\GoogleAnalyticsClientBundle\Query\QueryInterface');
        $this->httpClient = $this->prophesize('GuzzleHttp\Client');
        $this->authClient = $this->prophesize('Kairos\GoogleAnalyticsClientBundle\AuthProvider\AuthClientInterface');
        $this->authClient->getAccessToken()
            ->willReturn('access_token');

        $this->object = new GoogleAnalyticsRequest($this->authClient->reveal());
    }

    public function testGetResult(){}
}
