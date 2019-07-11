<?php

function getApiUrl($apiName = '') {
    $apiTable = \Fastcrawler\Core::$container['api_table'];
    if ($apiTable instanceof \Swoole\Table) {
        return $apiTable->get($apiName, 'address');
    } else {
        return false;
    }
}
