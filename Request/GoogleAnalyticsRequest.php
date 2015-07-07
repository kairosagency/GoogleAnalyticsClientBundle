<?php

namespace Kairos\GoogleAnalyticsClientBundle\Request;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Kairos\GoogleAnalyticsClientBundle\AuthProvider\AuthClientInterface;
use Kairos\GoogleAnalyticsClientBundle\Query\QueryInterface;
use Kairos\GoogleAnalyticsClientBundle\Exception\GoogleAnalyticsException;
use Kairos\GoogleAnalyticsClientBundle\Response\GoogleAnalyticsResponse;

/**
 * Class GoogleAnalyticsRequest
 * @package Kairos\GoogleAnalyticsClientBundle\Request
 */
class GoogleAnalyticsRequest implements GoogleAnalyticsRequestInterface
{
    /** @var \Kairos\GoogleAnalyticsClientBundle\Query\QueryInterface */
    protected $query;

    /** @var \Kairos\GoogleAnalyticsClientBundle\AuthProvider\AuthClientInterface */
    protected $authProvider;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var int
     */
    protected $throttle;

    /**
     * @var array
     */
    protected $throttleTable;

    /**
     * Constructor which initialize the query access token with the auth client.
     *
     * @param QueryInterface $query
     * @param AuthClientInterface $authClient
     */
    public function __construct(AuthClientInterface $authClient)
    {
        $this->authClient = $authClient;
        $this->client = new Client();
        $this->throttle = 10;
    }

    /**
     * @return array
     */
    public function getResult(QueryInterface $query)
    {
        return new GoogleAnalyticsResponse(
            $this->mergeResults(
                $this->getRawData($query->getBaseUrlApi(), $query->build())
            )
        );
    }

    /**
     * Send http request to Googla analytics and gets the response.
     *
     * @param string $requestUrl
     *
     * @throws \Kairos\GoogleAnalyticsClientBundle\Exception\GoogleAnalyticsException
     *
     * @return mixed
     */
    protected function request($baseUrl, $params)
    {

        $response = $this->client->get($baseUrl, array(), array('query' => $params));

        if ($response->getStatusCode() != 200) {
            throw GoogleAnalyticsException::invalidQuery($response->getReasonPhrase());
        }

        $data = json_decode($response->getBody(), true);

        return $data;
    }

    protected function throttledRequest($baseUrl, $params, $userIp = null)
    {
        // init throttle for ip
        if(!isset($this->throttleTable[$userIp])) {
            $this->throttleTable[$userIp] = $this->throttle;
        }

        // init start time
        if(!isset($start)) {
            $start = microtime();
        }

        $data = $this->request($baseUrl, $params);

        $this->throttleTable[$userIp]--;

        // test rate limit
        if($this->throttleTable[$userIp] === 0) {
            $dt = 1000000-(microtime() - $start);
            // if dt > 0 means that the number of requests has been made in less than 1 second
            if($dt > 0) {
                // wait until we reach the second
                usleep($dt);
                // reset throttle for ip
                $this->throttleTable[$userIp] = $this->throttle;
            }
            // reset start time
            $start = microtime();
        }
        return $data;
    }

    protected function getRawData($baseUrl, array $requests, $result = array())
    {
        foreach ($requests as $request) {

            $userIp = isset($request['userIp']) ? $request['userIp'] : null;
            $data = $this->request($baseUrl, $request, $userIp);

            $result[] = $data;

            $startIndex = $data['query']['start-index'] + 1;
            // we check if there is pagination
            if(($data['totalResults'] >= $startIndex * $data['query']['max-results'])) {
                $subrequest = clone($request);
                $subrequest['start-index'] = $startIndex;
                $this->getRawData($baseUrl, array($subrequest), $result, $userIp);
            }
        }

        // release the throttle table to avoid memory leak
        $this->throttleTable = array();

        return $result;
    }

    /**
     * Merge all the datas in the basic format.
     *
     * @param $results
     *
     * @return array
     */
    protected function mergeResults($results)
    {
        $mergedResults = array();

        // Then merge result
        if (count($results) > 0) {
            // Init a $data var with the first result in order to initialize the structure
            $mergedResults = $results[0];

            // data that we want to merge are rows and totals for all results
            $totalsForAllResults = array();
            $rows = array();

            foreach ($results as $result) {
                empty($result['rows']) ? $result['rows'] = array() : null;
                $rows = array_merge_recursive($rows, $result['rows']);

                // Do the merge for the total result only on a first page if there is pagination
                if ($result['query']['start-index'] == 1) {
                    $totalsForAllResults = array_merge_recursive($totalsForAllResults, $result['totalsForAllResults']);
                }
            }

            // Set the final data with the merged values
            $mergedResults['rows'] = $rows;

            // Set the merged and sumed total
            foreach ($totalsForAllResults as $metric => $value) {
                $mergedResults['totalsForAllResults'][$metric] = is_array($value) ? array_sum($value) : $value;
            }
        }
        return $mergedResults;
    }

    /**
     * Sets an http client in order to make google analytics request.
     *
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
     * @return Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }
}
