<?php

namespace Fastcrawler\Service;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Noodlehaus\Config;

class ConfigProvider implements ServiceProviderInterface {

    public function register(Container $pimple) {
        $pimple['config'] = function ($c) {
            if (MODE == 'master') {
                return new Config([CONFIG_DIR . '/master.php']);
            } else {
                return new Config(CONFIG_DIR . '/slave.php');
            }
        };
    }

}
