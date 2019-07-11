<?php

namespace Fastcrawler\Master;

use Fastcrawler\Core;
use Fastcrawler\Exception\ServerException;
use League\Pipeline\Pipeline;
use Fastcrawler\Command\InitApi;
use Swoole\Process;
use Swoole\Server as TcpServer;

class Server {

    private $config;
    
    private $log;
    
    private $server;
    
    private $masterId = '';

    public function __construct($config) {
        $this->config = $config;
        $this->log = Core::$container['log'];
        $this->log->pushHandler($this->config['log_file']);
        InitApi::execute();
    }
    
    public function execute($command = '') {
        if (file_exists($this->config['pid_file'])) {
            $this->masterId = file_get_contents($this->config['pid_file']);
        }
        switch ($command) {
            case 'start':
                return $this->start();
                break;
            case 'restart':
                return $this->restart();
                break;
            case 'stop':
                return $this->stop();
                break;
            case 'reload':
                return $this->reload();
                break;
            case 'startd':
                return $this->start(true);
                break;
            default:
                return false;
                break;
        }
    }
    
    private function detectMaster() {
        return $this->masterId && Process::kill($this->masterId, 0);
    }
    
    private function start($daemonize = false) {
        $isMasterAlive = $this->detectMaster();
        if ($isMasterAlive) {
            $this->log->addInfo("服务器已经启动！");
            return false;
        }
        $this->config['daemonize'] = $daemonize;
        $this->log->addInfo("服务器启动成功");
        $this->run();
    }
    
    private function stop() {
        $isMasterAlive = $this->detectMaster();
        if (!$isMasterAlive) {
            $this->log->addInfo("服务器已经停止！");
            return false;
        }
        $this->log->addInfo("服务器正在停止...");
        Process::kill($this->masterId);
        $timeout = 5;
        $start_time = time();
        while (1) {
            $isMasterAlive = $this->detectMaster();
            if ($isMasterAlive) {
                if (time() - $start_time >= $timeout) {
                    $this->log->addInfo("服务器停止失败！");
                    exit;
                }
                usleep(10000);
                continue;
            }
            $this->log->addInfo("服务器成功停止！");
            break;
        }
        return true;
    }
    
    private function restart() {
        $stopResult = $this->stop();
        if ($stopResult) {
            return $this->start(true);
        } else {
            return false;
        }
    }
    
    private function reload() {
        Process::kill($this->masterId, SIGUSR1);
        $this->log->addInfo("服务器重载成功！");
        return true;
    }
    
    private function setRegistePort() {
        $registeMaster = new Registe();
        $this->server->on('Receive', array($registeMaster, 'receive'));
    }
    
    private function setTaskPort() {
        $gettask_port = $this->server->addListener($this->config['monitor_ip'], $this->config['gettask_port'], SWOOLE_SOCK_TCP);
        $settask_port = $this->server->addListener($this->config['monitor_ip'], $this->config['settask_port'], SWOOLE_SOCK_TCP);
        $createtask_port = $this->server->addListener($this->config['monitor_ip'], $this->config['createtask_port'], SWOOLE_SOCK_TCP);
        $taskMaster = new Task();
        $gettask_port->on('Receive', array($taskMaster, 'get'));
        $settask_port->on('Receive', array($taskMaster, 'set'));
        $createtask_port->on('Receive', array($taskMaster, 'create'));
    }
    
    private function setDataPort() {
        $data_port = $this->server->addListener($this->config['monitor_ip'], $this->config['data_port'], SWOOLE_SOCK_TCP);
        $dataMaster = new Data();
        $data_port->on('Receive', array($dataMaster, 'insert'));
    }
    
    private function run() {
        $this->server = new TcpServer($this->config['monitor_ip'], $this->config['registe_port']);
        $this->server->set($this->config);
        $this->server->on('Start', array($this, 'onMasterStart'));
	$this->server->on('Shutdown', array($this, 'onShutdown'));
	$this->server->on('ManagerStart', array($this, 'onManagerStart'));
        $this->server->on('WorkerStart', array($this, 'onWorkerStart'));
        $this->server->on('WorkerStop', array($this, 'onWorkerStop'));
        $this->server->on('WorkerError', array($this, 'onWorkerError'));
        $this->server->on('Task', array($this, 'onTask'));
        $this->server->on('Finish', array($this, 'onFinish'));
        $this->server->on('Connect', array($this, 'onConnect'));
        $this->server->on('Close', array($this, 'onClose'));
        $this->setRegistePort();
        $this->setTaskPort();
        $this->setDataPort();
        $this->server->start();
    }
    
    public function onMasterStart(\swoole_server $server) {
        swoole_set_process_name('master process ' . $this->config['process_name']);
    }
    
    public function onShutdown(\swoole_server $server) {
        unlink($this->config['pid_file']);
    }
    
    public function onManagerStart(\swoole_server $server) {
        swoole_set_process_name('manager process ' . $this->config['process_name']);
    }
    
    public function onWorkerStart(\swoole_server $server, int $worker_id) {
        if(function_exists('apc_clear_cache')){
            apc_clear_cache();
        }
        if(function_exists('opcache_reset')){
            opcache_reset();
        }
        if ($worker_id >= $server->setting['worker_num']) {
            swoole_set_process_name('task worker process '.$this->config['process_name']);
        } else {
            swoole_set_process_name('event worker process '.$this->config['process_name']);
        }
    }
    
    public function onWorkerStop(\swoole_server $server, int $worker_id) {
        
    }
    
    public function onWorkerError(\swoole_server $server, int $worker_id, int $worker_pid, int $exit_code, int $signal) {
        $this->log->addInfo('worker_id : ' . $worker_id . '; worker_pid : ' . $worker_pid . '; exit_code : ' . $exit_code . '; signal : ' . $signal);
    }
    
    public function onTask(\swoole_server $server, int $task_id, int $src_worker_id, $data) {
        
    }
    
    public function onFinish(\swoole_server $server, int $task_id, $data) {
        
    }
    
    public function onConnect(\swoole_server $server, int $fd, int $reactorId) {
        echo 'server connect fd '.$fd;
        $clientInfo = $server->getClientInfo($fd);
        print_r($clientInfo);
    }
    
    public function onClose(\swoole_server $server, int $fd, int $reactorId) {
        echo 'server close fd '.$fd;
        $clientInfo = $server->getClientInfo($fd);
        print_r($clientInfo);
    }
}
