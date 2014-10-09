<?php

namespace Kairos\GoogleAnalyticsClientBundle\AuthClient;

interface AuthClientInterface
{
    /**
     * Get a valid access token from cache or from google
     *
     * @return string
     */
    public function getAccessToken();
}
