<?php

namespace Fastcrawler\Slave;

use Fastcrawler\Core;
use League\Pipeline\Pipeline;
use Swoole\Process;
use Swoole\Client as TcpClient;

class Client {

    private $config;
    
    private $log;
    
    public static $isRegiste = 0;
    
    public static $mid = '';

    public function __construct($config) {
        $this->config = $config;
        $this->log = Core::$container['log'];
        $this->log->pushHandler($this->config['log_file']);
    }
    
    public function execute() {
        $this->createRegistePort();
        $this->createGetTaskPort();
    }
    
    private function createRegistePort() {
        $registeSlave = new Registe($this->config['mid_file']);
        $registeClient = new TcpClient(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
        $registeClient->on('receive', array($registeSlave, 'onRegiste'));
        $registeClient->on('connect', array($registeSlave, 'onConnect'));
        $registeClient->on('close', array($this, 'onClose'));
        $registeClient->on('error', array($this, 'onError'));
        $registeClient->connect($this->config['master_ip'], $this->config['registe_port']);
    }
    
    private function createGetTaskPort() {
        $getTaskSlave = new Task();
        $getTaskClient = new TcpClient(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
        $getTaskClient->on('receive', array($getTaskSlave, 'handle'));
        $getTaskClient->on('connect', array($getTaskSlave, 'fetch'));
        $getTaskClient->on('close', array($this, 'onClose'));
        $getTaskClient->on('error', array($this, 'onError'));
        $getTaskClient->connect($this->config['master_ip'], $this->config['gettask_port']);
    }
    
    public function onClose($client) {
        
    }
    
    public function onError($client) {
        
    }
}
