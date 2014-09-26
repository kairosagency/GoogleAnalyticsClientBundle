<?php

namespace Kairos\GoogleAnalyticsClientBundle\AuthClient;

/**
 * Class AbstractAuthClient
 * @package Kairos\GoogleAnalyticsClientBundle\AuthClient
 */
abstract class AbstractAuthClient implements AuthClientInterface
{
    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $cacheProvider;

    /**
     * Lifetime parameter for the cache provider
     *
     * @var integer
     */
    protected $cacheTTL;

    /**
     * Get the google access token from cache/google
     *
     * @return string
     */
    public function getAccessToken()
    {
        $cacheKey = $this->generateCacheKey();

        if($this->cacheProvider->contains($cacheKey)) {
            return $this->cacheProvider->fetch($cacheKey);
        }
        else {
            $accessToken = $this->requestAccessToken();
            $this->cacheProvider->save($cacheKey, $accessToken, $this->cacheTTL);
            return $accessToken;
        }
    }

    /**
     * Generate a unique key depending on parameters
     *
     * @return string
     */
    abstract protected function generateCacheKey();

    /**
     * Get the google access token
     *
     * @return string
     */
    abstract protected function requestAccessToken();
}