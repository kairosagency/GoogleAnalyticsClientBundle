<?php

namespace Kairos\GoogleAnalyticsClientBundle\Consumer;

class Query
{

    const
        /** */
        BASE_LENGTH_GA_URL     = 300,

        /** */
        LENGTH_LIMIT_GA_URL    = 2000
    ;

    /** @var string */
    protected $ids;

    /** @var string */
    protected $accessToken;

    /** @var \DateTime */
    protected $startDate;

    /** @var \DateTime */
    protected $endDate;

    /** @var array */
    protected $metrics;

    /** @var array */
    protected $dimensions;

    /** @var array */
    protected $sorts;

    /** @var array */
    protected $filters;
    protected $filtersSeparator;

    /** @var string */
    protected $segment;

    /** @var integer */
    protected $startIndex;

    /** @var integer */
    protected $maxResults;

    protected $baseUrlApi;

    /**
     * Creates a google analytics query.
     *
     * @param string $ids The google analytics query ids.
     */
    public function __construct($ids, $baseUrlApi)
    {
        $this->ids = $ids;
        $this->metrics = array("ga:pageviews");
        $this->startDate = new \DateTime('now -1 Month');
        $this->endDate = new \DateTime('now');
        $this->startIndex = 1;
        $this->maxResults = 10000;
        $this->baseUrlApi = $baseUrlApi;
    }

    /**
     * Gets the google analytics query ids.
     *
     * @return string The google analytics query ids.
     */
    public function getIds()
    {
        return $this->ids;
    }

    /**
     * Sets the google analytics query ids.
     *
     * @param string $ids The google analytics query ids.
     *
     * @return \Kairos\GoogleAnalyticsClientBundle\Consumer\Query The query.
     */
    public function setIds($ids)
    {
        $this->ids = $ids;

        return $this;
    }

    /**
     * Gets the google analytics query ids.
     *
     * @return string The google analytics query ids.
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Gets the google analytics query ids.
     *
     * @return string The google analytics query ids.
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Gets the google analytics query start date.
     *
     * @return \DateTime The google analytics query start date.
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Sets the google analytics query start date.
     *
     * @param \DateTime $startDate The google analytics query start date.
     *
     * @return \Kairos\GoogleAnalyticsClientBundle\Consumer\Query The query.
     */
    public function setStartDate(\DateTime $startDate = null)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Gets the google analytics query end date.
     *
     * @return \DateTime The google analytics query end date.
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Sets the google analytics query end date.
     *
     * @param \DateTime $endDate The google analytics query end date.
     *
     * @return \Kairos\GoogleAnalyticsClientBundle\Consumer\Query The query.
     */
    public function setEndDate(\DateTime $endDate = null)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Gets the google analytics query metrics.
     *
     * @return array The google analytics query metrics.
     */
    public function getMetrics()
    {
        return $this->metrics;
    }

    /**
     * Sets the google analytics query metrics.
     *
     * @param array $metrics The google analytics query metrics.
     *
     * @return \Kairos\GoogleAnalyticsClientBundle\Consumer\Query The query.
     */
    public function setMetrics(array $metrics)
    {
        $this->metrics = $metrics;

        return $this;
    }

    /**
     * Checks if the google analytics query has dimensions.
     *
     * @return boolean TRUE if the google analytics query has a dimensions else FALSE.
     */
    public function hasDimensions()
    {
        return !empty($this->dimensions);
    }

    /**
     * Gets the google analytics query dimensions.
     *
     * @return array The google analytics query dimensions.
     */
    public function getDimensions()
    {
        return $this->dimensions;
    }

    /**
     * Sets the google analytics query dimensions.
     *
     * @param array $dimensions The google analytics query dimensions.
     *
     * @return \Kairos\GoogleAnalyticsClientBundle\Consumer\Query The query.
     */
    public function setDimensions(array $dimensions)
    {
        $this->dimensions = $dimensions;

        return $this;
    }

    /**
     * Checks if the google analytics query is ordered.
     *
     * @return boolean TRUE if the google analytics query is ordered else FALSE.
     */
    public function hasSorts()
    {
        return !empty($this->sorts);
    }

    /**
     * Gets the google analytics query sorts.
     *
     * @return array The google analytics query sorts.
     */
    public function getSorts()
    {
        return $this->sorts;
    }

    /**
     * Sets the google analytics query sorts.
     *
     * @param array $sorts The google analytics query sorts.
     *
     * @return \Kairos\GoogleAnalyticsClientBundle\Consumer\Query The query.
     */
    public function setSorts(array $sorts)
    {
        $this->sorts = $sorts;

        return $this;
    }

    /**
     * Checks if the google analytics query has filters.
     *
     * @return boolean TRUE if the google analytics query has filters else FALSE.
     */
    public function hasFilters()
    {
        return !empty($this->filters);
    }

