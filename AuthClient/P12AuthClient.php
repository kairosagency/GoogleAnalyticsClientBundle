<?php

namespace Kairos\GoogleAnalyticsClientBundle\AuthClient;

use Doctrine\Common\Cache\Cache;
use Guzzle\Http\Client;
use Kairos\GoogleAnalyticsClientBundle\Exception\GoogleAnalyticsException;

/**
 * Class AuthClient
 * @package Kairos\GoogleAnalyticsClientBundle\AuthClient
 */
class P12AuthClient extends AbstractAuthClient
{
    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var string
     */
    protected $tokenEndPoint;

    /**
     * @var string
     */
    protected $clientEmail;

    /**
     * Certificate P12 file for google analytics
     *
     * @var P12
     */
    protected $privateKey;

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
     * @param Cache $cacheProvider
     * @param $baseUrl
     * @param $tokenEndPoint
     * @param $clientEmail
     * @param $privateKey
     * @param $kernelRootDir
     * @param int $cacheTTL
     */
    public function __construct(Cache $cacheProvider, $baseUrl, $tokenEndPoint, $clientEmail, $privateKey, $kernelRootDir, $cacheTTL = 3600)
    {
        $this->cacheProvider = $cacheProvider;
        $this->baseUrl = $baseUrl;
        $this->tokenEndPoint = $tokenEndPoint;
        $this->clientEmail = $clientEmail;
        $this->privateKey = $kernelRootDir . '/certificates/' . $privateKey;
        $this->cacheTTL = $cacheTTL;
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
        $url = $this->baseUrl . $this->tokenEndPoint;
        $headers = array('Content-Type' => 'application/x-www-form-urlencoded');
        $content = array(
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $this->generateJsonWebToken(),
        );

        $client = new Client();
        $request = $client->post($url, $headers, $content);
        $response = $request->send();

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
        $url = $this->baseUrl . $this->tokenEndPoint;
        $exp = new \DateTime('+1 hours', new \DateTimeZone('Europe/Paris'));
        $iat = new \DateTime('now', new \DateTimeZone('Europe/Paris'));

        $jwtHeader = base64_encode(json_encode(array('alg' => 'RS256', 'typ' => 'JWT')));

        $jwtClaimSet = base64_encode(
            json_encode(
                array(
                    'iss'   => $this->clientEmail,
                    'scope' => 'https://www.googleapis.com/auth/analytics.readonly',
                    'aud'   => $url,
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
     * @throws \Kairos\GoogleAnalyticsClientBundle\Exception\GoogleAnalyticsException If an error occured when generating the signature.
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