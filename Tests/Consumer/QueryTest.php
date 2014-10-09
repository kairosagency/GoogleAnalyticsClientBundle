<?php

namespace Kairos\GoogleAnalyticsClientBundle\Tests;

use Kairos\GoogleAnalyticsClientBundle\Consumer\Query;

class QueryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Kairos\GoogleAnalyticsClientBundle\Consumer\Query */
    protected $object;

    /** @var array */
    protected $ids;

    /** @var string Base url for Google Analytics API */
    protected $baseUrlApi;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->ids = array('id1', 'id2');
        $this->baseUrlApi = 'https://base_url_api';

        $this->object = new Query($this->ids, $this->baseUrlApi);
    }

    public function testConstructor()
    {
        $this->assertFalse($this->object->hasDimensions());
        $this->assertFalse($this->object->hasSorts());
        $this->assertFalse($this->object->hasFilters());
        $this->assertFalse($this->object->hasSegment());

        $this->assertSame($this->ids, $this->object->getIds());
        $this->assertSame('ga:id1,ga:id2', $this->object->normalizeIds());
        $this->assertSame($this->baseUrlApi, $this->object->getBaseUrlApi());
        $this->assertInstanceOf('Datetime', $this->object->getStartDate());
        $this->assertInstanceOf('Datetime', $this->object->getEndDate());
        $this->assertSame(array("ga:pageviews"), $this->object->getMetrics());
        $this->assertSame(1, $this->object->getStartIndex());
        $this->assertSame(10000, $this->object->getMaxResults());
    }

    public function testIds()
    {
        $this->object->setIds(array('foo'));

        $this->assertSame(array('foo'), $this->object->getIds());
    }

    public function testStartDate()
    {
        $startDate = new \DateTime();
        $this->object->setStartDate($startDate);

        $this->assertSame($startDate, $this->object->getStartDate());
    }

    public function testEndDate()
    {
        $endDate = new \DateTime();
        $this->object->setEndDate($endDate);

        $this->assertSame($endDate, $this->object->getEndDate());
    }

    public function testMetrics()
    {
        $metrics = array('foo', 'bar');
        $this->object->setMetrics($metrics);

        $this->assertSame($metrics, $this->object->getMetrics());
    }

    public function testDimensions()
    {
        $dimensions = array('foo', 'bar');
        $this->object->setDimensions($dimensions);

        $this->assertTrue($this->object->hasDimensions());
        $this->assertSame($dimensions, $this->object->getDimensions());
    }

    public function testSorts()
    {
        $sorts = array('foo', 'bar');
        $this->object->setSorts($sorts);

        $this->assertTrue($this->object->hasSorts());
        $this->assertSame($sorts, $this->object->getSorts());
    }

    public function testFilters()
    {
        $filters = array('foo', 'bar');
        $this->object->setFilters($filters);

        $this->assertTrue($this->object->hasFilters());
        $this->assertSame($filters, $this->object->getFilters());
    }

    public function testSegment()
    {
        $segment = 'foo';
        $this->object->setSegment($segment);

        $this->assertTrue($this->object->hasSegment());
        $this->assertSame($segment, $this->object->getSegment());
    }

    public function testStartIndex()
    {
        $startIndex = 3;
        $this->object->setStartIndex($startIndex);

        $this->assertSame($startIndex, $this->object->getStartIndex());
    }

    public function testMaxResults()
    {
        $maxResults = 100;
        $this->object->setMaxResults($maxResults);

        $this->assertSame($maxResults, $this->object->getMaxResults());
    }

    public function testAccessToken()
    {
        $accessToken = 'access_token';
        $this->object->setAccessToken($accessToken);

        $this->assertSame($accessToken, $this->object->getAccessToken());
    }

    public function testBuild()
    {
        $this->object->setIds(array('foo', 'bar'));
        $this->object->setStartDate(new \DateTime('2013-01-01'));
        $this->object->setEndDate(new \DateTime('2013-01-31'));
        $this->object->setMetrics(array('m1', 'm2'));
        $this->object->setDimensions(array('d1', 'd2'));
        $this->object->setSorts(array('s1', 's2'));
        $this->object->setFilters(array('f1', 'f2'));
        $this->object->setSegment('seg');
        $this->object->setStartIndex(10);
        $this->object->setMaxResults(100);
        $this->object->setAccessToken('access_token');

        $expected = array(
            'https://base_url_api?ids=ga%3Afoo%2Cga%3Abar&access_token=access_token&' .
            'metrics=m1%2Cm2&start-date=2013-01-01&end-date=2013-01-31&start-index=10&max-results=100&' .
            'segment=seg&dimensions=d1%2Cd2&sort=s1%2Cs2&filters=f1%2Cf2'
        );

        $this->assertSame($expected, $this->object->build());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->ids = null;
        $this->baseUrlApi = null;
        $this->object = null;
    }

}