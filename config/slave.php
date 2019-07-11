<?php

return [
    'slave' => [
        'master_ip' => '127.0.0.1',
        'createtask_port' => 9528,//slave生成新任务监听端口
        'registe_port' => 9529,//slave注册监听端口
        'gettask_port' => 9530,//slave取任务监听端口
        'settask_port' => 9531,//slave回设任务状态监听端口
        'data_port' => 9532,//回插数据监听端口
        'log_file' => TEMP_DIR . '/swoole_slave.log',
        'mid_file' => TEMP_DIR . '/mid',
    ]
];
