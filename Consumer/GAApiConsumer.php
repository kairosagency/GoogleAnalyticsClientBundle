<?php

namespace Kairos\GoogleAnalyticsClientBundle\Consumer;

use Kairos\GoogleAnalyticsClientBundle\AuthClient\AuthClientInterface;
use Guzzle\Http\Client as HttpClient;
use Symfony\Component\HttpFoundation\Response;

class GAApiConsumer
{
    /**
     * @var \Kairos\GoogleAnalyticsClientBundle\AuthClient\AuthClientInterface
     */
    protected $authClient;

    protected $gapiId;
    protected $baseUrlApi;

    /**
     *
     */
    const
        BASE_NB_CHAR_GA_URL     = 300,
        LIMIT_NB_CHAR_GA_URL    = 2000
    ;

    public function __construct(AuthClientInterface $authClient, $gapiId, $baseUrlApi)
    {
        $this->authClient = $authClient;
        $this->gapiId = $gapiId;
        $this->baseUrlApi = $baseUrlApi;
    }

    /**
     * Function that call the google api in order to get specified informations
     *
     * @param array $params
     * @return array
     */
    public function apiCall($params = array())
    {
        // Google analytics parameters list
        $defaultParams = array(
            "access_token"  => $this->authClient->getAccessToken(),
            "ids"           => "ga:" . $this->gapiId,
            "metrics"       => "ga:pageviews",
            "start-date"    => date('Y-m-d', strtotime(date('Y-m-d') . ' -1 Month')),
            "end-date"      => date('Y-m-d'),
            "max-results"   => 10000
        );

        if(count($params) > 0){
            $defaultParams = array_merge($defaultParams, $params);
        }

        $client = new HttpClient();
        $request = $client->get($this->baseUrlApi . '?' . http_build_query($defaultParams));
        $response = $request->send();

        if ($response->getStatusCode() == 200) {
            return array('result' => json_decode($response->getBody(), true));
        }
        else {
            return array('error' => $response->getReasonPhrase());
        }
    }

    /**
     * Get the data from google api
     *
     * @param array $params
     * @return null|Response
     */
    public function getRawData($params = array())
    {
        // We call the google analytics api with the params
        $result = $this->apiCall($params);

        // Check the result
        if (isset($result['error'])) {
            return new Response('Problem Api Call : ' . $result['error']);
        }

        return isset($result['result']) ? $result['result'] : null;
    }


    /**
     * Get the data with the multiple way
     * @param $gaParams
     * @param $keycodes
     * @return array
     */
    public function getDataByFilters($gaParams, $filters)
    {
        $multipleGAResults   = array();
        $mergedResults       = array();

        // Get an array of parameters valid (check the url limit)
        $gApiRequestParams = $this->prepareGApiRequestParams($gaParams);

        // Get the data and check the pagination
        foreach($gApiRequestParams as $params) {
            $multipleGAResults = $this->getFullData($params, $multipleGAResults);
        }

        // Then merge result (multiple call and pagination)
        if(count($multipleGAResults) > 0) {
            $mergedResults = $this->mergeMultipleGAResults($multipleGAResults);
        }

        return $mergedResults;
    }



    /**
     * Get the base url length with the new params passed in the javascript call (Without filters)
     *
     * @param $gaParams
     * @return int
     */
    private function getBaseUrlLengthWithParams($gaParams)
    {
        $baseUrlLength = self::BASE_NB_CHAR_GA_URL;
        $baseUrlLength += strlen($this->baseUrlApi . '?' . http_build_query($gaParams));
        return $baseUrlLength;
    }

    /**
     * Prepare the parameters with all the keycodes to send to GA
     * @param $gaParams
     * @param $items
     * @param $type
     * @return array of parameters to send to Google
     */
    public function prepareGApiRequestParams($gaParams)
    {
        $gApiRequestParams  = array();
        $baseUrlLength      = $this->getBaseUrlLengthWithParams($gaParams);
        $currentUrlLength   = $baseUrlLength;
        $filters            = null;
        $filtersTmp         = null;
        $indexItem          = 0;

        if(count($items) > 0) {
            foreach($items as $item) {

                // We have to add comma separated or not and get the length of the filter
                $filtersTmp .= ($indexItem > 0) ? ',' : '';
                $filtersTmp .= 'ga:pagePath==/' . $type . '/' . $item;
                $currentUrlLength += strlen($this->baseUrlApi . '?' . http_build_query(array_merge($gaParams, array('filters' => $filtersTmp))));

                // If the limit url length is reached with send a request to google
                // In order to get the data for the keycodes
                if($currentUrlLength > self::LIMIT_NB_CHAR_GA_URL) {

                    $gApiRequestParams[] = array_merge($gaParams, array('filters' => $filters));

                    // Reset the currentUrlLength with the $baseUrlLength and last keycode
                    $currentUrlLength = $baseUrlLength + strlen($this->baseUrlApi . '?' . http_build_query(array_merge($gaParams, array('filters' => 'ga:pagePath==/' . $type . '/' . $item))));

                    // And reset $filters and $indexKeycode in order to send a new request to google
                    $filters        = null;
                    $filtersTmp     = null;
                    $indexItem   = 0;
                }

                // We add the keycode to the filter
                $filters .= ($indexItem > 0) ? ',ga:pagePath==/' . $type . '/' . $item : 'ga:pagePath==/' . $type . '/' . $item;

                $indexItem++;
            }
            $gApiRequestParams[] = array_merge($gaParams, array('filters' => $filters));
        }
        return $gApiRequestParams;
    }

}