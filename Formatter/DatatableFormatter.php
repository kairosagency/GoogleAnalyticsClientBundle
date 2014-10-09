<?php

namespace Kairos\GoogleAnalyticsClientBundle\Formatter;

class DatatableFormatter
{
    /** @var array */
    protected $label;

    /** @var string */
    protected $date;

    /** @var array */
    protected $number;

    /**
     * Constructor of the datatable formatter.
     * Initialize the list of available labels.
     */
    public function __construct()
    {
        $this->label = array(
            'ga:browser'              => 'browser',
            'ga:pageviews'            => 'pageviews',
            'ga:visitors'             => 'visitors',
            'ga:visits'               => 'visits',
            'ga:newVisits'            => 'new visits',
            'ga:date'                 => 'date',
            'ga:hour'                 => 'hour',
            'ga:year'                 => 'year',
            'ga:day'                  => 'day',
            'ga:month'                => 'month',
            'ga:continent'            => 'continent',
            'ga:subContinent'         => 'subContinent',
            'ga:country'              => 'country',
            'ga:region'               => 'region',
            'ga:city'                 => 'city',
            'ga:language'             => 'language',
            'ga:mobileDeviceBranding' => 'mobile device branding',
            'ga:mobileDeviceModel'    => 'mobile device model',
            'ga:pagePath'             => 'qr',
        );
    }

    /**
     * Convert data to DataTableJson format.
     *
     * @param $data
     * @param array $label
     *
     * @return array
     */
    public function toDataTable(array $response, $label = array())
    {

        //reset date and number array;
        $this->date = null;
        $this->number = array();

        $cols = isset($response['columnHeaders']) ? $response['columnHeaders'] : null;
        $rows = isset($response['rows']) ? $response['rows'] : null;

        $this->label = array_merge($this->label, $label);

        if (count($cols) > 0) {
            $cols = array_map(array($this, 'convertCol'), array_keys($cols), $cols);
        }

        if (count($rows) > 0) {
            $rows = array_map(array($this, 'convertRow'), $rows);
        }

        return array('cols' => $cols, 'rows' => $rows);
    }

    /**
     * Sanitize datatypes for use in datatable.
     * Format the cols for use in datatable.
     *
     * @param $key
     * @param $col
     *
     * @return array
     */
    public function convertCol($key, $col)
    {
        $res = array();
        if ($col['dataType'] == "INTEGER") {
            $res['type'] = "number";
            $this->number[] = $key;
        } else {
            $res['type'] = strtolower($col['dataType']);
        }

        $label = $this->label;
        $res['label'] = isset($label[$col['name']]) ? $label[$col['name']] : $col['name'];
        $res['id'] = $col['name'];

        if ($col['name'] == "ga:date") {
            $res['type'] = "date";
            $this->date = $key;
        }

        return $res;
    }

    /**
     * Sanitize row for use in datatable.
     *
     * @param $row
     *
     * @return array
     */
    public function convertRow($row)
    {
        $res = array();
        foreach ($row AS $key => $value) {
            if ($key === $this->date) {
                $date = \DateTime::createFromFormat('Ymd', $value);
                $res[$key] = array(
                    'v' => 'Date(' . $date->format('Y') . ',' . (intval($date->format('m')) - 1) . ',' . $date->format('d') . ')'
                );
            } elseif (in_array($key, $this->number)) {
                $res[$key] = array('v' => intval($value));
            } else {
                $res[$key] = array('v' => $value);
            }
        }
        return array('c' => $res);
    }
}
