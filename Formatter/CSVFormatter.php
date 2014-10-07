<?php

namespace Kairos\GoogleAnalyticsClientBundle\Formatter;

/**
 * Class CSVFormatter
 * @package Kairos\GoogleAnalyticsClientBundle\Formatter
 */
class CSVFormatter
{
    /**
     * CSV export of the reponse given by Google Analytics.
     *
     * @param array $response
     *
     * @return bool|\Symfony\Component\HttpFoundation\Response
     */
    public static function toCSV(array $response)
    {
        $keyDate = null;

        // Use the php memory to create the file
        $file = new \SplTempFileObject();

        // Set the header of the CSV
        $csvHeader = array();
        if (isset($response['columnHeaders']) && count($response['columnHeaders']) > 0) {
            foreach ($response['columnHeaders'] as $keyHead => $head) {
                $csvHeader[] = $head['name'];

                // if the header is date, set key
                if (strpos($head['name'], 'date') !== false) {
                    $keyDate = $keyHead;
                }
            }
        }
        $file->fputcsv($csvHeader);

        // Set the content of the CSV
        if (isset($response['rows']) && count($response['rows']) > 0) {
            foreach ($response['rows'] as $row) {
                $rowValue = array();
                foreach ($row as $keyValue => $value) {

                    // if the header key is date, format date
                    if ($keyValue === $keyDate) {
                        $rowValue[] = date('Y-m-d', strtotime($value));
                    } else {
                        $rowValue[] = $value;
                    }
                }
                $file->fputcsv($rowValue);
            }

            $response = new \Symfony\Component\HttpFoundation\Response();
            $response->setContent($file->getContents());
            $response->headers->set('Content-Type', 'application/force-download');
            $response->headers->set('Content-disposition', 'filename="export.csv"');

            return $response;
        }
        return false;
    }
}