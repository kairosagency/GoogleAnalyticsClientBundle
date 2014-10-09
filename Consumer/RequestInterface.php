<?php

namespace Kairos\GoogleAnalyticsClientBundle\Consumer;

/**
 * Class RequestInterface
 * @package Kairos\GoogleAnalyticsClientBundle\Consumer
 */
interface RequestInterface
{
    /**
     * @return array
     */
    public function getResult();
}
