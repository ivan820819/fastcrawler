<?php

namespace Fastcrawler\Exception;

use Exception;
use Fastcrawler\Core;

class ServerException extends Exception {

    private $httpCode = [
        200 => 'OK',
        400 => 'Bad Request',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Forbidden Method',
        500 => 'Internet Server Error',
        503 => 'Service Unavailable'
    ];
    
    public function __construct($httpcode = 200, $msg = '') {
        $this->code = $httpcode;
        if (empty($msg)) {
            $this->message = $this->httpCode[$httpcode];
        } else {
            $this->message = $msg . ' ' . $this->httpCode[$httpcode];
        }
    }
    
    public function response() {
        $info = [
            'code'    => $this->getCode(),
            'message' => $this->getMessage(),
        ];
        $debug_mode = Core::$container['config']['debug_mode'];
        if ($debug_mode) {
            $info['infomations'] = [
                'file'  => $this->getFile(),
                'line'  => $this->getLine(),
                'trace' => $this->getTrace(),
            ];
        }
        return json_encode($info, JSON_UNESCAPED_UNICODE);
    }

    public function shutdown() {
        $error = error_get_last();
        if (isset($error['type'])) {
            switch ($error['type']) {
                case E_ERROR :
                case E_PARSE :
                case E_CORE_ERROR :
                case E_COMPILE_ERROR :
                    $message = $error['message'];
                    $file = $error['file'];
                    $line = $error['line'];
                    $log = $this->message." : $message ($file:$line)\nStack trace:\n";
                    $trace = debug_backtrace();
                    foreach ($trace as $i => $t) {
                        if (!isset($t['file'])) {
                            $t['file'] = 'unknown';
                        }
                        if (!isset($t['line'])) {
                            $t['line'] = 0;
                        }
                        if (!isset($t['function'])) {
                            $t['function'] = 'unknown';
                        }
                        $log .= "#$i {$t['file']}({$t['line']}): ";
                        if (isset($t['object']) and is_object($t['object'])) {
                            $log .= get_class($t['object']) . '->';
                        }
                        $log .= "{$t['function']}()\n";
                    }
                    if (isset($_SERVER['REQUEST_URI'])) {
                        $log .= '[QUERY] ' . $_SERVER['REQUEST_URI'];
                    }
                    return $log;
                default:
                    break;
            }
        }
    }
}
