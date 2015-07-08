<?php

namespace Kairos\GoogleAnalyticsClientBundle\Tests\Consumer;

use Kairos\GoogleAnalyticsClientBundle\Consumer\Request;
use Prophecy\Argument;

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
        $this->query = $this->prophesize('Kairos\GoogleAnalyticsClientBundle\Consumer\QueryInterface');
        $this->httpClient = $this->prophesize('Guzzle\Http\Client');

        $this->authClient = $this->prophesize('Kairos\GoogleAnalyticsClientBundle\AuthClient\AuthClientInterface');
        $this->authClient->getAccessToken()
            ->shouldBeCalled()
            ->willReturn('access_token');

        $this->object = new Request($this->query->reveal(), $this->authClient->reveal());
        $this->object->setHttpClient($this->httpClient->reveal());
    }


    //todo check the results
    public function testGetResultWithUserIp()
    {
        $baseUrl = 'http://toto.com';

        $requests = array(
            array('userIp' => '192.190.190.1')
        );

        $result = array('query' =>
            array(
                'start-index' => 1,
                'max-results' => 100
            ),
            'totalResults' => 60,
            'totalsForAllResults' => array(60),
            'rows' => array('a', 'b', 'c')
        );


        $this->query->build()->shouldBeCalled()->willReturn($requests);
        $this->query->getBaseUrlApi()->shouldBeCalled()->willReturn($baseUrl);

        $guzzleRequest = $this->prophesize('Guzzle\Http\Message\RequestInterface');
        $guzzleResponse = $this->prophesize('Guzzle\Http\Message\Response');

        $query = $this->prophesize('Guzzle\Http\QueryString');
        $query->set('start-index', $result['query']['start-index']+1);
        $query->toArray()->willReturn(array('query'=>array('userIp' => '192.190.190.2')));


        $guzzleRequest->getClient()->willReturn($this->httpClient);
        $guzzleRequest->getQuery()->willReturn($query);

        $guzzleRequest->getResponse()->willReturn($guzzleResponse);
        $guzzleResponse->getBody()->shouldBeCalled()
            ->willReturn(json_encode($result));



        $this->httpClient->get($baseUrl, array(), Argument::type('array'))
            ->shouldBeCalledTimes(count($requests))->willReturn($guzzleRequest);

        $this->httpClient->send(Argument::type('array'))->shouldBeCalled();

        $result = $this->object->getResult();

        $this->assertEquals(array('query' =>
            array(
                'start-index' => 1,
                'max-results' => 100
            ),
            'totalResults' => 60,
            'totalsForAllResults' => array(60),
            'rows' => array('a', 'b', 'c')
        ),$result);
    }
}
