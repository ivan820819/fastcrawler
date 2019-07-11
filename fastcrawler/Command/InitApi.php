<?php

namespace Fastcrawler\Command;

use Swoole\Table;
use GuzzleHttp\Client;
use Fastcrawler\Core;

class InitApi {

    public static function execute() {
        $apiConfig = Core::$container['config']['api'];
        $table = false;
        $httpClient = new Client();
        $response = $httpClient->request('GET', 'http://'.$apiConfig['domain'].':'.$apiConfig['port']);
        if ($response->getStatusCode() == 200) {
            $apiContent = $response->getBody();
            $apiJson = json_decode($apiContent, true);
            if (is_array($apiJson['result']) && count($apiJson['result'])) {
                $table = new Table(1024);
                $table->column('key', Table::TYPE_STRING, 30);
                $table->column('address', Table::TYPE_STRING, 100);
                $isCreated = $table->create();
                if ($isCreated) {
                    foreach ($apiJson['result'] as $value) {
                        $table->set($value['api_key'], array('key' => $value['api_key'], 'address' => $value['api_address']));
                    }
                }
            }
        }
        Core::$container['api_table'] = function ($c) use ($table) {
            return $table;
        };
    }
}
