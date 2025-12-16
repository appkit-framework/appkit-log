<?php

namespace AppKit\Log\Handler;

interface LogHandlerInterface {
    public function log($time, $level, $moduleName, $moduleContext, $message, $exception);
}
