<?php

namespace Kairos\GoogleAnalyticsClientBundle;

use Kairos\GoogleAnalyticsClientBundle\DependencyInjection\Compiler\CacheProviderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class KairosGoogleAnalyticsClientBundle
 * @package Kairos\GoogleAnalyticsClientBundle
 */
class KairosGoogleAnalyticsClientBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new CacheProviderPass());
    }
}
