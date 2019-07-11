<?php

namespace Fastcrawler\Master;

use Fastcrawler\Crawler\Simple;

class Task {

    public function get($server, $fd, $reactor_id, $data) {
        $fetchTaskUrl = getApiUrl('fetch_task');
        $result = Simple::getContent($fetchTaskUrl, $data, 'post');
        $server->send($fd, $result);
    }
    
    public function set($server, $fd, $reactor_id, $data) {
        
    }
    
    public function create($server, $fd, $reactor_id, $data) {
        
    }
}
