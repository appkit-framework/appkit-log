<?php

namespace AppKit\Log;

use AppKit\Async\ExecutionContext;

use Throwable;

class Logger {
    private $handlers = [];
    private $modulePath = [];
    
    function __construct($module, $instance = null) {
        $this -> addModule($module, $instance);
    }
    
    public function log($level, $message, $localContext = [], $exception = null) {
        $time = microtime(true);

        $executionContext = ExecutionContext::get(self::class) ?? [];

        if($localContext instanceof Throwable) {
            $exception = $localContext;
            $localContext = [];
        }
        
        foreach($this -> handlers as $handlerRecord) {
            if($level -> value <= $handlerRecord['level'])
                $handlerRecord['handler'] -> log(
                    $time,
                    $level,
                    $executionContext,
                    $this -> modulePath,
                    $message,
                    $localContext,
                    $exception
                );
        }
    }
    
    public function error($message, $localContext = [], $exception = null) {
        $this -> log(LogLevel::Error, $message, $localContext, $exception);
    }
    
    public function warning($message, $localContext = [], $exception = null) {
        $this -> log(LogLevel::Warning, $message, $localContext, $exception);
    }
    
    public function info($message, $localContext = [], $exception = null) {
        $this -> log(LogLevel::Info, $message, $localContext, $exception);
    }
    
    public function debug($message, $localContext = [], $exception = null) {
        $this -> log(LogLevel::Debug, $message, $localContext, $exception);
    }

    public function addHandler($handler, $level) {
        $this -> handlers[] = [
            'handler' => $handler,
            'level' => $level -> value
        ];

        return $this;
    }

    public function withModule($module, $instance = null) {
        $new = clone $this;
        $new -> addModule($module, $instance);
        return $new;
    }

    public function setContext($key, $value) {
        $context = ExecutionContext::get(self::class) ?? [];
        $context[$key] = $value;
        ExecutionContext::set(self::class, $context);

        return $this;
    }

    private function addModule($module, $instance) {
        $this -> modulePath[] = [
            'module' => $module,
            'instance' => $instance
        ];
    }
}
