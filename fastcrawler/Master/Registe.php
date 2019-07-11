<?php

namespace Fastcrawler\Master;

use Fastcrawler\Crawler\Simple;

class Registe {

    public function receive($server, $fd, $reactor_id, $data) {
        $message = json_decode($data, true);
        $registeUrl = getApiUrl('registe_slave');
        $result = Simple::getContent($registeUrl, $message, 'post');
        $server->send($fd, $result);
    }
}
