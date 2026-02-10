<?php

namespace AppKit\Log\Handler;

interface LogHandlerInterface {
    public function log(
        $time,
        $level,
        $executionContext,
        $modulePath,
        $message,
        $localContext,
        $exception
    );
}
