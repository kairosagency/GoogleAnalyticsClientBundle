<?php

namespace Kairos\GoogleAnalyticsClientBundle\Request;

use Kairos\GoogleAnalyticsClientBundle\Query\QueryInterface;

/**
 * Interface GoogleAnalyticsRequestInterface
 * @package Kairos\GoogleAnalyticsClientBundle\Request
 */
interface GoogleAnalyticsRequestInterface
{
    /**
     * @return array
     */
    public function getResult(QueryInterface $query);
}
