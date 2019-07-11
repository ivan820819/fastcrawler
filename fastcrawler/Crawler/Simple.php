<?php

namespace Fastcrawler\Crawler;

use Swoole\Coroutine\Http\Client;

class Simple implements Page {

    public static function getContent($url, $data = [], $method = 'get') {
        $urlInfo = parse_url($url);
        $cli = new Client($urlInfo['host'], $urlInfo['port']);
        if ($method == 'get') {
            if (count($data)) {
                $cli->setHeaders($data);
            }
            $cli->get($urlInfo['path']);
        } else {
            $cli->post($urlInfo['path'], $data);
        }
        $result = $cli->body;
        $cli->close();
        return $result;
    }

}
