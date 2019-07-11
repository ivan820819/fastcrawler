<?php

namespace Fastcrawler;

use Pimple\Container;
use Fastcrawler\Service\ConfigProvider;
use Fastcrawler\Service\EngineProvider;
use Fastcrawler\Service\LogProvider;
use Fastcrawler\Exception\ServerException;

class Core {

    public static $container;

    public static function init() {
        self::$container = new Container();
        $error = new ServerException();
        register_shutdown_function(array($error, 'shutdown'));
        self::registeService();
        return new static;
    }
    
    private static function registeService() {
        self::$container->register(new ConfigProvider());
        self::$container->register(new LogProvider());
        self::$container->register(new EngineProvider());
    }
}
