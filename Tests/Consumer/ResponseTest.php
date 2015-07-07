<?php

namespace Kairos\GoogleAnalyticsClientBundle\Tests\Consumer;

use Kairos\GoogleAnalyticsClientBundle\Response\GoogleAnalyticsResponse;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRawResponse()
    {
        $response = array('foo', 'bar');
        $obj = new GoogleAnalyticsResponse($response);

        $this->assertSame($response, $obj->getRawResponse());
    }

    public function testGetTFAR()
    {
        $response = array('foo', 'totalsForAllResults' => 100);
        $obj = new GoogleAnalyticsResponse($response);

        $this->assertSame(100, $obj->getTotalsForAllResults());
    }

    public function testGetTR()
    {
        $response = array('foo', 'totalResults' => 10000);
        $obj = new GoogleAnalyticsResponse($response);

        $this->assertSame(10000, $obj->getTotalResults());
    }
}
