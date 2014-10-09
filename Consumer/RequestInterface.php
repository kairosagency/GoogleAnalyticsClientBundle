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
}
