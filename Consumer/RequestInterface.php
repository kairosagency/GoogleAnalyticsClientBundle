<?php

namespace Kairos\GoogleAnalyticsClientBundle\Consumer;

/**
 * Class RequestInterface
 * @package Kairos\GoogleAnalyticsClientBundle\Consumer
 */
interface RequestInterface
{
    /**
     * Send http request to Googla analytics and gets the response.
     *
     * @param $requestUrl
     *
     * @return mixed
     * @throws \Kairos\GoogleAnalyticsClientBundle\Exception\GoogleAnalyticsException
     */
    public function request($requestUrl);

    /**
     * @return array
     */
    public function getResult();

    /**
     * Check if the data has pagination and gets all the data if there is a pagination.
     *
     * @return array
     */
    protected function getGAResult();

    /**
     * Merge all the datas in the basic format.
     *
     * @param $results
     *
     * @return array
     */
    protected function mergeResults($results);
}