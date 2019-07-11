<?php

namespace Fastcrawler\Slave;

class Registe {
    
    private $midFile;
    
    public function __construct($midFile) {
        $this->midFile = $midFile;
    }

    public function onRegiste($client, $data) {
        $message = json_decode($data, true);
        if ($message['result']) {
            Client::$isRegiste = 1;
        } else {
            print_r($message);
        }
    }
    
    public function onConnect($client) {
        $mid = '';
        if (file_exists($this->midFile)) {
            $mid = file_get_contents($this->midFile);
        }
        if (!$mid) {
            $mid = uniqid('fast_', true);
            file_put_contents($this->midFile, $mid);
        }
        Client::$mid = $mid;
        $sockInfo = $client->getsockname();
        $message = json_encode(['mid'=>$mid, 'info'=>$sockInfo], JSON_UNESCAPED_UNICODE);
        $client->send($message);
    }
}
