<?php

namespace Kairos\GoogleAnalyticsClientBundle\Consumer;

/**
 * Class QueryInterface
 * @package Kairos\GoogleAnalyticsClientBundle\Consumer
 */
interface QueryInterface
{
    /**
     * Gets the google analytics query ids.
     *
     * @return array The google analytics query ids.
     */
    public function getIds();

    /**
     * Normalize the ids to this format ga:xxxx,ga:xxxx
     *
     * @return string The google analytics query ids.
     */
    public function normalizeIds();

    /**
     * Sets the google analytics query ids.
     *
     * @param string $ids The google analytics query ids.
     *
     * @return \Kairos\GoogleAnalyticsClientBundle\Consumer\Query The query.
     */
    public function setIds(array $ids);

    /**
     * Sets the google analytics access token.
     *
     * @param string $accessToken
     *
     * @return \Kairos\GoogleAnalyticsClientBundle\Consumer\Query The query.
     */
    public function setAccessToken($accessToken);

    /**
     * Gets the google analytics access token.
     *
     * @return string The google analytics access token.
     */
    public function getAccessToken();

    /**
     * Gets the google analytics query start date.
     *
     * @return \DateTime The google analytics query start date.
     */
    public function getStartDate();

    /**
     * Sets the google analytics query start date.
     *
     * @param \DateTime $startDate The google analytics query start date.
     *
     * @return \Kairos\GoogleAnalyticsClientBundle\Consumer\Query The query.
     */
    public function setStartDate(\DateTime $startDate = null);

    /**
     * Gets the google analytics query end date.
     *
     * @return \DateTime The google analytics query end date.
     */
    public function getEndDate();

    /**
     * Sets the google analytics query end date.
     *
     * @param \DateTime $endDate The google analytics query end date.
     *
     * @return \Kairos\GoogleAnalyticsClientBundle\Consumer\Query The query.
     */
    public function setEndDate(\DateTime $endDate = null);

    /**
     * Gets the google analytics query metrics.
     *
     * @return array The google analytics query metrics.
     */
    public function getMetrics();

    /**
     * Sets the google analytics query metrics.
     *
     * @param array $metrics The google analytics query metrics.
     *
     * @return \Kairos\GoogleAnalyticsClientBundle\Consumer\Query The query.
     */
    public function setMetrics(array $metrics);

    /**
     * Checks if the google analytics query has dimensions.
     *
     * @return boolean TRUE if the google analytics query has a dimensions else FALSE.
     */
    public function hasDimensions();

    /**
     * Gets the google analytics query dimensions.
     *
     * @return array The google analytics query dimensions.
     */
    public function getDimensions();

    /**
     * Sets the google analytics query dimensions.
     *
     * @param array $dimensions The google analytics query dimensions.
     *
     * @return \Kairos\GoogleAnalyticsClientBundle\Consumer\Query The query.
     */
    public function setDimensions(array $dimensions);

    /**
     * Checks if the google analytics query is ordered.
     *
     * @return boolean TRUE if the google analytics query is ordered else FALSE.
     */
    public function hasSorts();

    /**
     * Gets the google analytics query sorts.
     *
     * @return array The google analytics query sorts.
     */
    public function getSorts();

    /**
     * Sets the google analytics query sorts.
     *
     * @param array $sorts The google analytics query sorts.
     *
     * @return \Kairos\GoogleAnalyticsClientBundle\Consumer\Query The query.
     */
    public function setSorts(array $sorts);

    /**
     * Checks if the google analytics query has filters.
     *
     * @return boolean TRUE if the google analytics query has filters else FALSE.
     */
    public function hasFilters();

    /**
     * Gets the google analytics query filters.
     *
     * @return array The google analytics query filters.
     */
    public function getFilters();

    /**
     * Sets the google analytics query filters and filters separator.
     *
     * @param array $filters The google analytics query filters.
     * @param string $filtersSeparator The google analytics query filters separator.
     *
     * @return \Kairos\GoogleAnalyticsClientBundle\Consumer\Query The query.
     */
    public function setFilters(array $filters, $filtersSeparator = ',');

    /**
     * Gets the google analytics query filters separator.
     *
     * @return string The google analytics query filters separator.
     */
    public function getFiltersSeparator();

    /**
     * Checks of the google analytics query has a segment.
     *
     * @return boolean TRUE if the google analytics query has a segment else FALSE.
     */
    public function hasSegment();

    /**
     * Gets the google analytics query segment.
     *
     * @return string The google analytics query segment.
     */
    public function getSegment();

    /**
     * Sets the google analytics query segment.
     *
     * @param string $segment The google analytics query segment.
     *
     * @return \Kairos\GoogleAnalyticsClientBundle\Consumer\Query The query.
     */
    public function setSegment($segment);

    /**
     * Gets the google analytics query start index.
     *
     * @return integer The google analytics query start index.
     */
    public function getStartIndex();

    /**
     * Sets the google analytics query start index.
     *
     * @param integer $startIndex The google analytics start index.
     *
     * @return \Kairos\GoogleAnalyticsClientBundle\Consumer\Query The query.
     */
    public function setStartIndex($startIndex);

    /**
     * Gets the google analytics query max result count.
     *
     * @return integer The google analytics query max result count.
     */
    public function getMaxResults();

    /**
     * Sets the google analytics query max result count.
     *
     * @param integer $maxResults The google analytics query max result count.
     *
     * @return \Kairos\GoogleAnalyticsClientBundle\Consumer\Query The query.
     */
    public function setMaxResults($maxResults);

    /**
     * Sets the google analytics base url api.
     *
     * @param string $baseUrlApi
     *
     * @return \Kairos\GoogleAnalyticsClientBundle\Consumer\Query The query.
     */
    public function setBaseUrlApi($baseUrlApi);

    /**
     * Gets the google analytics base url api.
     *
     * @return string
     */
    public function getBaseUrlApi();

    /**
     * Checks how many url will be needed to get data from Google Analytics API
     * and build an array with the request urls.
     *
     * @return string[]
     */
    public function build();
}
