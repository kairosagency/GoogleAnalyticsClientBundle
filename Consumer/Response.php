<?php

namespace Kairos\GoogleAnalyticsClientBundle\Consumer;

/**
 * Class Response
 * @package Kairos\GoogleAnalyticsClientBundle\Consumer
 */
class Response
{
    /** @var array */
    protected $rawResponse;

    /** @var array */
    protected $totalsForAllResults;

    /** @var integer */
    protected $totalResults;

    /**
     * @param array $response
     */
    public function __construct(array $response)
    {
        $this->rawResponse = $response;

        if (isset($response['totalsForAllResults'])) {
            $this->totalsForAllResults = $response['totalsForAllResults'];
        }

        if (isset($response['totalResults'])) {
            $this->totalResults = $response['totalResults'];
        }
    }

    /**
     * Gets the raw reponse given by Google Analytics.
     *
     * @return array
     */
    public function getRawResponse()
    {
        return $this->rawResponse;
    }

    /**
     * Gets the totals for all results.
     *
     * @return array The totals for all results.
     */
    public function getTotalsForAllResults()
    {
        return $this->totalsForAllResults;
    }

    /**
     * Gets the total number of results.
     *
     * @return integer The total number of results.
     */
    public function getTotalResults()
    {
        return $this->totalResults;
    }
}
