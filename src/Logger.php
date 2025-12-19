<?php

namespace AppKit\Log;

class Logger {
    private $handlers;
    private $moduleName;
    private $moduleContext;
    
    function __construct($module, $context = null) {
        $this -> handlers = [];
        $this -> setModule($module, $context);
    }
    
    public function log($level, $message, $exception = null) {
        $time = microtime(true);
        
        foreach($this -> handlers as $handlerRecord) {
            if($level -> value <= $handlerRecord['level'])
                $handlerRecord['handler'] -> log(
                    $time,
                    $level,
                    $this -> moduleName,
                    $this -> moduleContext,
                    $message,
                    $exception
                );
        }
    }
    
    public function error($message, $exception = null) {
        $this -> log(LogLevel::Error, $message, $exception);
    }
    
    public function warning($message, $exception = null) {
        $this -> log(LogLevel::Warning, $message, $exception);
    }
    
    public function info($message) {
        $this -> log(LogLevel::Info, $message);
    }
    
    public function debug($message) {
        $this -> log(LogLevel::Debug, $message);
    }

    public function addHandler($handler, $level) {
        $this -> handlers[] = [
            'handler' => $handler,
            'level' => $level -> value
        ];

        return $this;
    }

    public function withModule($module, $context = null) {
        $new = clone $this;
        $new -> setModule($module, $context);
        return $new;
    }

    private function setModule($module, $context) {
        $this -> moduleName = get_class($module);
        $this -> moduleContext = $context;
    }
}
