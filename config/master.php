<?php

return [
    'master' => [
        'monitor_ip' => '0.0.0.0',
        'registe_port' => 9529,//slave注册监听端口
        'gettask_port' => 9530,//slave取任务监听端口
        'settask_port' => 9531,//slave回设任务状态监听端口
        'createtask_port' => 9528,//slave生成新任务监听端口
        'data_port' => 9532,//回插数据监听端口
        'worker_num' => 2,
        'task_worker_num' => 2,
        'daemonize' => 0,
        'open_cpu_affinity' => 1,
        'open_tcp_nodelay' => 1,
        'dispatch_mode' => 2,
        'open_eof_check' => false,
        'package_eof' => "\r\n",
        'chroot' => LIB_DIR,
        'user' => 'www-data',
        'group' => 'www-data',
        'log_file' => TEMP_DIR . '/swoole_master.log',
        'pid_file' => TEMP_DIR . '/server.pid',
        'process_name' => 'Fastcrawler',
    ],
    'api' => [
        'domain' => '127.0.0.1',
        'port' => 9527
    ]
];
