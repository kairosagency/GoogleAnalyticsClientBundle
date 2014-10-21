# Google Analytics Client Bundle #
[![Gitter](https://badges.gitter.im/Join Chat.svg)](https://gitter.im/kairosagency/GoogleAnalyticsClientBundle?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

[![Build Status](https://travis-ci.org/kairosagency/GoogleAnalyticsClientBundle.svg?branch=develop)](https://travis-ci.org/kairosagency/GoogleAnalyticsClientBundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/kairosagency/GoogleAnalyticsClientBundle/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/kairosagency/GoogleAnalyticsClientBundle/?branch=develop)
[![Code Coverage](https://scrutinizer-ci.com/g/kairosagency/GoogleAnalyticsClientBundle/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/kairosagency/GoogleAnalyticsClientBundle/?branch=develop)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/2306bd64-d0e6-420f-bdf1-7a7dd2c115c3/mini.png)](https://insight.sensiolabs.com/projects/2306bd64-d0e6-420f-bdf1-7a7dd2c115c3)

This bundle provides a way to get google analytics data using the certificate-based authentication with google analytics.

## Documentation ##

### Bundle setup ###

Install the bundle via composer :

```json
"require": {
    ...
    "kairos/google-analytics-client-bundle": "1.0.0",
    ...
}
```

Register your bundle in your AppKernel.php

```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            ...
            new Kairos\GoogleAnalyticsClientBundle\KairosGoogleAnalyticsClientBundle(),
            ...
        );
    ....
    }
}
```

Config for your config.yml :

```
kairos_google_analytics_client:
    gapi_id: xxx
    gapi_account: xxx
    oauth:
        client_email: xxx@xxx
        private_key: DIR/xxx.p12
```

### Usage ###

[Usage](https://github.com/kairosagency/GoogleAnalyticsClientBundle/blob/master/Resources/doc/usage.md)

### Todos ###

* Add Tests

## License ##

[Mozilla Public License 2.0](https://www.mozilla.org/MPL/2.0/)

## Acknowledgements ##

Thanks to [Widop Google Analytics Bundle](https://github.com/widop/google-analytics) where i've taken information.
