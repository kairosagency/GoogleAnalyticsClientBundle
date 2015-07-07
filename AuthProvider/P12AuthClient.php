<?php

namespace Kairos\GoogleAnalyticsClientBundle\AuthProvider;

use Doctrine\Common\Cache\Cache;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Kairos\GoogleAnalyticsClientBundle\Exception\GoogleAnalyticsException;

/**
 * Class AuthClient
 * @package Kairos\GoogleAnalyticsClientBundle\AuthClient
 */
class P12AuthClient extends AbstractAuthClient
{
    /** @const Google Analytics OAuth scope */
    const SCOPE = 'https://www.googleapis.com/auth/analytics.readonly';

    /** @var string */
    protected $url;

    /** @var string */
    protected $tokenEndPoint;

    /** @var string */
    protected $clientEmail;

    /** @var string Certificate P12 file path for google analytics */
    protected $privateKey;

    /** @var \Doctrine\Common\Cache\Cache */
    protected $cacheProvider;

    /** @var int Lifetime parameter for the cache provider */
    protected $cacheTTL;

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @param Cache $cacheProvider
     * @param string $baseUrl
     * @param string $tokenEndPoint
     * @param string $clientEmail
     * @param string $privateKey
     * @param int $cacheTTL
     */
    public function __construct(
        Cache $cacheProvider,
        $baseUrl,
        $tokenEndPoint,
        $clientEmail,
        $privateKey,
        $cacheTTL = 3600
    ) {
        $this->cacheProvider = $cacheProvider;
        $this->httpClient = new Client();
        $this->url = $baseUrl . $tokenEndPoint;
        $this->clientEmail = $clientEmail;
        $this->privateKey = $privateKey;
        $this->cacheTTL = $cacheTTL;
    }

    /**
     * Sets a cache provider in order use cache to save the access token.
     *
     * @param Cache $cacheProvider
     *
     * @return \Kairos\GoogleAnalyticsClientBundle\AuthProvider\P12AuthClient
     */
    public function setCacheProvider(Cache $cacheProvider)
    {
        $this->cacheProvider = $cacheProvider;
        return $this;
    }

    /**
     * Gets a cache provider in order use cache to save the access token.
     *
     * @return Cache $cacheProvider.
     */
    public function getCacheProvider()
    {
        return $this->cacheProvider;
    }

    /**
     * Sets an http client in order to make google analytics request.

     * @param Client $httpClient
     * @return $this
     */
    public function setHttpClient(Client $httpClient)
    {
        $this->httpClient = $httpClient;

        return $this;
    }

    /**
     * Gets an http client in order to make google analytics request.
     *
     * @return Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * Sets the client email.
     *
     * @param string $clientEmail The client email.
     *
     * @return \Kairos\GoogleAnalyticsClientBundle\AuthProvider\P12AuthClient
     */
    public function setClientEmail($clientEmail)
    {
        $this->clientEmail = $clientEmail;

        return $this;
    }

    /**
     * Gets the client email.
     *
     * @return string The client email.
     */
    public function getClientEmail()
    {
        return $this->clientEmail;
    }

    /**
     * Sets the google analytics service url.
     *
     * @param string $url The google analytics service url.
     *
     * @return \Kairos\GoogleAnalyticsClientBundle\AuthProvider\P12AuthClient
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Gets the google analytics service url.
     *
     * @return string The google analytics service url.
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Sets the lifetime integer for the cache provider.
     *
     * @param int $cacheTTL Lifetime parameter for the cache provider.
     *
     * @return \Kairos\GoogleAnalyticsClientBundle\AuthProvider\P12AuthClient
     */
    public function setCacheTTL($cacheTTL)
    {
        $this->cacheTTL = $cacheTTL;

        return $this;
    }

    /**
     * Gets the lifetime integer for the cache provider.
     *
     * @return int Lifetime parameter for the cache provider.
     */
    public function getCacheTTL()
    {
        return $this->cacheTTL;
    }

    /**
     * Sets the absolute private key path.
     *
     * @param string $privateKey The absolute private key path.
     *
     * @throws \Kairos\GoogleAnalyticsClientBundle\Exception\GoogleAnalyticsException If the private key does not exist.
     *
     * @return \Kairos\GoogleAnalyticsClientBundle\AuthProvider\P12AuthClient
     */
    public function setPrivateKey($privateKey)
    {
        if (!file_exists($privateKey)) {
            throw GoogleAnalyticsException::invalidPrivateKeyFile($privateKey);
        }

        $this->privateKey = $privateKey;

        return $this;
    }

    /**
     * Gets the absolute private key path.
     *
     * @return string The absolute private key path.
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    protected function generateCacheKey()
    {
        return md5($this->clientEmail);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Kairos\GoogleAnalyticsClientBundle\Exception\GoogleAnalyticsException If the access token can not be retrieved.
     * @return string
     */
    protected function requestAccessToken()
    {
        $response = $this->getHttpClient()->post(
            $this->getUrl(),
            array(
            'form_params' => array(
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $this->generateJsonWebToken(),
            )
        ));
        $response = json_decode($response->getBody());

        if (isset($response->error)) {
            throw GoogleAnalyticsException::invalidAccessToken($response->error);
        }

        return $response->access_token;
    }

    /**
     * Generates the JWT in order to get the access token.
     *
     * @return string The Json Web Token (JWT).
     */
    private function generateJsonWebToken()
    {
        $exp = new \DateTime('+1 hours', new \DateTimeZone('Europe/Paris'));
        $iat = new \DateTime('now', new \DateTimeZone('Europe/Paris'));

        $jwtHeader = base64_encode(json_encode(array('alg' => 'RS256', 'typ' => 'JWT')));

        $jwtClaimSet = base64_encode(
            json_encode(
                array(
                    'iss'   => $this->clientEmail,
                    'scope' => self::SCOPE,
                    'aud'   => $this->getUrl(),
                    'exp'   => $exp->getTimestamp(),
                    'iat'   => $iat->getTimestamp(),
                )
            )
        );

        $jwtSignature = base64_encode($this->generateSignature($jwtHeader . '.' . $jwtClaimSet));

        return sprintf('%s.%s.%s', $jwtHeader, $jwtClaimSet, $jwtSignature);
    }

    /**
     * Generates the JWT signature according to the private key file and the JWT content.
     *
     * @param string $jsonWebToken The JWT content.
     *
     * @throws \Kairos\GoogleAnalyticsClientBundle\Exception\GoogleAnalyticsException If an error occured when generating the signature.
     *
     * @return string The JWT signature.
     */
    private function generateSignature($jsonWebToken)
    {
        if (!function_exists('openssl_x509_read')) {
            throw GoogleAnalyticsException::invalidOpenSslExtension();
        }

        $certificate = file_get_contents($this->privateKey);

        $certificates = array();
        if (!openssl_pkcs12_read($certificate, $certificates, 'notasecret')) {
            throw GoogleAnalyticsException::invalidPKCS12File();
        }

        if (!isset($certificates['pkey']) || !$certificates['pkey']) {
            throw GoogleAnalyticsException::invalidPKCS12Format();
        }

        $ressource = openssl_pkey_get_private($certificates['pkey']);

        if (!$ressource) {
            throw GoogleAnalyticsException::invalidPKCS12PKey();
        }

        $signature = null;
        if (!openssl_sign($jsonWebToken, $signature, $ressource, 'sha256')) {
            throw GoogleAnalyticsException::invalidPKCS12Signature();
        }

        openssl_pkey_free($ressource);

        return $signature;
    }
}
