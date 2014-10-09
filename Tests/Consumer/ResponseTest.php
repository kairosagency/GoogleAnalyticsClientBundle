<?php

namespace Kairos\GoogleAnalyticsClientBundle\Tests;

use Kairos\GoogleAnalyticsClientBundle\Consumer\Response;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRawResponse()
    {
        $response = array('foo', 'bar');
        $obj = new Response($response);

        $this->assertSame($response, $obj->getRawResponse());
    }

    public function testGetTFAR()
    {
        $response = array('foo', 'totalsForAllResults' => 100);
        $obj = new Response($response);

        $this->assertSame(100, $obj->getTotalsForAllResults());
    }

    public function testGetTR()
    {
        $response = array('foo', 'totalResults' => 10000);
        $obj = new Response($response);

        $this->assertSame(10000, $obj->getTotalResults());
    }
}