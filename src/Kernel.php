<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    // Add this method will disable the routing from the route.yaml
    // protected function configureRoutes(RoutingConfigurator $routes): void
    // {
    //     // $routes->import('../src/Infrastructure/User/Controller/', 'attribute');
    //     // $routes->import('../config/routes/api_platform.yaml');
    //     // $routes->import('../config/routes.yaml');
    // }
}
