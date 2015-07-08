<?php

namespace Kairos\GoogleAnalyticsClientBundle\Consumer;

/**
 * Class Query
 * @package Kairos\GoogleAnalyticsClientBundle\Consumer
 */
class Query implements QueryInterface
{
    /** Estimation of the base length of a Googla analytics request url */
    const BASE_LENGTH_GA_URL = 300;

    /** Length limit of a Google analytics request url
    real limit : 9415 (not included)
     */
    const LENGTH_LIMIT_GA_URL = 8000;

    /** @var array */
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

    /** @var string */
    protected $filtersSeparator;

    /** @var string */
    protected $segment;

    /** @var integer */
    protected $startIndex;

    /** @var integer */
    protected $maxResults;

    /** @var string Base url for Google Analytics API */
    protected $baseUrlApi;

    /**
     * ip of user to get through rate limit
     * @var string
     */
    protected $userIp;

    /**
     * id of user to get through rate limit
     * @var string
     */
    protected $quotaUser;



    /**
     * Creates a google analytics query.
     *
     * @param array $ids The google analytics query ids.
     */
    public function __construct(array $ids, $baseUrlApi)
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
     * @return array The google analytics query ids.
     */
    public function getIds()
    {
        return $this->ids;
    }

    /**
     * Normalize the ids to this format ga:xxxx,ga:xxxx
     *
     * @return string The google analytics query ids.
     */
    public function normalizeIds()
    {
        $ids = '';
        if(!empty($this->ids)) {
            foreach($this->ids as $key => $id) {
                if($key > 0) { $ids .= ','; }
                $ids .= 'ga:' . $id;
            }
        }
        return $ids;
    }

    /**
     * Sets the google analytics query ids.
     *
     * @param string $ids The google analytics query ids.
     *
     * @return \Kairos\GoogleAnalyticsClientBundle\Consumer\Query The query.
     */
    public function setIds(array $ids)
    {
        $this->ids = $ids;

        return $this;
    }

    /**
     * Sets the google analytics access token.
     *
     * @param string $accessToken
     *
     * @return \Kairos\GoogleAnalyticsClientBundle\Consumer\Query The query.
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Gets the google analytics access token.
     *
     * @return string The google analytics access token.
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
     * Sets the google analytics query filters and filters separator.
     *
     * @param array $filters The google analytics query filters.
     * @param string $filtersSeparator The google analytics query filters separator.
     *
     * @return \Kairos\GoogleAnalyticsClientBundle\Consumer\Query The query.
     */
    public function setFilters(array $filters, $filtersSeparator = ',')
    {
        $this->filters = $filters;
        $this->filtersSeparator = $filtersSeparator;

        return $this;
    }

    /**
     * Gets the google analytics query filters separator.
     *
     * @return string The google analytics query filters separator.
     */
    public function getFiltersSeparator()
    {
        return $this->filtersSeparator;
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

    /**
     * Sets the google analytics base url api.
     *
     * @param string $baseUrlApi
     *
     * @return \Kairos\GoogleAnalyticsClientBundle\Consumer\Query The query.
     */
    public function setBaseUrlApi($baseUrlApi)
    {
        $this->baseUrlApi = $baseUrlApi;

        return $this;
    }

    /**
     * Gets the google analytics base url api.
     *
     * @return string
     */
    public function getBaseUrlApi()
    {
        return $this->baseUrlApi;
    }

    /**
     * @param $ip
     * @return $this
     */
    public function setUserIp($userIp)
    {
        $this->userIp = $userIp;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserIp()
    {
        return $this->userIp;
    }

    /**
     * @return bool
     */
    public function hasUserIp()
    {
        return !is_null($this->userIp);
    }

    /**
     * @param $user
     * @return $this
     */
    public function setQuotaUser($quotaUser)
    {
        $this->quotaUser = $quotaUser;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuotaUser()
    {
        return $this->quotaUser;
    }

    /**
     * @return bool
     */
    public function hasQuotaUser()
    {
        return !is_null($this->quotaUser);
    }

    /**
     * Checks how many url will be needed to get data from Google Analytics API
     * and build an array with the request urls.
     *
     * @return string[]
     */
    public function build()
    {
        $initialQuery = $this->generate();
        $initialLength = $this->queryLength($initialQuery);

        if($initialLength > self::LENGTH_LIMIT_GA_URL) {
            $GARequestUrls = array();
            $baseUrlLength = $this->getBaseLengthUrlGAWithoutFilters();
            $divisionCoef = ceil($initialLength/(self::LENGTH_LIMIT_GA_URL-$baseUrlLength));
            $offset = ceil(count($this->getFilters())/$divisionCoef);
            $filterChunks = array_chunk($this->getFilters(), $offset);
            foreach($filterChunks AS $filterChunk) {
                $GARequestUrls[] = $this->generate($filterChunk);
            }
            return $GARequestUrls;
        }
        else
            return array($initialQuery);
    }


    /**
     * Generate a request url.
     *
     * @return string The builded query.
     */
    protected function generate(array $overrideFilters = array())
    {
        $query = array(
            'ids'          => $this->normalizeIds(),
            'access_token' => $this->getAccessToken(),
            'metrics'      => implode(',', $this->getMetrics()),
            'start-date'   => $this->getStartDate()->format('Y-m-d'),
            'end-date'     => $this->getEndDate()->format('Y-m-d'),
            'start-index'  => $this->getStartIndex(),
            'max-results'  => $this->getMaxResults(),
        );

        if ($this->hasQuotaUser()) {
            $query['quotaUser'] = $this->getQuotaUser();
        }

        if ($this->hasUserIp()) {
            $query['userIp'] = $this->getUserIp();
        }

        if ($this->hasSegment()) {
            $query['segment'] = $this->getSegment();
        }

        if ($this->hasDimensions()) {
            $query['dimensions'] = implode(',', $this->getDimensions());
        }

        if ($this->hasSorts()) {
            $query['sort'] = implode(',', $this->getSorts());
        }

        if(count($overrideFilters) === 0) {
            if ($this->hasFilters()) {
                $query['filters'] = implode($this->getFiltersSeparator(), $this->getFilters());
            }
        } else {
            $query['filters'] = implode($this->getFiltersSeparator(), $overrideFilters);
        }

        return $query;
    }

    /**
     * @param array $query
     * @return string
     */
    public function queryToString(array $query)
    {
        return sprintf('%s?%s', $this->getBaseUrlApi(), http_build_query($query));
    }

    /**
     * @param array $query
     * @return int
     */
    public function queryLength(array $query)
    {
        if(function_exists('mb_strlen'))
            return mb_strlen($this->queryToString($this->generate()), 'UTF-8');
        else
            return strlen($this->queryToString($this->generate()));
    }

    /**
     * Gets the base url length with initial parameters (without filters).
     *
     * @return int
     */
    private function getBaseLengthUrlGAWithoutFilters()
    {
        $filters = $this->getFilters();
        $this->setFilters(array());
        $baseUrlLength = self::BASE_LENGTH_GA_URL + $this->queryLength($this->generate());
        $this->setFilters($filters);

        return $baseUrlLength;
    }
}