    /**
     * Gets the google analytics query filters.
     *
     * @return array The google analytics query filters.
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Sets the google analytics query filters.
     *
     * @param array $filters The google analytics query filters.
     *
     * @return \Kairos\GoogleAnalyticsClientBundle\Consumer\Query The query.
     */
    public function setFilters(array $filters, $mainSeparator = ',')
    {
        $this->filters = $filters;
        $this->mainSeparator = $mainSeparator;

        return $this;
    }

    /**
     * Checks of the google analytics query has a segment.
     *
     * @return boolean TRUE if the google analytics query has a segment else FALSE.
     */
    public function hasSegment()
    {
        return $this->segment !== null;
    }

    /**
     * Gets the google analytics query segment.
     *
     * @return string The google analytics query segment.
     */
    public function getSegment()
    {
        return $this->segment;
    }

    /**
     * Sets the google analytics query segment.
     *
     * @param string $segment The google analytics query segment.
     *
     * @return \Kairos\GoogleAnalyticsClientBundle\Consumer\Query The query.
     */
    public function setSegment($segment)
    {
        $this->segment = $segment;

        return $this;
    }

    /**
     * Gets the google analytics query start index.
     *
     * @return integer The google analytics query start index.
     */
    public function getStartIndex()
    {
        return $this->startIndex;
    }

    /**
     * Sets the google analytics query start index.
     *
     * @param integer $startIndex The google analytics start index.
     *
     * @return \Kairos\GoogleAnalyticsClientBundle\Consumer\Query The query.
     */
    public function setStartIndex($startIndex)
    {
        $this->startIndex = $startIndex;

        return $this;
    }

    /**
     * Gets the google analytics query max result count.
     *
     * @return integer The google analytics query max result count.
     */
    public function getMaxResults()
    {
        return $this->maxResults;
    }

    /**
     * Sets the google analytics query max result count.
     *
     * @param integer $maxResults The google analytics query max result count.
     *
     * @return \Kairos\GoogleAnalyticsClientBundle\Consumer\Query The query.
     */
    public function setMaxResults($maxResults)
    {
        $this->maxResults = $maxResults;

        return $this;
    }


    public function setBaseUrlApi($baseUrlApi)
    {
        $this->baseUrlApi = $baseUrlApi;

        return $this;
    }


    public function getBaseUrlApi()
    {
        return $this->baseUrlApi;
    }

    /**
     * Builds the query.
     *
     * @param string $accessToken The access token used to build the query.
     *
     * @return string The builded query.
     */
    public function generate()
    {
        $query = array(
            'ids'          => $this->getIds(),
            'access_token' => $this->getAccessToken(),
            'metrics'      => implode(',', $this->getMetrics()),
            'start-date'   => $this->getStartDate()->format('Y-m-d'),
            'end-date'     => $this->getEndDate()->format('Y-m-d'),
            'start-index'  => $this->getStartIndex(),
            'max-results'  => $this->getMaxResults(),
        );

        if ($this->hasSegment()) {
            $query['segment'] = $this->getSegment();
        }

        if ($this->hasDimensions()) {
            $query['dimensions'] = implode(',', $this->getDimensions());
        }

        if ($this->hasSorts()) {
            $query['sort'] = implode(',', $this->getSorts());
        }

        if ($this->hasFilters()) {
            $query['filters'] = implode(',', $this->getFilters());
        }

        return sprintf('%s?%s', $this->getBaseUrlApi(), http_build_query($query));
    }

    /**
     * @return array
     */
    public function build()
    {
        $GARequestUrls      = array();
        $currentFilters     = $this->getFilters();

        if(count($currentFilters) > 0) {

            $baseUrlLength      = $this->getBaseUrlLengthWithoutFilters();
            $currentUrlLength   = $baseUrlLength;
            $filters            = array();
            $filtersTmp         = array();
            $idxFilter          = 0;

            foreach($currentFilters as $filter) {

                // We fill the filters temp array in order to check the length of the url generate
                $filtersTmp[] = $filter;
                $this->setFilters($filtersTmp);
                $currentUrlLength += strlen($this->generate());

                // If the limit url length is reached, we keep in $GARequestUrls the url
                if($currentUrlLength > self::LENGTH_LIMIT_GA_URL) {

                    // We set the filters array in order to generate the url that will be sent
                    $this->setFilters($filters);
                    $GARequestUrls[] = $this->generate();

                    // Reset the current ur lLength with the $baseUrlLength and last filter
                    $this->setFilters($filter);
                    $currentUrlLength = $baseUrlLength + strlen($this->generate());

                    // And reset all the var for the generation of the url length
                    $filters    = array();
                    $filtersTmp = array();
                    $idxFilter  = 0;
                }

                // We fill the official filters array
                $filters[] = $filter;
                $idxFilter++;
            }
        }

        $GARequestUrls[] = $this->generate();

        return $GARequestUrls;
    }

    /**
     * Get the base url length with initial parameters (Without filters)
     *
     * @return int
     */
    private function getBaseUrlLengthWithoutFilters()
    {
        $filters = $this->getFilters();
        $this->setFilters(array());
        $baseUrlLength = self::BASE_LENGTH_GA_URL + strlen($this->generate());
        $this->setFilters($filters);

        return $baseUrlLength;
    }
}