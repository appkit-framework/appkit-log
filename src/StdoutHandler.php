<?php

namespace AppKit\Log;

use AppKit\Log\LogHandlerInterface;
use AppKit\Log\LogLevel;

class StdoutHandler implements LogHandlerInterface {
    const COLOR_RESET = "\033[0m";
    const COLOR_MODULE = "\033[34m";
    const LEVEL_COLOR_MAP = [
        LogLevel::Error -> value   => "\033[31m",
        LogLevel::Warning -> value => "\033[33m",
        LogLevel::Info -> value    => "\033[32m",
        LogLevel::Debug -> value   => "\033[90m"
    ];
    const LEVEL_TEXT_MAP = [
        LogLevel::Error -> value   => 'ERROR',
        LogLevel::Warning -> value => 'WARN ',
        LogLevel::Info -> value    => 'INFO ',
        LogLevel::Debug -> value   => 'DEBUG'
    ];

    private $printStackTraces;

    private $isTty;

    function __construct($printStackTraces) {
        $this -> printStackTraces = $printStackTraces;

        $this -> isTty = function_exists('posix_isatty') && posix_isatty(STDOUT);
    }
    
    public function log($time, $level, $moduleName, $moduleContext, $message, $exception) {
        echo $this -> onlyTty("\r").
             date('Y-m-d H:i:s  ', $time).
             $this -> onlyTty(self::LEVEL_COLOR_MAP[$level -> value]).
             self::LEVEL_TEXT_MAP[$level -> value].
             '  '.
             $this -> onlyTty(self::COLOR_MODULE).
             '['.
             $this -> shortClassName($moduleName);

        if($moduleContext !== null)
            echo ":$moduleContext";

        echo '] '.
             $this -> onlyTty(self::COLOR_RESET).
             $message;

        if($exception) {
            if($this -> printStackTraces)
                echo PHP_EOL.
                     PHP_EOL.
                     ((string) $exception).
                     PHP_EOL;
            else
                echo ': '.
                     $this -> shortClassName(get_class($exception)).
                     ': '.
                     $exception -> getMessage();
        }

        echo PHP_EOL;
    }

    private function shortClassName($fqcn) {
        $pos = strrpos($fqcn, '\\');
        return $pos === false ? $fqcn : substr($fqcn, $pos + 1);
    }

    private function onlyTty($string) {
        if($this -> isTty)
            return $string;
        return '';
    }
}
