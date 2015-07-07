<?php

namespace Kairos\GoogleAnalyticsClientBundle\Response;

/**
 * Interface GoogleAnalyticsResponseInterface
 * @package Kairos\GoogleAnalyticsClientBundle\Response
 */
interface GoogleAnalyticsResponseInterface
{
    /**
     * Gets the raw reponse given by Google Analytics.
     *
     * @return array
     */
    public function getRawResponse();

    /**
     * Gets the totals for all results.
     *
     * @return array The totals for all results.
     */
    public function getTotalsForAllResults();

    /**
     * Gets the total number of results.
     *
     * @return integer The total number of results.
     */
    public function getTotalResults();
}
