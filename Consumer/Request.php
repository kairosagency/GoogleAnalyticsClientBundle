<?php

namespace Kairos\GoogleAnalyticsClientBundle\Consumer;

use Guzzle\Http\Client as HttpClient;
use Kairos\GoogleAnalyticsClientBundle\AuthClient\AuthClientInterface;
use Kairos\GoogleAnalyticsClientBundle\Exception\GoogleAnalyticsException;

class Request
{
    protected $query;
    protected $authClient;

    public function __construct(Query $query, AuthClientInterface $authClient)
    {
        $this->authClient = $authClient;
        $this->query = $query;

        $this->query->setAccessToken($this->authClient->getAccessToken());
    }


    public function request($requestUrl)
    {
        $client = new HttpClient();
        $request = $client->get($requestUrl);
        $response = $request->send();

        if ($response->getStatusCode() != 200) {
            throw GoogleAnalyticsException::invalidQuery($response->getReasonPhrase());
        }

        $data = json_decode($response->getBody(), true);

        return $data;
    }

    public function getResult()
    {
        return $this->mergeResults($this->getGAResult());
    }

    /**
     * Check if the data has pagination and get all the data if there is a pagination
     *
     * @return array
     */
    protected function getGAResult()
    {
        $results = array();
        $requestUrls = $this->query->build();

        foreach($requestUrls as $url) {

            $data = $this->request($url);
            $results[] = $data;

            $startIndex = $data['query']['start-index'] + 1;
            while(($data['totalResults'] >= $startIndex * $data['query']['max-results'])) {
                $this->query->setStartIndex($startIndex);
                $results[] = $this->query->build();
                $startIndex++;
            }
        }

        return $results;
    }


    /**
     * Merge all the datas in the basic format
     * @param $multipleGAData
     * @return array
     */
    /**
     * @param $results
     * @return array
     */
    protected function mergeResults($results)
    {
        $mergedResults = array();

        // Then merge result
        if(count($results) > 0) {
            // Init a $data var with the first result in order to initialize the structure
            $mergedResults = $results[0];

            // data that we want to merge are rows and totals for all results
            $totalsForAllResults    = array();
            $rows                   = array();

            foreach($results as $result) {
                empty($result['rows']) ? $result['rows'] = array() : null;
                $rows = array_merge_recursive($rows, $result['rows']);

                // Do the merge for the total result only on a first page if there is pagination
                if($result['query']['start-index'] == 1) {
                    $totalsForAllResults = array_merge_recursive($totalsForAllResults, $result['totalsForAllResults']);
                }
            }

            // Set the final data with the merged values
            $mergedResults['rows'] = $rows;

            // Set the merged and sumed total
            foreach($totalsForAllResults as $metric => $value) {
                $mergedResults['totalsForAllResults'][$metric] = is_array($value) ? array_sum($value) : $value;
            }
        }

        return $mergedResults;
    }


}