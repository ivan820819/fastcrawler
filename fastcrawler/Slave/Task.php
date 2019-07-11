<?php

namespace Fastcrawler\Slave;

class Task {
    
    private static $timeId = 0;
    
    private static $doTask = 0;
    
    private static $doingTask = 0;

    public function handle($client, $data) {
        echo 'receive server task data '.$data."\n";
        $message = json_decode($data, true);
        if (isset($message['result']) && $message['result']) {
            
        } else {
            $this->fetch($client);
        }
    }
    
    public function fetch($client) {
        self::$timeId = swoole_timer_tick(1000, function() use ($client) {
            if (Client::$isRegiste) {
                $client->send(Client::$mid);
                swoole_timer_clear(self::$timeId);
            } else {
                echo "client not registe\n";
            }
        });
    }
}
