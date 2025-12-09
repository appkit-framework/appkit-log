<?php

namespace AppKit\Log;

interface LogHandlerInterface {
    public function log($time, $level, $moduleName, $moduleContext, $message, $exception);
}
