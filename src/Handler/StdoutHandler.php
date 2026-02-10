<?php

namespace AppKit\Log\Handler;

use AppKit\Log\LogLevel;

class StdoutHandler implements LogHandlerInterface {
    const COLOR_RESET = "\033[0m";
    const COLOR_CONTEXT = "\033[35m";
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
    
    public function log(
        $time,
        $level,
        $executionContext,
        $modulePath,
        $message,
        $localContext,
        $exception
    ) {
        // Time
        echo $this -> onlyTty("\r"),
             date('Y-m-d H:i:s  ', $time);

        // Level
        echo $this -> onlyTty(self::LEVEL_COLOR_MAP[$level -> value]),
             self::LEVEL_TEXT_MAP[$level -> value],
             '  ';

        // Execution context
        if(!empty($executionContext))
             echo $this -> onlyTty(self::COLOR_CONTEXT),
                  '[', $this -> formatArray($executionContext), '] ';

        // Module
        $module = end($modulePath);
        echo $this -> onlyTty(self::COLOR_MODULE),
             '[',
             $this -> shortClassName($module['module']);

        if($module['instance'] !== null)
            echo ':', $module['instance'];

        echo '] ';

        // Message
        echo $this -> onlyTty(self::COLOR_RESET),
             $message;

        // Local context
        if(!empty($localContext))
             echo ', ', $this -> formatArray($localContext);

        // Exception
        if($exception) {
            echo ': ', $this -> shortClassName(get_class($exception));

            $exceptionCode = $exception -> getCode();
            if($exceptionCode != 0)
                echo '[', $exceptionCode, ']';

            echo ': ', $exception -> getMessage();

            if($this -> printStackTraces)
                echo PHP_EOL,
                     PHP_EOL,
                     (string) $exception,
                     PHP_EOL;
        }

        // EOL
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

    private function formatArray($array) {
        $assoc = ! array_is_list($array);
        $formatted = [];

        foreach($array as $k => $v) {
            if(is_string($v))
                $v = '"' . $v . '"';
            else if(is_bool($v))
                $v = $v ? 'true' : 'false';
            else if(is_scalar($v))
                $v = (string) $v;
            else if(is_null($v))
                $v = 'null';
            else if(is_array($v))
                $v = '[' . $this -> formatArray($v) . ']';
            else
                $v = gettype($v);

            $formatted[] = $assoc ? "$k: $v" : $v;
        }

        return implode(', ', $formatted);
    }
}
