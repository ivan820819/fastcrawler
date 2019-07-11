<?php

namespace Fastcrawler\Service;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Fastcrawler\Message\Log;

class LogProvider implements ServiceProviderInterface {

    public function register(Container $pimple) {
        $pimple['log'] = function ($c) {
            return new Log('fastcrawler');
        };
    }

}
