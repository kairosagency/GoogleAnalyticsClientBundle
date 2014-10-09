# Usage #

## Get your credentials ##

As you have read in the README, the library allows you to request the google analytics service without user interaction.
In order to make it possible, you need to create a [Google Service Account](https://developers.google.com/console/help/new/#usingkeys).

At the end, you should have:

 * `client_`: an email address which should look like `XXXXXXXXXXXX@developer.gserviceaccount.com`.
 * `profile_id`: a view ID which should look like `ga:XXXXXXXX`.
 * `private_key`: a PKCS12 certificate file

## Auth client ##

You can use the auth client manually ([Check the constructor of the client](https://github.com/kairosagency/GoogleAnalyticsClientBundle/blob/master/AuthClient/P12AuthClient.php)) but there is a service initialize in order to use the client.

```
$container->get('kairos_google_analytics_client.p12_auth_client');
```

## Query ##

You can have a look to the [Google Api Reference](https://developers.google.com/analytics/devguides/reporting/core/v3/reference) in order to see the value that you can use

``` php
use Kairos\GoogleAnalyticsClientBundle\Consumer\Query;

$profileId = 'ga:XXXXXXXX';
$baseUrlApi = 'https://www.googleapis.com/analytics/v3/data/ga';
$query = new Query($profileId, $baseUrlApi);

// Default values :)
$query->setMetrics(array('ga:pageviews'));
$query->setStartDate(new \DateTime('now -1 Month'));
$query->setEndDate(new \DateTime('now));
$query->setStartIndex(1);
$query->setMaxResults(10000);

// Others functions
$query->setDimensions(array('ga:browser', 'ga:city'));
$query->setSorts(array('ga:country', 'ga:browser'));
$query->setSegment('gaid::10');
```

Filters are particulars, you can add a separator in order to manage the AND/OR logic

``` php
// Get the data for the (France OR Spain)
$query->setFilters(array('ga:country==France', 'ga:country==Spain')); // The comma separator is the default value
$query->setFilters(array('ga:country==France', 'ga:country==Spain'), ',');

// Get the data for the (France AND Spain)
$query->setFilters(array('ga:country==France', 'ga:country==Spain'), ';');

// Get the data for the ((France AND Canada) OR Spain)
$query->setFilters(array('ga:country==France;ga:country==Canada', 'ga:country==Spain'), ',');
```

## Request ##

We get the request object in order to request google analytics and get the data

``` php
use Kairos\GoogleAnalyticsClientBundle\Consumer\Request;

$request = new Request($query, $container->get('kairos_google_analytics_client.p12_auth_client'));

//In order to get the result
$request->getResult()
```

## Reponse/Formatters ##

The response object provides manipulation of data, if you need to get the (raw data...)
We defines some formatters in order to provides some others functions

``` php

// Response
use Kairos\GoogleAnalyticsClientBundle\Consumer\Request;
use Kairos\GoogleAnalyticsClientBundle\Consumer\Response;
use Kairos\GoogleAnalyticsClientBundle\Formatter\CSVFormatter;
use Kairos\GoogleAnalyticsClientBundle\Formatter\DatatableFormatter;

$request = new Request($query, $container->get('kairos_google_analytics_client.p12_auth_client'));
$data = $request->getResult();

$response = new Response($data);

$response->getRawResponse();
$response->getTotalsForAllResults();
$response->getTotalResults();

// CSVFormatter
CSVFormatter::toCSV($data); // OR
CSVFormatter::toCSV($response->getRawResponse());

// DatatableFormatter
$GADatatableFormatter = new DatatableFormatter();

$GADatatableFormatter->toDataTable($data) // OR
$GADatatableFormatter->toDataTable($response->getRawResponse())

```
