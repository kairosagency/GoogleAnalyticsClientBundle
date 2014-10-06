<?php

namespace Kairos\GoogleAnalyticsClientBundle\Consumer;

use Kairos\GoogleAnalyticsClientBundle\Formatter\CSVFormatter;
use Kairos\GoogleAnalyticsClientBundle\Formatter\DatatableFormatter;

class Response
{
    /** @var array */
    protected $rawResponse;

    /** @var array */
    protected $totalsForAllResults;

    /**
     * @param array $response
     */
    public function __construct(array $response)
    {
        $this->rawResponse = $response;

        if (isset($response['totalsForAllResults'])) {
            $this->totalsForAllResults = $response['totalsForAllResults'];
        }
    }

    /**
     * @return array
     */
    public function getRawResponse()
    {
        return $this->rawResponse;
    }

    /**
     * Gets the totals for all results.
     *
     * @return array The totals for all results.
     */
    public function getTotalsForAllResults()
    {
        return $this->totalsForAllResults;
    }

    /**
     * Convert data to DataTableJson format
     *
     * @return array
     */
    public function toDataTable()
    {
        $datatableFormatter = new DatatableFormatter();
        return $datatableFormatter->toDataTable($this->rawResponse);
    }


    /**
     * @return bool|\Symfony\Component\HttpFoundation\Response
     */
    public function toCSV()
    {
        $CSVFormatter = new CSVFormatter();
        return $CSVFormatter->toCSV($this->rawResponse);
    }
}