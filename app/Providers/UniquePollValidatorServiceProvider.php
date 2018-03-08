<?php

namespace Framadate\Providers;

use Framadate\Constraint\UniquePollConstraintValidator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application;

class UniquePollValidatorServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['validator.unique_poll'] = function () use ($app) {
            $validator =  new UniquePollConstraintValidator();
            $validator->setPollService($app['poll.service']);

            return $validator;
        };
    }

    public function boot(Application $app) {}
}
