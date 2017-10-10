<?php

namespace JincorTech\AuthClient\Tests;

use JincorTech\AuthClient\AuthClientServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public function getPackageProviders($application)
    {
        return [
            AuthClientServiceProvider::class,
        ];
    }
}
