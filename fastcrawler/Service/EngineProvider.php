<?php

namespace Fastcrawler\Service;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Fastcrawler\Master\Server;
use Fastcrawler\Slave\Client;

class EngineProvider implements ServiceProviderInterface {

    public function register(Container $pimple) {
        $pimple['master_config'] = $pimple['config']['master'];
        $pimple['slave_config'] = $pimple['config']['slave'];
        $pimple['engine'] = function ($c) {
            if (MODE == 'master') {
                return new Server($c['master_config']);
            } else {
                return new Client($c['slave_config']);
            }
        };
    }

}
